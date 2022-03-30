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

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->radio('documentation_type', 'editor_documentation', false, ["class" => "form-check-input", "id" => 'documentation_type_editor_documentation']); ?>
            <?php echo $form->label('documentation_type_editor_documentation', t("Editor Documentation"), ["class" => "form-check-label"]); ?>

            <p class="text-muted">
                <?php echo t('Editor documentation describes how to use a particular feature of the Concrete user interface. Good editor documentation should be targeted at someone using Concrete to edit a website.') ?>
            </p>
        </div>
    </div>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->radio('documentation_type', 'developer_documentation', false, ["class" => "form-check-input", "id" => 'documentation_type_developer_documentation']); ?>
            <?php echo $form->label('documentation_type_developer_documentation', t("Developer Documentation"), ["class" => "form-check-label"]); ?>

            <p class="text-muted">
                <?php echo t('Developer documentation pages should be broader than tutorials, and appear at the proper point in the table of contents. They should target someone configuring, extending or customizing Concrete.') ?>
            </p>
        </div>
    </div>

    <div class="float-right">
        <button type="submit" class="btn btn-primary">
            <?php echo t('Next') ?>
        </button>
    </div>
</form>
