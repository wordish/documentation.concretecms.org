<?php

/**
 * @project:   ConcreteCMS Docs
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Support\Facade\Application;

$user = new User();
$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
?>

<div id="ccm-sub-nav">
    <div class="container">
        <div class="row">
            <div class="col">
                <h3>
                    <?php echo t("Documentation"); ?>
                </h3>

                <nav>
                    <ul>
                        <?php if ($user->isRegistered()) { ?>
                            <li>
                                <a href="<?php echo (string)Url::to("/contributions");?>">
                                    <?php echo t("Your Contributions"); ?>
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo (string)Url::to('/login', 'do_logout', $token->generate('do_logout')); ?>">
                                    <?php echo t("Sign Out"); ?>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li>
                                <a href="<?php echo (string)Url::to('/login') ?>">
                                    <?php echo t("Sign In"); ?>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>