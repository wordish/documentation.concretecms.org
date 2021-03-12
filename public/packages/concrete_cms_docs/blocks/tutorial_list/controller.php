<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

/** @noinspection PhpUnused */

namespace Concrete\Package\ConcreteCmsDocs\Block\TutorialList;

use Concrete\Attribute\Select\Controller as SelectController;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Url\Url;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\View\View;
use PortlandLabs\ConcreteCms\Documentation\Tutorial\TutorialList;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    /** @var TutorialList */
    protected $list;
    /** @var Numbers */
    protected $numberValidator;
    /** @var SelectController */
    protected $selectController;

    public function getBlockTypeName()
    {
        return t('Tutorial List');
    }

    public function on_start()
    {
        parent::on_start();

        $this->list = new TutorialList();
        $this->numberValidator = $this->app->make(Numbers::class);
        $this->selectController = $this->app->make(SelectController::class);
    }

    public function on_before_render()
    {
        parent::on_before_render();

        /** @noinspection PhpDeprecationInspection */
        $results = $this->list->getPagination();
        $sort = 'newest';
        $audience = null;

        if (in_array($this->request->query->get('audience'), ['developers', 'designers', 'editors'])) {
            $audience = $this->request->query->get('audience');
            $audienceLabel = ucfirst($audience);
        } else {
            $audienceLabel = t('Audience');
        }

        if (in_array($this->request->query->get('sort'), ['newest', 'popular', 'trending'])) {
            $sort = $this->request->query->get('sort');
        }

        $this->set('results', $results);
        $this->set('audience', $audience);
        $this->set('sort', $sort);
        $this->set('audienceLabel', $audienceLabel);
    }

    public function action_search()
    {
        if ($this->request->query->has('search')) {
            $kw = $this->request->query->get('search');

            if ($this->numberValidator->integer($kw)) {
                $option = $this->selectController->getOptionByID($kw);

                if ($option instanceof SelectValueOption) {
                    $this->list->filterBySelectOption('questionOrTag', $option);
                }
            }

            if (!isset($option) || !is_object($option)) {
                $this->list->filterByKeywords($kw);
            }
        }

        if ($this->request->query->has('audience')) {
            $audience = $this->request->query->get('audience');

            $option = $this->selectController->getOptionByValue($audience, CollectionKey::getByHandle('audience'));

            if ($option instanceof SelectValueOption) {
                $this->list->filterBySelectOption('audience', $option);
            }
        }

        if ($this->request->query->has('sort')) {
            $sort = $this->request->query->get('sort');

            switch ($sort) {
                case 'trending':
                    $this->list->sortByTrending();
                    break;

                case 'popular':
                    $this->list->sortByPopularityDescending();
                    break;

                default:
                    $this->list->sortByPublicDateDescending();
                    break;
            }
        }


    }

    /**
     * @param View $view
     * @param string $variable
     * @param string $value
     * @return mixed
     */
    public function getSearchURL($view, $variable, $value)
    {
        /** @var Url $url */
        $url = $view->action('search');
        $url = $url->setQuery(Url::createFromServer($this->request->server->all())->getQuery());
        $query = $url->getQuery();
        $query[$variable] = $value;
        return $url->setQuery($query);
    }
}
