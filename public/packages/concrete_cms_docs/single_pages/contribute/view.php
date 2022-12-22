<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var string $buttonTitle */
/** @var string $pageTitle */
/** @var string $action */
/** @var string $documentationType */
/** @var Type $pagetype */
/** @var Page $document */
/** @var bool $canEditDocumentAuthor */
/** @var int $documentAuthor */


$buttonTitle = $buttonTitle ?? null;
$pageTitle = $pageTitle ?? null;
$action = $action ?? null;
$documentationType = $documentationType ?? null;
$pagetype = $pagetype ?? null;
$document = $document ?? null;
$canEditDocumentAuthor = $canEditDocumentAuthor ?? null;
$documentAuthor = $documentAuthor ?? null;
$parent = $parent ?? null;


$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var UserSelector $userSelector */
$userSelector = $app->make(UserSelector::class);

?>

    <div class="page-header mb-3">
        <h1 class="page-title"><?php echo $pageTitle ?></h1>
    </div>

    <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data">
        <?php echo $form->hidden('documentation_type', $documentationType) ?>
        <?php echo $token->output('save') ?>

        <div class="ccm-composer-form">
            <?php $pagetype->renderComposerOutputForm($document, $parent); ?>
        </div>

        <?php if (is_object($document)) { ?>
            <hr/>

            <div class="form-group">
                <?php echo $form->label('versionComment', t('Reason for Changes')) ?>
                <?php echo $form->textarea('versionComment', array('rows' => 4)) ?>
            </div>

            <?php if ($canEditDocumentAuthor) { ?>
                <div class="form-group">
                    <?php echo $form->label('documentAuthor', t('Author')) ?>
                    <?php echo $userSelector->quickSelect('documentAuthor', $documentAuthor); ?>
                </div>
            <?php } ?>

        <?php } ?>

        <div class="float-right">
            <button type="submit" class="btn btn-primary">
                <?php echo $buttonTitle ?>
            </button>
        </div>
    </form>
