<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation\Page;

use Concrete\Block\CoreConversation\Controller;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class PageInspector
{
    protected $features;
    protected $page;
    protected $conversation;

    public function canEditInDocumentationComposer()
    {
        $p = new Checker($this->page);
        /** @noinspection PhpUndefinedMethodInspection */
        if ($p->canEditInDocumentationComposer()) {
            $blocks = $this->page->getBlocks('Main');

            if (count($blocks) > 0) {
                return true;
            }
        }
        return false;
    }

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getConversationObject()
    {
        if (!isset($this->conversation)) {
            $blocks = $this->page->getBlocks('Main');

            foreach($blocks as $block) {
                $controller = $block->getController();
                if ($controller instanceof Controller) {
                    $this->conversation = $controller->getConversationObject();
                }
            }
        }

        return $this->conversation;
    }

    public function getTotalComments()
    {
        $conversation = $this->getConversationObject();
        if (is_object($conversation)) {
            return (int)$conversation->getConversationMessagesTotal();
        }
        return 0;
    }


    public function getTotalLikes()
    {
        return (int)$this->page->getAttribute('likes_this_page_count');
    }
}