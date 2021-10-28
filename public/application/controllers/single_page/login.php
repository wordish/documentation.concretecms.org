<?php
declare(strict_types=1);

namespace Application\Controller\SinglePage;

use Concrete\Core\User\PostLoginLocation;

class Login extends \Concrete\Controller\SinglePage\Login
{

    public function view($type = null, $element = 'form')
    {
        parent::view($type, $element);

        // Set post login location from request. Make sure it's a path and not a url.
        $requestedRedirect = $this->request->get('r');
        if ($requestedRedirect && $requestedRedirect[0] === '/') {
            $this->app->make(PostLoginLocation::class)->setSessionPostLoginUrl(\URL::to($requestedRedirect));
        }
    }

}