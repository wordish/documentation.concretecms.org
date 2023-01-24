<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\ConcreteCms\Documentation\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Level\ExpensiveCache;
use Concrete\Core\User\Avatar\AvatarServiceInterface;
use Concrete\Core\User\UserInfo;
use GuzzleHttp\Client;

class AvatarService implements AvatarServiceInterface
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getAvatarPath($communityUserID)
    {
        return sprintf('https://community.concretecms.org/application/files/avatars/%s.jpg', $communityUserID);
    }

    /**
     * @param UserInfo $ui
     * @return bool
     */
    public function userHasAvatar(UserInfo $ui)
    {
        /** @var ExpensiveCache $cache */
        $cache = $this->application->make(ExpensiveCache::class);
        $identifier = "user.avatar.exists.{$ui->getUserID()}";
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        if ($ui->getUserCommunityUserID()) {
            // I know this sucks. These type hints should be UserInfoInterface so that I can swap out UserInfo with our extended implementation. sadly
            // that's a bit more than what's on our plate right now, but maybe in v8
            $client = new Client();
            try {
                $response = $client->head($this->getAvatarPath($ui->getUserCommunityUserID()));
                if ($response->getStatusCode() == 200) {
                    $response = true;
                } else {
                    $response = false;
                }

            } catch (\Exception $err) {
                $response = false;
            }
        } else {
            $response = false;
        }

        $cache->save($item->set($response)->expiresAfter(1800)); // 30 minutes

        return $response;
    }

    public function removeAvatar(UserInfo $ui)
    {
        return false;
    }

    public function getAvatar(UserInfo $ui)
    {
        if ($this->userHasAvatar($ui)) {
            return $this->application->make('PortlandLabs\ConcreteCms\Documentation\User\Avatar\CommunityAvatar', ['userInfo' => $ui]);
        } else {
            return $this->application->make('Concrete\Core\User\Avatar\EmptyAvatar', ['userInfo' => $ui]);
        }
    }


}