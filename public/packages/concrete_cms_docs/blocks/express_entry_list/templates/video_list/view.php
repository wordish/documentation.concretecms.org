<?php

/**
 * @project:   ConcreteCMS Docs
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Search\Result\Item;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;

/** @var Entity $entity */
/** @var bool $enablePagination */
/** @var Result $result */
/** @var Pagination $pagination */

$app = Application::getFacadeApplication();
/** @var Date $dateService */
$dateService = $app->make(Date::class);
?>

<div class="video-list">
    <?php if ($entity) { ?>
        <?php $results = $result->getItemListObject()->getResults(); ?>

        <?php if (count($results)) { ?>
            <div class="row">
                <?php foreach ($result->getItems() as $resultItem) { ?>
                    <?php /** @var Item $resultItem */ ?>
                    <?php
                    /** @var Entry $videoObj */
                    $videoObj = $resultItem->getItem(); ?>

                    <div class="col-sm-3">
                        <div class="video">
                            <a href="https://www.youtube.com/watch?v=<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>?autoplay=1&rel=0"
                               title="<?php echo h((string)$videoObj->getAttribute("video_description")) ?>">

                                <img src="https://img.youtube.com/vi/<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>/maxresdefault.jpg"
                                     alt="<?php echo h((string)$videoObj->getAttribute("video_description")) ?>"/>
                            </a>

                            <p>
                                <a href="https://www.youtube.com/watch?v=<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>?autoplay=1&rel=0">
                                    <?php echo (string)$videoObj->getAttribute("video_name") ?>
                                </a>

                                <span class="small">
                                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                                    echo t('Posted on ' . $dateService->formatDate($videoObj->getAttribute("video_date"))) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php if ($enablePagination && $pagination) { ?>
                <div class="pagination justify-content-center">
                    <?php echo $pagination ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>
                <?php echo t("Currently there are no videos available.") ?>
            </p>
        <?php } ?>
    <?php } ?>
</div>