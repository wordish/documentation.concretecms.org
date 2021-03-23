<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\ConcreteCmsDocs\Block\LatestVideo;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Exception;

class Controller extends BlockController
{
    const AUDIENCE_ALL = 'all';
    const AUDIENCE_DEVELOPERS = 'Developers';
    const AUDIENCE_DESIGNERS = 'Designers';
    const AUDIENCE_EDITORS = 'Editors';

    protected $btInterfaceWidth = 380;
    protected $btInterfaceHeight = 250;
    protected $btTable = 'btLatestVideo';
    /** @var ObjectManager */
    protected $objectManager;
    /** @var string */
    protected $filteredAudienceOption;
    /** @var int */
    protected $filteredAudienceAll;
    /** @var int */
    protected $filteredAudienceDevelopers;
    /** @var int */
    protected $filteredAudienceEditors;
    /** @var int */
    protected $filteredAudienceDesigners;

    public function getBlockTypeName()
    {
        return t('Latest Video Block');
    }

    public function getBlockTypeDescription()
    {
        return t('Displays the latest video from selected audience');
    }

    public function on_start()
    {
        parent::on_start();
        $this->objectManager = $this->app->make(ObjectManager::class);
    }

    /**
     * @throws Exception
     */
    public function on_before_render()
    {
        parent::on_before_render();

        $entity = $this->objectManager->getObjectByHandle('youtubevideo');

        /** @var EntryList $list */
        $list = new EntryList($entity);

        if ($this->filteredAudienceDevelopers) {
            $list->filterByAttribute("video_audience", self::AUDIENCE_DEVELOPERS);
        } elseif ($this->filteredAudienceEditors) {
            $list->filterByAttribute("video_audience", self::AUDIENCE_EDITORS);
        } elseif ($this->filteredAudienceDesigners) {
            $list->filterByAttribute("video_audience", self::AUDIENCE_DESIGNERS);
        }

        $list->sortByDateAddedDescending();
        $list->getQueryObject()->setMaxResults(1);

        $videos = $list->getResults();

        $videoObj = $videos[0];

        $this->set('videoObj', $videoObj);
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->set('all', self::AUDIENCE_ALL);
        $this->set('editors', self::AUDIENCE_EDITORS);
        $this->set('developers', self::AUDIENCE_DEVELOPERS);
        $this->set('designers', self::AUDIENCE_DESIGNERS);
    }

    public function save($data)
    {
        $data['filteredAudienceAll'] = $data['filteredAudienceOption'] === self::AUDIENCE_ALL ? 1 : 0;
        $data['filteredAudienceDevelopers'] = $data['filteredAudienceOption'] === self::AUDIENCE_DEVELOPERS ? 1 : 0;
        $data['filteredAudienceDesigners'] = $data['filteredAudienceOption'] === self::AUDIENCE_DESIGNERS ? 1 : 0;
        $data['filteredAudienceEditors'] = $data['filteredAudienceOption'] === self::AUDIENCE_EDITORS ? 1 : 0;

        parent::save($data);
    }
}

