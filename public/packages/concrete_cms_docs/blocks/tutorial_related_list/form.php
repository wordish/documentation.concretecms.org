<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

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
/** @var string $all */
/** @var string $editors */
/** @var string $developers */
/** @var string $designers */
/** @var string $newest */
/** @var string $trend */
/** @var string $likes */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<fieldset>
    <legend>
        <?php echo t("General"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("title", t('Block Title')); ?>
        <?php echo $form->text("title", $title ? $title : t('Tutorials')); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("maxNumber", t('Max Results')); ?>
        <?php echo $form->number("maxNumber", $maxNumber, ["min" => 0, "step" => 1]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("filteredByTags", t('Filtered by Tags')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('filteredByTags', 1, $filteredByTags, ["class" => "form-check-input"]); ?>
            <?php echo $form->label('filteredByTags', t("Filter by Tags on current page"), ["class" => "form-check-label"]); ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Filtered by Audience (optional)') ?>
    </legend>

    <?php
    $audienceOptions = [
        (string)$all => t('Show All'),
        (string)$developers => t('Developers'),
        (string)$editors => t('Editors'),
        (string)$designers => t('Designers')
    ];

    foreach ($audienceOptions as $audienceOptionValue => $audienceOptionLabel) {
        $isChecked = '';

        if ($filteredAudienceOption) {
            $isChecked = ($filteredAudienceOption === $audienceOptionValue);
        } else {
            $isChecked = ($audienceOptionValue === $all);
        }

        ?>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->radio('filteredAudienceOption', $audienceOptionValue, $isChecked, ["class" => "form-check-input", "id" => 'option_' . $audienceOptionValue]); ?>
                <?php echo $form->label('option_' . $audienceOptionValue, $audienceOptionLabel, ["class" => "form-check-label"]); ?>
            </div>
        </div>
    <?php } ?>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Sort By"); ?>
    </legend>

    <?php
    $sortOptions = [
        (string)$newest => t('By Published Date'),
        (string)$trend => t('By Trending'),
        (string)$likes => t('By Likes')
    ];

    foreach ($sortOptions as $optionValue => $optionLabel) {
        $isChecked = '';

        if ($sortByOptions) {
            $isChecked = ($sortByOptions === $optionValue);
        } else {
            $isChecked = ($optionValue === $newest);
        }
        ?>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->radio('sortByOptions', $optionValue, $isChecked, ["class" => "form-check-input", "id" => 'option_' . $optionValue]); ?>
                <?php echo $form->label('option_' . $optionValue, $optionLabel, ["class" => "form-check-label"]); ?>
            </div>
        </div>
    <?php } ?>
</fieldset>