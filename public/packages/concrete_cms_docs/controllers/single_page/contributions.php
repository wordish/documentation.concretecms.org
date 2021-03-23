<?php /** @noinspection PhpUnused */

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\ConcreteCmsDocs\Controller\SinglePage;

use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;

class Contributions extends PageController
{

    public function view()
    {
        $u = new User();
        $list = new PageList();
        $list->filterByPageTypeHandle(['tutorial', 'editor_document', 'developer_document']);
        $list->sortByPublicDateDescending();
        $list->filterByUserID($u->getUserID());
        $list->setItemsPerPage(30);
        $list->ignorePermissions();
        $list->setPageVersionToRetrieve(PageList::PAGE_VERSION_RECENT);
        /** @noinspection PhpDeprecationInspection */
        $results = $list->getPagination();
        $this->set('results', $results);
    }

    public function pageIsLive(Page $page)
    {
        $d = clone $page;
        $cp = new Checker($d);

        /** @noinspection PhpUndefinedMethodInspection */
        if (!$page->isActive() && (!$cp->canViewPageVersions())) {
            return false;
        }

        $d->loadVersionObject('ACTIVE');
        $v = $d->getVersionObject();
        $vp = new Checker($v);

        if ($vp->getError() == COLLECTION_NOT_FOUND) {
            return false;
        }

        return true;
    }
}