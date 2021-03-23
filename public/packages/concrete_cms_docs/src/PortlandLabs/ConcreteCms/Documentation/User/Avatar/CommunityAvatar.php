<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\User\Avatar\StandardAvatar;
use PortlandLabs\ConcreteCms\Documentation\User\Avatar\AvatarService as DocsAvatarService;
use PortlandLabs\ConcreteCms\Documentation\User\UserInfo;

class CommunityAvatar extends StandardAvatar
{
    protected $userInfo;
    protected $application;
    protected $avatarService;
    /** @noinspection PhpMissingParentConstructorInspection */

    /**
     * Sigh. Again with the UserInfoInterface
     * @param UserInfo $userInfo
     * @param Application $application
     * @param DocsAvatarService $avatarService
     */
    public function __construct(UserInfo $userInfo, Application $application, AvatarService $avatarService)
    {
        $this->userInfo = $userInfo;
        $this->application = $application;
        $this->avatarService = $avatarService;
    }

    public function getPath()
    {
        return $this->avatarService->getAvatarPath($this->userInfo->getUserCommunityUserID());
    }
}
