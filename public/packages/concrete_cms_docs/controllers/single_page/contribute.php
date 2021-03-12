<?php /** @noinspection PhpUnused */
/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\ConcreteCmsDocs\Controller\SinglePage;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use PortlandLabs\ConcreteCms\Documentation\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;
use Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Contribute extends PageController
{
    /** @var Token */
    protected $token;
    /** @var ErrorList */
    protected $error;
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var UserInfoRepository */
    protected $userInfoRepository;

    public function on_start()
    {
        parent::on_start();

        $this->token = $this->app->make(Token::class);
        $this->error = $this->app->make(ErrorList::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->userInfoRepository = $this->app->make(UserInfoRepository::class);

        $this->requireAsset("core/cms");
    }

    public function on_before_render()
    {
        $this->set('token', $this->token);
        $this->set('error', $this->error);
    }

    public function edit($cID = null)
    {
        $u = new User();

        if (!$u->isRegistered()) {
            return $this->responseFactory->forbidden(Page::getCurrentPage());
        }

        $document = $this->getDocument($cID);

        if (!$document) {
            return $this->responseFactory->forbidden(Page::getCurrentPage());
        } else {
            $type = $document->getPageTypeObject();

            $this->set('pagetype', $type);
            $this->set('document', $document);

            switch ($type->getPageTypeHandle()) {
                case 'developer_document':
                    $this->set('buttonTitle', t('Update'));
                    $this->set('pageTitle', t('Update Developer Document'));
                    $this->set('documentationType', 'developer_documentation');
                    break;

                case 'editor_document':
                    $this->set('buttonTitle', t('Update'));
                    $this->set('pageTitle', t('Update Editor Document'));
                    $this->set('documentationType', 'editor_documentation');
                    break;

                default:
                    $this->set('buttonTitle', t('Update'));
                    $this->set('pageTitle', t('Update Tutorial'));
                    $this->set('documentationType', 'tutorial');
                    break;
            }

            $p = new Checker();
            /** @noinspection PhpUndefinedMethodInspection */
            $this->set('canEditDocumentAuthor', $p->canEditDocumentAuthor());

            $ui = $this->userInfoRepository->getByID($document->getCollectionUserID());

            if ($ui instanceof UserInfo) {
                $this->set('documentAuthor', $ui->getUserName());
            }

            $this->set('action', (string)Url::to('/contribute', 'save', $cID));
        }
    }

    /**
     * @param $cID
     * @return bool|Page
     */
    protected function getDocument($cID)
    {
        $c = Page::getByID($cID);

        $document = false;

        if ($c instanceof Page && !$c->isError()) {
            $type = $c->getPageTypeObject();

            if ($type instanceof Type && in_array($type->getPageTypeHandle(), ['developer_document', 'editor_document', 'tutorial'])) {
                $cp = new Checker($c);
                /** @noinspection PhpUndefinedMethodInspection */
                if ($cp->canEditInDocumentationComposer()) {
                    $document = $c;
                }
            }
        }

        return $document;
    }

    public function view()
    {
        $u = new User();

        if ($u->isRegistered()) {
            $this->render('/contribute/choose_type');
        } else {
            return $this->responseFactory->forbidden(Page::getCurrentPage());
        }
    }

    /**
     * @param null $documentationType
     * @return Type
     */
    protected function getDocumentPageType($documentationType = null)
    {
        if (!$documentationType) {
            $documentationType = $this->request->request->get('documentation_type');
        }

        switch ($documentationType) {
            case 'developer_documentation':
                $this->set('buttonTitle', t('Add Developer Document'));
                $this->set('pageTitle', t('Add Developer Documentation'));
                $pageType = Type::getByHandle('developer_document');
                break;

            case 'editor_documentation':
                $this->set('buttonTitle', t('Add Editor Document'));
                $this->set('pageTitle', t('Add Editor Documentation'));
                $pageType = Type::getByHandle('editor_document');
                break;

            default:
                $this->set('buttonTitle', t('Add Tutorial'));
                $this->set('pageTitle', t('Add Tutorial'));
                $pageType = Type::getByHandle('tutorial');
                break;
        }

        return $pageType;
    }

    public function choose_type($documentationType = null)
    {
        $u = new User();

        if ($u->isRegistered()) {
            $this->set('action', (string)Url::to('/contribute', 'create'));
            $this->set('document', null);

            if (!$documentationType) {
                $documentationType = $this->request->request->get('documentation_type');
            }

            $pageType = $this->getDocumentPageType($documentationType);

            $this->set('documentationType', $documentationType);

            switch ($documentationType) {
                case 'editor_documentation':
                    $this->set('buttonTitle', t('Add Editor Document'));
                    $this->set('pageTitle', t('Add Editor Documentation'));
                    break;

                case 'developer_documentation':
                    $this->set('buttonTitle', t('Add Developer Document'));
                    $this->set('pageTitle', t('Add Developer Documentation'));
                    break;

                default:
                    $this->set('buttonTitle', t('Add Tutorial'));
                    $this->set('pageTitle', t('Add Tutorial'));
                    break;
            }

            $this->set('pagetype', $pageType);
        } else {
            return $this->responseFactory->forbidden(Page::getCurrentPage());
        }
    }

    /**
     * @param Type $type
     * @return int
     */
    protected function getTargetPageParentID($type)
    {
        $configuredTarget = $type->getPageTypePublishTargetObject();
        $cParentID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();

        if (!$cParentID) {
            $cParentID = $this->request->request->get('cParentID');
        }

        return $cParentID;
    }

    /**
     * @param Page $document
     */
    protected function validate($document = null)
    {
        $type = $this->getDocumentPageType();

        if ($document) {
            $cParentID = $document->getCollectionParentID();
        } else {
            $cParentID = $this->getTargetPageParentID($type);
        }

        $parent = Page::getByID($cParentID);

        $validator = $type->getPageTypeValidatorObject();
        $template = $type->getPageTypeDefaultPageTemplateObject();
        $this->error->add($validator->validateCreateDraftRequest($template));
        $this->error->add($validator->validatePublishDraftRequest());

        if (!is_object($parent) || $parent->isError()) {
            $this->error->add(t('You must choose a page to publish this page beneath.'));
        } else {
            $pt = new Checker($type);

            switch ($type->getPageTypeHandle()) {
                case 'tutorial':
                    /** @noinspection PhpUndefinedMethodInspection */
                    if ($parent->getCollectionPath() != '/tutorials' ||
                        !$pt->canAddInDocumentationComposer()) {
                        $this->error->add(t('You do not have permission to publish a page in this location.'));
                    }

                    break;

                case 'document':
                    /** @noinspection PhpUndefinedMethodInspection */
                    if ($parent->getPageTypeHandle() != 'section' ||
                        !$pt->canAddInDocumentationComposer()) {
                        $this->error->add(t('You do not have permission to publish a page in this location.'));
                    }

                    break;
            }
        }
    }

    /**
     * @param string|null $cID
     * @return RedirectResponse|SymfonyResponse
     * @throws Exception
     */
    public function save($cID = null)
    {
        $document = $this->getDocument($cID);

        if (!$document) {
            throw new Exception(t('Access Denied.'));
        }

        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->request->request->get('versionComment')) {
            $this->error->add(t('You must specify the changes you made in this document.'));
        }

        $this->validate($document);

        if (!$this->error->has()) {
            $type = $this->getDocumentPageType();
            $document = $document->getVersionToModify();
            $v = $document->getVersionObject();
            $comment = $this->request->request->get('versionComment');

            $p = new Checker();

            $updateAuthor = false;
            /** @noinspection PhpUndefinedMethodInspection */
            if ($p->canEditDocumentAuthor()) {
                if ($this->request->request->get('documentAuthor')) {
                    $originalAuthor = $this->userInfoRepository->getByID($document->getCollectionUserID());
                    $originalAuthorName = t('(Unknown)');

                    if ($originalAuthor instanceof UserInfo) {
                        $originalAuthorName = $originalAuthor->getUserName();
                    }

                    $documentAuthor = $this->userInfoRepository->getByName($this->request->request->get('documentAuthor'));

                    if ($documentAuthor instanceof UserInfo && $documentAuthor->getUserName() != $originalAuthorName) {
                        $comment .= t(' - Note: author changed from %s to %s', $originalAuthorName, $documentAuthor->getUserName());
                        $updateAuthor = true;
                    }
                }
            }

            $v->setComment($comment);
            /** @noinspection PhpDeprecationInspection */
            $type->savePageTypeComposerForm($document);
            $type->publish($document);

            if (isset($documentAuthor) && $documentAuthor instanceof UserInfo && $updateAuthor) {
                $document->update(['uID' => $documentAuthor->getUserID()]);
            }

            $this->flash('success', t('Page updated! Your changes are under review.'));

            return $this->responseFactory->redirect((string)Url::to(Page::getByPath("/contributions")), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->edit($cID);
    }

    public function create()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $this->validate();

        if (!$this->error->has()) {
            $type = $this->getDocumentPageType();
            $template = $type->getPageTypeDefaultPageTemplateObject();
            $d = $type->createDraft($template);
            $cParentID = $this->getTargetPageParentID($type);
            $d->setPageDraftTargetParentPageID($cParentID);
            /** @noinspection PhpDeprecationInspection */
            $type->savePageTypeComposerForm($d);
            $type->publish($d);

            /** @var GroupRepository $groupRepository */
            $groupRepository = $this->app->make(GroupRepository::class);
            $registeredUsersGroup = $groupRepository->getGroupByID(REGISTERED_GROUP_ID);
            $guestsGroup = $groupRepository->getGroupByID(GUEST_GROUP_ID);

            if (is_object($registeredUsersGroup)) {
                $d->assignPermissions($registeredUsersGroup, ["edit_in_documentation_composer"]);
            }

            if (is_object($guestsGroup)) {
                $d->assignPermissions($guestsGroup, ["view_page"]);
            }

            $this->flash('success', t('Thanks for your contribution! We are reviewing it for accuracy.'));

            return $this->responseFactory->redirect((string)Url::to(Page::getByPath("/contributions")), Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->choose_type();
    }

}