<?php

namespace Concrete\Package\ConcreteCmsDocs\Controller;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RemoteHelp
{

    public function view()
    {
        $request = Request::createFromGlobals();

        // Porting from the old marketplace.concretecms.com because that's what the core expects.
        $pl = new PageList();
        $pl->setItemsPerPage(5);
        $pl->filterByKeywords(h($request->query->get('q')));
        $pl->sortByRelevance();
        $pages = array();
        foreach($pl->getPagination() as $c) {
            $obj = [];
            $pc = Page::getByID($c->getCollectionParentID());
            $obj['href'] = (string) $c->getCollectionLink();
            $obj['cID'] = $c->getCollectionID();
            $obj['name'] = $pc->getCollectionName() . ' > ' . $c->getCollectionName();
            $pages[] = $obj;
        }

        return new JsonResponse($pages);
    }

}