<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var int $count */
/** @var bool $userLikesThis */
/** @var View $view */
$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="ccm-block-likes-this-wrapper">
    <p>
        <?php echo t("Was this information useful?"); ?>
    </p>

    <a href="<?php echo $view->action('unlike', $token->generate('unlike_page')) ?>" title="<?php echo h(t("No")); ?>" class="text-danger">
        <i class="far fa-thumbs-down"></i>
    </a>

    <a href="<?php echo $view->action('like', $token->generate('like_page')) ?>" title="<?php echo h(t("Yes")); ?>" class="text-success">
        <i class="far fa-thumbs-up"></i>
    </a>
</div>