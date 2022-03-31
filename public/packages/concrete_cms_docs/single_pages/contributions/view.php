<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\ConcreteCmsDocs\Controller\SinglePage\Contributions;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Localization\Service\Date;

/** @var Contributions $controller */
/** @var Pagination|Page[] $results */

$app = Application::getFacadeApplication();
/** @var Date $dateService */
$dateService = $app->make(Date::class);
?>

<?php if (count($results) > 0) { ?>

    <div class="page-header mb-5">
        <div class="float-right">
            <a href="<?php echo (string)Url::to('/contribute') ?>" class="btn btn-primary">
                <?php echo t('Write Documentation') ?>
            </a>
        </div>
        <h1 class="page-title"><?php echo t('Contributions') ?></h1>
    </div>


    <table class="table">
        <thead>
        <tr>
            <th>
                <?php echo t('Date Posted') ?>
            </th>

            <th>
                <?php echo t('Page') ?>
            </th>

            <th>
                <?php echo t('Type') ?>
            </th>

            <th>
                <?php echo t('Status') ?>
            </th>

            <th>
                &nbsp;
            </th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($results as $page) { ?>
            <tr>
                <td class="contribution-date">
                <span class="text-muted">
                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                    echo $dateService->formatDate($page->getCollectionDatePublicObject()) ?>
                </span>
                </td>

                <td class="contribution-name">
                    <?php if ($controller->pageIsLive($page)) { ?>
                        <a href="<?php echo (string)Url::to($page) ?>">
                            <?php echo $page->getCollectionName() ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $page->getCollectionName() ?>
                    <?php } ?>
                </td>

                <td>
                    <?php print $page->getPageTypeObject()->getPageTypeName();
                    ?>
                </td>

                <td class="contribution-status">
                    <?php if ($controller->pageIsLive($page)) { ?>
                        <?php echo t("Live"); ?>
                    <?php } else { ?>
                        <span class="text-danger">
                        <?php echo t("Not Live"); ?>
                    </span>
                    <?php } ?>
                </td>

                <td>
                    <div class="float-right">
                        <a class="btn btn-secondary btn-sm"
                           href="<?php echo (string)Url::to('/contribute', 'edit', $page->getCollectionID()) ?>">
                            <?php echo t("Edit"); ?>
                        </a>
                    </div>

                    <div class="clearfix"></div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if ($results->haveToPaginate()) { ?>
        <div class="pagination justify-content-center">
            <?php print $results->renderDefaultView(); ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="mt-5 mb-5">
        <p class="lead text-center">
            <?php echo t('You have not contributed any documentation to Concrete CMS.') ?>
            <br><br>
            <a href="<?php echo (string)Url::to('/contribute') ?>" class="btn btn-large">
                <?php echo t('Write Documentation') ?>
            </a>
        </p>

    </div>
<?php } ?>