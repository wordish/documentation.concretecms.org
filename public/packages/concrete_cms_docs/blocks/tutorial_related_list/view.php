<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use PortlandLabs\ConcreteCms\Documentation\User\UserInfo;
use PortlandLabs\ConcreteCms\Documentation\Page\PageInspector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Localization\Service\Date;

/** @var Pagination|Page[] $results */
/** @var string $title */
/** @var int $maxNumber */
/** @var string $sortByOptions */
/** @var int $sortByTrending */
/** @var int $sortByLikes */
/** @var int $sortByPostedDate */
/** @var string $filteredAudienceOption */
/** @var int $filteredAudienceAll */
/** @var int $filteredAudienceDevelopers */
/** @var int $filteredAudienceEditors */
/** @var int $filteredAudienceDesigners */
/** @var int $filteredByTags */

$app = Application::getFacadeApplication();
/** @var UserInfoRepository $userInfoRepository */
$userInfoRepository = $app->make(UserInfoRepository::class);
/** @var Date $dh */
$dh = $app->make(Date::class);

?>

<div class="recent-tutorials">
    <h5>
        <?php echo $title ?>
    </h5>

    <?php if ($results) { ?>
        <?php foreach ($results as $result) { ?>
            <?php
            $username = t('Unknown');

            $ui = $userInfoRepository->getByID($result->getCollectionUserID());

            $profileUrl = null;

            if ($ui instanceof UserInfo) {
                $username = $ui->getUserDisplayName();
                $profileUrl = $ui->getUserPublicProfileUrl();
            }

            $inspector = new PageInspector($result);
            ?>

            <div class="recent-tutorial">
                <a href="<?php echo $result->getCollectionLink() ?>" class="recent-tutorial-page-name">
                    <?php echo $result->getCollectionName() ?>
                </a>

                <div class="recent-tutorial-date">
                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                    echo $dh->formatDate($result->getCollectionDatePublic(), true);?>
                </div>

                <div class="recent-tutorial-author">
                    <?php
                    if ($profileUrl) {
                        ?>
                        <?php echo t("By %s.", sprintf("<a href=\"%s\">%s</a>", $profileUrl, $username)); ?>
                    <?php } else { ?>
                        <?php echo t("By %s.", $username); ?>
                    <?php } ?>
                </div>

                <div class="clearfix"></div>

                <p class="recent-tutorial-description">
                    <?php echo (string)$result->getCollectionDescription(); ?>
                </p>

                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
