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
        <div class="col-md-6">
            <h1>
                <?php echo t("Tutorials"); ?>
            </h1>

            <p>
                <?php echo t("Spend a few minutes learning some of the great tutorials created by our staff and the community."); ?>
            </p>
        </div>

        <div class="col-md-6">
            <div class="float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <?php echo $audienceLabel ?>
                    <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item"
                       href="<?php echo $controller->getSearchURL($view, 'audience', null) ?>">
                        <?php echo t("All"); ?>
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item"
                       href="<?php echo $controller->getSearchURL($view, 'audience', 'editors') ?>">
                        <?php echo t("Editors"); ?>
                    </a>

                    <a class="dropdown-item"
                       href="<?php echo $controller->getSearchURL($view, 'audience', 'designers') ?>">
                        <?php echo t("Designers"); ?>
                    </a>

                    <a class="dropdown-item"
                       href="<?php echo $controller->getSearchURL($view, 'audience', 'developers') ?>">
                        <?php echo t("Developers"); ?>
                    </a>
                </div>

                <a href="<?php echo (string)Url::to('/contribute', 'choose_type', 'tutorial') ?>"
                   class="btn btn-primary">
                    <?php echo t("Write Tutorial"); ?>
                </a>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="nav-item <?php if ($sort == 'newest') { ?> active<?php } ?>">
                    <a class="nav-link" href="<?php echo $controller->getSearchURL($view, 'sort', 'newest') ?>">
                        <?php echo t("Newest"); ?>
                    </a>
                </li>

                <li class="nav-item <?php if ($sort == 'popular') { ?> active<?php } ?>">
                    <a class="nav-link" href="<?php echo $controller->getSearchURL($view, 'sort', 'popular') ?>">
                        <?php echo t("Popular"); ?>
                    </a>
                </li>

                <li class="nav-item <?php if ($sort == 'trending') { ?> active<?php } ?>">
                    <a class="nav-link" href="<?php echo $controller->getSearchURL($view, 'sort', 'trending') ?>">
                        <?php echo t("Trending"); ?>
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

                        if ($ui instanceof UserInfo) {
                            $username = $ui->getUserDisplayName();
                        }

                        $inspector = new PageInspector($result);
                        ?>

                        <tr class="tutorial">
                            <td>
                                <a href="<?php echo $result->getCollectionLink() ?>" class="page-name">
                                    <?php echo $result->getCollectionName() ?>
                                </a>

                                <p class="page-author">
                                    <?php echo t("By %s.", sprintf("<a href=\"#\">%s</a>", $username)); ?>
                                </p>
                            </td>

                            <td>
                                <div class="float-right d-none d-sm-block">
                                    <div class="timestamp">
                                        <?php echo $dateService->describeInterval(
                                            time() - $result->getCollectionDatePublicObject()->getTimestamp()
                                        ) ?>
                                    </div>

                                    <!--
                                    <div class="total-comments">
                                        <i class="far fa-comment"></i> <?php echo $inspector->getTotalComments() ?>
                                    </div>

                                    <div class="total-likes">
                                        <i class="far fa-heart"></i> <?php echo $inspector->getTotalLikes() ?>
                                    </div>
                                    -->
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
