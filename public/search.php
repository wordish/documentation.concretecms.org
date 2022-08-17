<?php

/**
 * Takes a query parameter (keywords), finds the latest published copy of the
 * API by looping through the directory, and forwards the request on to the
 * API documentation.
 *
 * Redirects a request like `/search.php?keywords=Concrete\Core\Calendar\Event`
 * to
 * `/api/<latest_version>/search.html?search=Concrete%5CCore%5CCalendar%5CEvent`
 *
 * Need the following set in `.env`:
 * URL_SITE_DOCUMENTATION_API="https://documentation.concretecms.org/api/"
 * PATH_SITE_DOCUMENTATION_API="../<documentation_api_path>/current/public"
 */

use \Dotenv\Dotenv;
use \PharIo\Version\Version;

# Load in the composer vendor files
require_once __DIR__ . "/../vendor/autoload.php";

# Try loading in environment info
$env = new Dotenv(__DIR__ . '/../');
try {
    $env->overload();
} catch (\Exception $e) {
    // Ignore any errors
}

# Add the vendor directory to the include path
ini_set('include_path', __DIR__ . "/../../../vendor" . PATH_SEPARATOR . get_include_path());

$apiUrl = rtrim(getenv('URL_SITE_DOCUMENTATION_API'), '/');

$latestVersion = new Version('0.0.0');

foreach (new \DirectoryIterator('../' . getenv('PATH_SITE_DOCUMENTATION_API')) as $dir) {
    if ($dir->isDot()) {
        continue;               // ignore '..' and '.'
    }
    $version = new Version($dir);
    if ($version->isGreaterThan($latestVersion)) {
        $latestVersion = $version;
    }
}

$version = $latestVersion->getVersionString();

$queryData = [];
$keywords = $_GET['keywords'] ?: '';
$queryData['search'] = ltrim($keywords, '\\'); // defend against leading backslashes
$query = http_build_query($queryData);

$redirect =  "${apiUrl}/${version}/search.html?${query}";


$header = "Location: ${redirect}";
$replace = true;
$response_code = 307;

header($header, $replace, $response_code);

die;
