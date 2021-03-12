<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\ConcreteCmsDocs\Block\TutorialSearch;

use Concrete\Attribute\Select\Controller as SelectController;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use stdClass;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    /** @var Numbers */
    protected $numberValidator;
    /** @var SelectController */
    protected $selectController;
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function getBlockTypeName()
    {
        return t('Tutorial Search');
    }

    public function on_start()
    {
        parent::on_start();
        $this->numberValidator = $this->app->make(Numbers::class);
        $this->selectController = $this->app->make(SelectController::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    public function view()
    {
        $placeholders = [
            t('How do you log emails?'),
            t('How do you build a theme?'),
            t('How can I customize my theme\'s colors?'),
            t('How can I add a block type?'),
            t('How can I control who can see my site?'),
            t('Can I lock down certain parts of my site?'),
            t('How can I extend the functionality of my concreteCMS site?'),
            t('Can I blog with concreteCMS?'),
            t('Where can I install concreteCMS?')
        ];

        shuffle($placeholders);

        $placeholder = $placeholders[0];

        $this->set('placeholder', $placeholder);
        $this->requireAsset('javascript', 'underscore');

        if ($this->request->query->has('search')) {
            $o = null;

            $kw = $this->request->query->get('search');

            if ($this->numberValidator->integer($kw)) {
                $option = $this->selectController->getOptionByID($kw);

                if ($option instanceof SelectValueOption) {
                    $key = null;

                    $list = $option->getOptionList();

                    if (is_object($list)) {
                        $settings = $this->entityManager
                            ->getRepository(SelectSettings::class)
                            ->findOneBy(["list" => $list]);

                        if ($settings instanceof SelectSettings) {
                            $key = $settings->getAttributeKey();
                        }
                    }

                    if ($key instanceof Key) {
                        switch ($key->getAttributeKeyHandle()) {
                            case 'questions_answered':
                                $o = new stdClass;
                                $o->type = 'question';
                                $o->text = h($option->getSelectAttributeOptionValue());
                                $o->id = $option->getSelectAttributeOptionID();
                                break;

                            case 'tags':
                                $o = new stdClass;
                                $o->type = 'tag';
                                $o->text = h($option->getSelectAttributeOptionValue());
                                $o->id = $option->getSelectAttributeOptionID();
                                break;
                        }
                    }
                }
            }

            if ($o === null) {
                $o = new stdClass;
                $o->type = 'query';
                $o->text = h($kw);
                $o->id = $o->text;
            }

            $this->set('selection', $o);
        }

        $audience = null;

        if (in_array($this->request->query->get('audience'), ['developers', 'designers', 'editors'])) {
            $audience = $this->request->query->get('audience');
        }

        $this->set('audience', $audience);
    }

    /** @noinspection PhpUnused */
    public function action_load_questions($bID = false)
    {
        $options = [];

        if ($this->bID == $bID) {
            /** @var Key $ak */
            $ak = CollectionKey::getByHandle('questions_answered');

            /** @var SelectValueOption[] $questionAnsweredOptions */
            $questionAnsweredOptions = $ak->getController()->getOptions('%' . $this->request->query->get("q") . '%');

            foreach ($questionAnsweredOptions as $option) {
                $o = new stdClass;
                $o->id = $option->getSelectAttributeOptionID();
                $o->type = 'question';
                $o->text = $option->getSelectAttributeOptionValue();

                $options[] = $o;
            }

            /** @var Key $ak */
            $ak = CollectionKey::getByHandle('tags');

            /** @var SelectValueOption[] $tagOptions */
            $tagOptions = $ak->getController()->getOptions('%' . $this->request->query->get("q") . '%');

            foreach ($tagOptions as $option) {
                $o = new stdClass;
                $o->id = $option->getSelectAttributeOptionID();
                $o->type = 'tag';
                $o->text = $option->getSelectAttributeOptionValue();

                $options[] = $o;
            }
        }

        return new JsonResponse($options);
    }

}
