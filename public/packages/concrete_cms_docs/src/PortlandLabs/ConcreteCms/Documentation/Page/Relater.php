<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation\Page;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use PortlandLabs\ConcreteCms\Documentation\Entity\RelatedPage;

class Relater
{
    protected $manager;
    protected $page;

    public function __construct(
        EntityManagerInterface $manager,
        Page $page
    )
    {
        $this->manager = $manager;
        $this->page = $page;
    }

    public function clearRelations()
    {
        $q = $this->manager->createQuery("delete from
        \PortlandLabs\ConcreteCms\Documentation\Entity\RelatedPage r where r.page_id = :id");
        $q->setParameter('id', $this->page->getCollectionID());
        $q->execute();
    }

    public function determineRelations()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $tags = $this->page->getAttribute('tags');
        $questions = $this->page->getAttribute('questions_answered');

        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $stm = $db->prepare('select p.cID from Pages p inner join CollectionVersions cv on p.cID = cv.cID
inner join CollectionAttributeValues cav on cv.cID = cav.cID and cv.cvID = cav.cvID
inner join atSelectOptionsSelected ats on cav.avID = ats.avID where ats.avSelectOptionID = :optionID
and cvIsApproved = 1 and p.cID <> :cID');

        $relations = [];
        $relationsToScores = [];

        if (is_object($tags) || is_array($tags)) {
            foreach ($tags as $tag) {
                $stm->bindValue('cID', $this->page->getCollectionID());
                $stm->bindValue('optionID', $tag->getSelectAttributeOptionID());
                $stm->execute();

                while ($cID = $stm->fetchColumn()) {
                    if (isset($relationsToScores[$cID])) {
                        $relationsToScores[$cID]++;
                    } else {
                        $relationsToScores[$cID] = 1;
                    }
                }
            }
        }

        if (is_object($questions) || is_array($questions)) {
            foreach ($questions as $question) {
                $stm->bindValue('cID', $this->page->getCollectionID());
                $stm->bindValue('optionID', $question->getSelectAttributeOptionID());
                $stm->execute();
                while ($cID = $stm->fetchColumn()) {
                    if (isset($relationsToScores[$cID])) {
                        $relationsToScores[$cID] = $relationsToScores[$cID] + 10;
                    } else {
                        $relationsToScores[$cID] = 10;
                    }
                }
            }
        }

        foreach ($relationsToScores as $cID => $score) {
            $relatedPage = new RelatedPage();
            $relatedPage->setPageId($this->page->getCollectionID());
            $relatedPage->setRelatedPageId($cID);
            $relatedPage->setRelationScore($score);
            $relations[] = $relatedPage;
        }

        return $relations;
    }
}