<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation\Tutorial;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Page\PageList;

class TutorialList extends PageList
{
    public function __construct()
    {
        parent::__construct();
        $this->filterByPageTypeHandle('tutorial');
        $this->ignorePermissions();
        $this->sortByPublicDateDescending();
    }

    public function sortByTrending()
    {
        $this->query->addSelect('(select count(btl.cID) from btLikesThisUserPages btl where btl.cID = p.cID
            and TIMESTAMPDIFF(DAY,lastTimeMarked,CURDATE()) <= 14) as likes');
        $this->query->orderBy('likes', 'desc');
    }

    public function sortByPopularityDescending()
    {
        $this->query->orderBy('ak_likes_this_page_count', 'desc');
    }

    /**
     * @param string $join
     * @param SelectValueOption $option
     */
    public function filterBySelectOption($join, $option)
    {
        $this->query->leftJoin('cv', 'CollectionAttributeValues', $join,
            'cv.cID = ' . $join . '.cID and cv.cvID = ' . $join . '.cvID');
        $this->query->leftJoin($join, 'atSelectOptionsSelected', $join . 'Options', $join . '.avID = ' . $join . 'Options.avID');
        $this->query->andWhere($join . 'Options.avSelectOptionID = :avSelectOptionID');
        $this->query->setParameter('avSelectOptionID', $option->getSelectAttributeOptionID());
    }

}
