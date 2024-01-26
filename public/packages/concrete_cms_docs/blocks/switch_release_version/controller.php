<?php

namespace Concrete\Package\ConcreteCmsDocs\Block\SwitchReleaseVersion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Exception;

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 340;
    protected $btTable = 'btSwitchReleaseVersion';
    protected $mapping = [
        '9.x' => 'grc_TR',
        '8.x' => 'en_US'
    ];
    protected $currentRelease = '9.x';

    public function getBlockTypeName()
    {
        return t('Switch Release Version');
    }

    public function getBlockTypeDescription()
    {
        return t("Displays a list of release versions to select appropriate documentation.");
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::MULTILINGUAL
        ];
    }

    public function view ()
    {
        $c = Page::getCurrentPage();
        /** @var Detector $detector */
        $detector = $this->app->make('multilingual/detector');
        $releaseLabels = [];
        $releases = $detector->getAvailableSections();
        $releasesAccessible = [];
        $selectedRelease = null;
        $dd = [];
        foreach ($releases as $release) {
            // check permissions
            $pc = new Checker(Page::getByID($release->getCollectionID()));
            if ($pc->canRead()) {
                $releasesAccessible[] = $release;
                $releaseLabel = array_flip($this->mapping)[$release->getLocale()];
                $releaseID = $release->getCollectionID();
                $releaseLabels[$releaseID] = $releaseLabel;
            }
        }
        $selectedRelease = $detector->getActiveSection($c)->getCollectionID();
        $this->set('selectedRelease', $selectedRelease);
        $this->set('releaseLabels', $releaseLabels);
        $this->set('releaseSelections', $releasesAccessible);
        $this->set('cID', $c->getCollectionID());
        $this->set('detector', $detector);
        $this->set('currentRelease', $this->currentRelease);
    }
    
    public function action_switch_release_version ($currentPageID, $targetReleaseVersion) {
        /** @var ResponseFactoryInterface $factory */
        $factory = $this->app->make(ResponseFactoryInterface::class);
        return $factory->redirect($translatedPage->getCollectionLink(), Response::HTTP_FOUND);
    }

}
