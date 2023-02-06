<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use PortlandLabs\ConcreteCms\Documentation\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\View\View;
use Concrete\Package\ConcreteCmsDocs\Block\TutorialList\Controller;
use PortlandLabs\ConcreteCms\Documentation\Page\PageInspector;

/** @var View $view */
/** @var Controller $controller */
/** @var Pagination|Page[] $results */
/** @var string $audience */
/** @var string $sort */
/** @var string $audienceLabel */

$app = Application::getFacadeApplication();
/** @var Date $dateService */
$dateService = $app->make(Date::class);
/** @var UserInfoRepository $userInfoRepository */
$userInfoRepository = $app->make(UserInfoRepository::class);
?>

<div class="tutorials">
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?php if ($sort == 'newest') { ?> active<?php } ?>" href="<?php echo $controller->getSearchURL($view, 'sort', 'newest') ?>">
                        <?php echo t("Newest"); ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if ($sort == 'popular') { ?> active<?php } ?>" href="<?php echo $controller->getSearchURL($view, 'sort', 'popular') ?>">
                        <?php echo t("Popular"); ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if ($sort == 'trending') { ?> active<?php } ?>" href="<?php echo $controller->getSearchURL($view, 'sort', 'trending') ?>">
                        <?php echo t("Trending"); ?>
                    </a>
                </li>

                <li class="nav-item ms-auto d-flex">
                    <a href="<?php echo (string)Url::to('/contribute', 'choose_type', 'tutorial') ?>"
                       class="align-self-end mb-1 btn btn-primary btn-sm text-white fw-bold">
                        <?php echo t("Write Tutorial"); ?>
                    </a>

                </li>
            </ul>


            <?php if (count($results) === 0) { ?>
                <p style="margin-top: 15px">
                    <?php echo t("Currently there are no tutorials available."); ?>
                </p>
            <?php } else { ?>
                <table class="table">
                    <tbody>
                    <?php foreach ($results as $result) {
                        $username = t('Unknown');

                        $ui = $userInfoRepository->getByID($result->getCollectionUserID());

                        $profileUrl = null;

                        if ($ui instanceof UserInfo) {
                            $username = $ui->getUserDisplayName();
                            $profileUrl = $ui->getUserPublicProfileUrl();
                        }

                        $inspector = new PageInspector($result);
                        ?>

                        <tr class="tutorial">
                            <td>
                                <a href="<?php echo $result->getCollectionLink() ?>" class="page-name">
                                    <?php echo $result->getCollectionName() ?>
                                </a>

                                <p class="page-author">
                                    <?php
                                    if ($profileUrl) {
                                    ?>
                                        <?php echo t("By %s.", sprintf("<a href=\"%s\">%s</a>", $profileUrl, $username)); ?>
                                    <?php } else { ?>
                                        <?php echo t("By %s.", $username); ?>
                                    <?php } ?>
                                </p>
                            </td>

                            <td>
                                <div class="float-end d-none d-sm-block">
                                    <div class="timestamp">
                                        <?php
                                        $date = \Carbon\Carbon::createFromTimestamp($result->getCollectionDatePublicObject()->getTimestamp());
                                        echo $date->diffForHumans(short: true, parts: 2);
                                        ?>
                                    </div>

                                    <?php /*
                                    <div class="total-comments">
                                        <i class="far fa-comment"></i> <?php echo $inspector->getTotalComments() ?>
                                    </div>

                                    <div class="total-likes">
                                        <i class="far fa-heart"></i> <?php echo $inspector->getTotalLikes() ?>
                                    </div>
                                    */ ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="mx-auto">
                    <?php if ($results->haveToPaginate()) { ?>
                        <?php echo $results->renderDefaultView() ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
