<?php

namespace Concrete\Package\ConcreteCmsDocs\Controller;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use \PharIo\Version\Version;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Takes a query parameter (keywords), finds the latest published copy of the
 * API by looping through the directory, and forwards the request on to the
 * API documentation.
 *
 * Redirects a request like `/api/search?keywords=Concrete\Core\Calendar\Event`
 * to
 * `/api/<latest_version>/search.html?search=Concrete%5CCore%5CCalendar%5CEvent`
 *
 * Need the following set in `.env`:
 * URL_SITE_DOCUMENTATION_API="https://documentation.concretecms.org/api/"
 * PATH_SITE_DOCUMENTATION_API="../<documentation_api_path>/current/public"
 */

class ApiDocumentation
{

    public function search()
    {
        $apiUrl = rtrim(getenv('URL_SITE_DOCUMENTATION_API'), '/');

        $latestVersion = '0.0.0'; // start at zero

        foreach (new \DirectoryIterator(getenv('PATH_SITE_DOCUMENTATION_API')) as $dir) {
            if ($dir->isDot()) {
                continue;               // ignore '..' and '.'
            }
            $version = $dir->getFilename();
            if (version_compare($latestVersion, $version, '<')) {
                $latestVersion = $version;
            }
        }

        $request = Request::createFromGlobals();
        $keywords = $request->query->get('keywords');

        $queryData = [];
        $queryData['search'] = ltrim($keywords, '\\'); // defend against leading backslashes
        $query = http_build_query($queryData);

        $redirect =  "${apiUrl}/${latestVersion}/search.html?${query}";
        $status = 307;

        return new RedirectResponse($redirect, $status);
    }

}
