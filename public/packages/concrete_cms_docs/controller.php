<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\ConcreteCmsDocs;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Express\EntryBuilder;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\GroupRepository;
use PortlandLabs\ConcreteCms\Documentation\Migration\Publisher\Block\MarkdownPublisher;
use PortlandLabs\ConcreteCms\Documentation\ServiceProvider;
use Exception;
use DateTime;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'concrete_cms_docs';
    protected $appVersionRequired = '9.0';
    protected $pkgVersion = '1.2.2';
    protected $pkgAllowsFullContentSwap = true;
    protected $pkgAutoloaderRegistries = [
        'src/PortlandLabs/ConcreteCms/Documentation' => 'PortlandLabs\ConcreteCms\Documentation'
    ];

    public function getPackageDescription()
    {
        return t("Documentation site.");
    }

    public function getPackageName()
    {
        return t("Documentation site");
    }

    public function getEntityManagerProvider()
    {
        return new StandardPackageProvider($this->app, $this, [
            'src/PortlandLabs/ConcreteCms/Documentation/Entity' => 'PortlandLabs\ConcreteCms\Documentation\Entity'
        ]);
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function on_after_swap_content()
    {
        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        $pkgEntity = $packageService->getByHandle("concrete_cms_theme");

        if ($pkgEntity instanceof PackageEntity) {
            /** @var \Concrete\Package\ConcreteCmsTheme\Controller $pkg */
            $pkg = $pkgEntity->getController();
            $pkg->on_after_swap_content();
        }

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $site = $this->app->make('site')->getActiveSiteForEditing();
        $siteConfig = $site->getConfigRepository();

        $siteConfig->save("concrete_cms_theme.enable_dark_mode", true);

        // Allow registered users to access the sitemap (required to display the page selector in the composer form)
        $pk = Key::getByHandle('access_sitemap');
        $pa = $pk->getPermissionAccessObject();

        /** @var GroupRepository $groupRepository */
        $groupRepository = $this->app->make(GroupRepository::class);
        $g = $groupRepository->getGroupByID(REGISTERED_GROUP_ID);
        $guestsGroup = $groupRepository->getGroupByID(GUEST_GROUP_ID);

        if (is_object($g)) {
            $pae = GroupEntity::getOrCreate($g);
            $pa->addListItem($pae, false, Key::ACCESS_TYPE_INCLUDE);

            // Allow registered users to add documentation pages (not possible to set this through CIF file format - core bug?)
            foreach (["tutorial", "document"] as $ptHandle) {
                $pt = Type::getByHandle($ptHandle);
                $pk = Key::getByHandle('add_in_documentation_composer');
                $pk->setPermissionObject($pt);
                $pa = $pk->getPermissionAccessObject();
                $pae = GroupEntity::getOrCreate($g);
                $pa->addListItem($pae, false, Key::ACCESS_TYPE_INCLUDE);
            }

            // Enable Advanced Permissions
            $config->save('concrete.permissions.model', 'advanced');

            // Extra: Allow view sitemap for root page when advanced permissions are turned on (required to add/edit documentation pages)
            $rootPage = Page::getByID(Page::getHomePageID());

            if ($rootPage instanceof Page && !$rootPage->isError()) {
                $rootPage->assignPermissions($g, ["view_page_in_sitemap"]);
            }
        }

        // Apply permissions to demo content
        foreach (["developer_document", "editor_document", "document"] as $ptHandle) {
            $pageList = new PageList();
            $pageList->filterByPageTypeHandle($ptHandle);
            $allPages = $pageList->getResults();
            foreach ($allPages as $curPage) {
                /** @var Page $curPage */

                if (is_object($g)) {
                    $curPage->assignPermissions($g, ["edit_in_documentation_composer"]);
                }

                if (is_object($guestsGroup)) {
                    $curPage->assignPermissions($guestsGroup, ["view_page"]);
                }
            }
        }

        // Finally let's add a sample video (need to to this programmatically because this was also not working by CIF)
        /** @var ObjectManager $objectManager */
        $objectManager = $this->app->make(ObjectManager::class);
        /** @var EntryBuilder $builder */
        $builder = $objectManager->buildEntry("youtubevideo");
        $builder->setAttribute("video_name", t("Connecting to the Concrete REST API"));
        $builder->setAttribute("youtube_id", "p8ySVFeCgv0");
        $builder->setAttribute("video_date", new DateTime());
        $builder->setAttribute("video_description", t("Connecting to the Concrete REST API"));
        $builder->save();
    }

    /** @noinspection PhpUnused */
    public function on_after_packages_start()
    {
        try {
            $app = Application::getFacadeApplication();
            $manager = $app->make('migration/manager/publisher/block');
            $manager->extend('markdown', function () {
                return new MarkdownPublisher();
            });
        } catch (Exception $e) {
        }
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile('update/data.xml');
    }

}
