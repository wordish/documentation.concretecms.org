<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;

/** @var Entry $videoObj */
/** @var string $filteredAudienceOption */
/** @var int $filteredAudienceAll */
/** @var int $filteredAudienceDevelopers */
/** @var int $filteredAudienceEditors */
/** @var int $filteredAudienceDesigners */

$app = Application::getFacadeApplication();
/** @var Date $dateService */
$dateService = $app->make(Date::class);
?>

<?php if ($videoObj instanceof Entry) { ?>
    <div class="latest-video">
        <a href="https://www.youtube.com/watch?v=<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>?autoplay=1&rel=0"
           title="<?php echo h((string)$videoObj->getAttribute("video_description")) ?>">

            <img src="https://img.youtube.com/vi/<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>/maxresdefault.jpg"
                 alt="<?php echo h((string)$videoObj->getAttribute("video_description")) ?>"/>
        </a>

        <h4>
            <a href="https://www.youtube.com/watch?v=<?php echo h((string)$videoObj->getAttribute("youtube_id")) ?>?autoplay=1&rel=0">
                <?php echo (string)$videoObj->getAttribute("video_name") ?>
            </a>

            <span class="small">
                <?php /** @noinspection PhpUnhandledExceptionInspection */
                echo t('Posted on ' . $dateService->formatDate($videoObj->getAttribute("video_date"))) ?>
            </span>
        </h4>
    </div>
<?php } ?>