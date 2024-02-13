<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\View\View;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var View $view */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<div class="page-header mb-3">
    <h1 class="page-title">
        <?php echo t('Write Documentation') ?>
    </h1>
</div>

<form action="<?php echo $view->action('choose_type') ?>" method="post">
    <p>
        <?php echo t("Choose the type of documentation you'd like to write.") ?>
    </p>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->radio('documentation_type', 'tutorial', true, ["class" => "form-check-input", "id" => 'documentation_type_tutorial']); ?>
            <?php echo $form->label('documentation_type_tutorial', t("Tutorial"), ["class" => "form-check-label"]); ?>

            <p class="text-muted">
                <?php echo t('Tutorials are targeted, self-contained how-to documents that tackle a particular topic. They can be geared toward site editors, designers or developers. Tutorials should be about a specific topic, and hopefully answer a question about how to accomplish a task with Concrete CMS.') ?>
            </p>
        </div>
    </div>

    <div class="float-end">
        <button type="submit" class="btn btn-primary">
            <?php echo t('Next') ?>
        </button>
    </div>
</form>
