<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Block\Block;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var Block $b */
/** @var View $view */
/** @var int $placeholder */
/** @var string $audience */
/** @var stdClass $selection */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);


?>

<form action="<?php echo $view->action('search') ?>" class="tutorial-search-form"
      data-tutorial-search="<?php echo h($b->getBlockID()) ?>">

    <?php echo $form->hidden("audience", $audience); ?>
    <?php echo $form->hidden("search", isset($selection) ? $selection->id : null); ?>
    <?php echo $form->select("searchField", [], [
        "data-abs-ajax-url" => (string)$view->action('load_questions'),
        "data-abs-locale-empty-title" => (string)(is_object($selection) ? $selection->text : $placeholder)
    ]); ?>
</form>