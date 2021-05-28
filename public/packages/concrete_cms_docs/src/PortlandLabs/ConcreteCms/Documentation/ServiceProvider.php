<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Package;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Collection\Version\Event;
use Concrete\Core\Page\DeletePageEvent;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\Router;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\View\View;
use Concrete\Package\ConcreteCmsDocs\Controller;
use Doctrine\ORM\EntityManagerInterface;
use PortlandLabs\ConcreteCms\Documentation\CommunityApiClient\Models\Achievements;
use PortlandLabs\ConcreteCms\Documentation\Page\Relater;
use PortlandLabs\ConcreteCms\Documentation\User\Avatar\AvatarService;
use Exception;
use PortlandLabs\ConcreteCmsTheme\Navigation\HeaderNavigationFactory;

class ServiceProvider extends Provider
{
    public function register()
    {
        $al = AssetList::getInstance();
        $app = Application::getFacadeApplication();
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $app->make(EventDispatcher::class);
        /** @var PackageService $packageService */
        $packageService = $app->make(PackageService::class);
        /** @var Router $router */
        $router = $app->make(Router::class);

        /** @var Package $pkgEntity */
        $pkgEntity = $packageService->getByHandle("concrete_cms_docs");
        /** @var Controller $pkg */
        $pkg = $pkgEntity->getController();

        $al->register(
            'css', 'concrete-cms-docs', 'css/concrete-cms-docs.css',
            ['minify' => false, 'combine' => false], $pkg
        );

        $al->register(
            'javascript', 'concrete-cms-docs', 'js/concrete-cms-docs.js',
            ['minify' => false, 'combine' => false], $pkg
        );

        $al->registerGroup('concrete-cms-docs', [
            ['javascript', 'concrete-cms-docs'],
            ['css', 'concrete-cms-docs']
        ]);

        $eventDispatcher->addListener('on_page_version_approve', function ($event) use ($app) {
            /** @var Event $event */
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $app->make(EntityManagerInterface::class);

            $page = $event->getPageObject();
            if ($page->getPageTypeHandle() == 'document' || $page->getPageTypeHandle() == 'tutorial') {
                $relater = new Relater($entityManager, $page);
                $relater->clearRelations();

                foreach ($relater->determineRelations() as $relation) {
                    $entityManager->persist($relation);
                }

                $entityManager->flush();

                // Assign badge through API
                /** @var Repository $config */
                $config = $app->make(Repository::class);
                /** @var Achievements $achievements */
                $achievements = $app->make(Achievements::class, [
                    "user" => User::getByUserID($page->getVersionObject()->getVersionAuthorUserID()) // override the user object with the author of the current page version
                ]);

                if ($page->getPageTypeHandle() == 'document') {
                    $achievementHandle = "new_documentation";
                } else {
                    $achievementHandle = "new_tutorial";
                }

                $achievements->assign($achievementHandle);
            }
        });

        // force to use the community login if community login is enabled
        /*
        $u = new User();
        try {
            $externalAuthType = AuthenticationType::getByHandle("external_concrete5");

            if ($externalAuthType->isEnabled() && !$u->isRegistered()) {
                $router->all("/login", function () use ($app) {
                    $responseFactory = $app->make(ResponseFactory::class);
                    $responseFactory->redirect((string)Url::to("/ccm/system/authentication/oauth2/external_concrete5/attempt_auth"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                    $app->shutdown();
                });
            }
        } catch (Exception $e) {
            // SKip any issues
        }
        */

        $eventDispatcher->addListener('on_page_delete', function ($event) use ($app) {
            /** @var DeletePageEvent $event */
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $app->make(EntityManagerInterface::class);

            $page = $event->getPageObject();

            if ($page->getPageTypeHandle() == 'document' || $page->getPageTypeHandle() == 'tutorial') {
                $relater = new Relater($entityManager, $page);
                $relater->clearRelations();
            }
        });

        $app->bind(UserInfo::class, \PortlandLabs\ConcreteCms\Documentation\User\UserInfo::class);

        /** @noinspection PhpDeprecationInspection */
        $app->bindShared('user/avatar', function () use ($app) {
            return $app->make(AvatarService::class);
        });

        $eventDispatcher->addListener('on_before_render', function () {
            View::getInstance()->requireAsset("concrete-cms-docs");
            $c = Page::getCurrentPage();
            if (is_object($c) && !$c->isError()) {
                if ($c->getAttribute('replace_link_with_first_in_nav')) {
                    $child = $c->getFirstChild();
                    if (is_object($child) && !$child->isError()) {
                        /** @noinspection PhpParamsInspection */
                        Redirect::page($child)->send();
                        exit;
                    }
                }
            }
        });

        // header navigation
        $elementManager = $this->app->make(ElementManager::class);
        $elementManager->register('sub_nav_custom', new Element('sub_nav_custom', 'concrete_cms_docs'));

        $this->app->make('director')->addListener('on_before_render', function($event) {
            // must be done in an event because it must come AFTER the concrete cms package registers the
            // header navigation factory class as a singleton.
            $headerNavigationFactory = app(HeaderNavigationFactory::class);
            $headerNavigationFactory->setActiveSection(HeaderNavigationFactory::SECTION_SUPPORT);
        });

    }
}