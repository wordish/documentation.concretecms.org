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

/** @var string $all */
/** @var string $editors */
/** @var string $developers */
/** @var string $designers */
/** @var string $filteredAudienceOption */
/** @var int $filteredAudienceAll */
/** @var int $filteredAudienceDevelopers */
/** @var int $filteredAudienceEditors */
/** @var int $filteredAudienceDesigners */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

$audienceOptions = [
    (string)$all => 'Most Recent Video',
    (string)$developers => 'Most Recent Developers Video',
    (string)$editors => 'Most Recent Editors Video',
    (string)$designers => 'Most Recent Designers Video'
];

?>

<fieldset>
    <legend>
        <?php echo t('Filter Latest Video by Audience') ?>
    </legend>

    <?php foreach ($audienceOptions as $audienceOptionValue => $audienceOptionLabel) { ?>
        <div class="form-group">
            <div class="form-check">
                <?php echo $form->radio('filteredAudienceOption', $audienceOptionValue, $filteredAudienceOption, ["class" => "form-check-input", "id" => 'option_' . $audienceOptionValue]); ?>
                <?php echo $form->label('option_' . $audienceOptionValue, $audienceOptionLabel, ["class" => "form-check-label"]); ?>
            </div>
        </div>
    <?php } ?>
</fieldset>
