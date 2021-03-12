<?php /** @noinspection PhpUnused */

namespace Concrete\Package\ConcreteCmsDocs\Job;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Job\JobQueue;
use Concrete\Core\Job\JobQueueMessage;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\ConcreteCms\Documentation\Page\Relater;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Page\Page;

class RefreshPageRelations extends QueueableJob
{
    public function getJobName()
    {
        return t("Refresh Page Relations");
    }

    public function getJobDescription()
    {
        return t("Goes through all documentation and tutorial pages and relates them to other pages by tag.");
    }

    public function start(JobQueue $q)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $documentation = Type::getByHandle('document');
        $tutorial = Type::getByHandle('tutorial');

        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $rows = $db->fetchAll('select cID from Pages where (ptID = ? or ptID = ?) and cIsTemplate = 0 and cIsActive = 1',
            [$documentation->getPageTypeID(), $tutorial->getPageTypeID()]
        );

        foreach ($rows as $row) {
            $q->send($row['cID']);
        }
    }

    /**
     * @param JobQueue $q
     * @return mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function finish(JobQueue $q)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $query = $entityManager->createQuery("SELECT COUNT(r) FROM \PortlandLabs\ConcreteCms\Documentation\Entity\RelatedPage r");
        $total = $query->getSingleScalarResult();
        return t('Index updated. %s pages related.', $total);
    }

    public function processQueueItem(JobQueueMessage $msg)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $c = Page::getByID($msg->body, 'ACTIVE');
        /** @noinspection PhpParamsInspection */
        $relater = new Relater($entityManager, $c);
        $relater->clearRelations();
        $relations = $relater->determineRelations();

        foreach ($relations as $relation) {
            $entityManager->persist($relation);
        }

        $entityManager->flush();
    }
}