<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Multilingual\Service\Detector $detector */
?>

<div class="float-right" style="margin-top:-3.8rem;">
    <form method="post" class="row row-cols-auto g-0 align-items-center">
        <div class="col-auto me-3">
            <label for="switch-release-version-select"><?=t('Release Version')?></label>
        </div>
        <div class="col-auto">
            <?= $form->select(
                'release-version',
                $releaseLabels,
                $selectedRelease,
                [
                    'data-select' => 'switch-release-version',
                    'data-action' => $detector->getSwitchLink($cID, '--release--')
                ]
            ) ?>
        </div>
    </form>
</div>
<?php
if ($currentRelease !== $releaseLabels[$selectedRelease]) {
?>
<div class="alert alert-warning">
    <aside>
        <i class="fas fa-exclamation-triangle"></i>
        This documentation is not for the most recent release version of Concrete CMS.
    </aside>
</div>

<?php
}
