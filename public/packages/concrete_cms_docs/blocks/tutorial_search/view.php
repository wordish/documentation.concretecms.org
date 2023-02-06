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
$selection = $selection ?? null;
$audience = $audience ?? 'all';
$placeholder = $placeholder ?? null;
?>

<div data-vue-app="tutorial-search" v-cloak>
    <tutorial-search
            :query='<?=json_encode($selection)?>'
            placeholder="<?=$placeholder?>"
            action-url="<?php echo $view->action('search') ?>"
            questions-data-source="<?=$view->action('load_questions')?>"
            audience="<?=$audience?>"
    >
        <template v-slot:content>
            <h1>
                <?php echo t("Tutorials"); ?>
            </h1>

            <p>
                <?php echo t("Spend a few minutes learning some of the great tutorials created by our staff and the community."); ?>
            </p>
        </template>
    </tutorial-search>
</div>

<script>
    $(function () {
        Concrete.Vue.activateContext('frontend', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=tutorial-search]',
                components: config.components
            });
        });
    });
</script>
