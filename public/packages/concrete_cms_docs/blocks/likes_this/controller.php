<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

/** @noinspection PhpUnused */
/** @noinspection PhpInconsistentReturnPointsInspection */

namespace Concrete\Package\ConcreteCmsDocs\Block\LikesThis;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;

class Controller extends BlockController
{
    protected $btTable = 'btLikesThis';
    protected $btDefaultSet = 'social';
    /** @var Connection */
    protected $db;
    /** @var Token */
    protected $token;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function getBlockTypeDescription()
    {
        return t("Allows users to say they like a page");
    }

    public function getBlockTypeName()
    {
        return t("Like this Page");
    }

    public function view()
    {
        $u = new User();
        $page = Page::getCurrentPage();

        $this->set('page', $page);
        $this->set('u', $u);
        $this->set('count', $this->getLikeCount($page->getCollectionID()));
        $this->set('userLikesThis', $this->hasMarked($page->getCollectionID()));
    }

    public function on_start()
    {
        parent::on_start();
        $this->db = $this->app->make(Connection::class);
        $this->token = $this->app->make(Token::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function action_like($token = false, $bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        if ($this->token->validate('like_page', $token)) {
            $page = Page::getCurrentPage();

            $u = new User();

            if (!$u->isRegistered()) {
                $this->responseFactory->forbidden(Page::getCurrentPage())->send();
                $this->app->shutdown();
            }

            $this->markLike($page->getCollectionID(), $page->getPageTypeID(), $u->getUserID());
        }
    }

    public function action_unlike($token = false, $bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        if ($this->token->validate('unlike_page', $token)) {
            $page = Page::getCurrentPage();
            $u = new User();

            if (!$u->isRegistered()) {
                $this->responseFactory->forbidden(Page::getCurrentPage())->send();
                $this->app->shutdown();
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $this->db->executeQuery('delete from btLikesThisUserPages where cID = ? and uID = ?', [
                $page->getCollectionID(), $u->getUserID()
            ]);

            $res = $this->getLikeCount($page->getCollectionID());
            $res--;
            $page->setAttribute('likes_this_page_count', $res);
        }
    }

    public function markLike($cID, $ctID, $uID)
    {
        if (!$this->hasMarked($cID)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $this->db->executeQuery("REPLACE INTO btLikesThisUserPages (cID, uID, ctID) VALUES (?,?,?)", [$cID, $uID, $ctID]);
            $res = $this->getLikeCount($cID);
            $res++;

            $page = Page::getByID($cID);
            $page->setAttribute('likes_this_page_count', $res);

            $lt = null;

            if ($_COOKIE['LikesThisBlockLikes']) {
                $lt = $_COOKIE['LikesThisBlockLikes'];

                if ($lt) {
                    $lt = explode(',', $lt);
                }
            }

            if (!is_array($lt)) {
                $lt = [];
            }

            $lt[$cID] = $cID;
            setcookie('LikesThisBlockLikes', implode(',', $lt), time() + 3600, DIR_REL . '/');
            $GLOBALS['LikesThisBlockLikes'] = $lt; // I know, this is BAD.
        }
    }

    protected function hasMarked($cID)
    {
        $u = new User();

        if ($u->isRegistered()) {
            $uID = $u->getUserID();
            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $hasMarked = $this->db->fetchColumn("SELECT uID FROM btLikesThisUserPages WHERE uID = ? AND cID = ?", [$uID, $cID]);
            return $hasMarked;
        } else {
            $lt = null;

            if (isset($GLOBALS['LikesThisBlockLikes'])) {
                $lt = $GLOBALS['LikesThisBlockLikes'];
            } else if ($_COOKIE['LikesThisBlockLikes']) {
                $lt = $_COOKIE['LikesThisBlockLikes'];

                if ($lt) {
                    $lt = explode(',', $lt);
                }
            }

            if (is_array($lt) && in_array($cID, $lt)) {
                return true;
            }
        }
    }

    protected function getLikeCount($cID)
    {
        return Page::getByID($cID)->getAttribute('likes_this_page_count');
    }

    protected function getAbsoluteLikeCount($cID)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        return $this->db->fetchColumn("SELECT COUNT(cID) FROM btLikesThisUserPages WHERE cID = ?", [$cID]);
    }
}
