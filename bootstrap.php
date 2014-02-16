<?php

namespace PHPoAuthImplDemo;

use PHPoAuthImpl\Psr0\Autoloader;
use PHPoAuthImplDemo\Network\Http\Request;
use PHPoAuthImplDemo\Storage\ImmutableArray;
use PHPoAuthImpl\Service\Collection;

/**
 * Setup the environment
 */
require_once __DIR__ . '/init.deployment.php';

/**
 * Bootstrap the PHPoAuthImpl library
 */
require_once __DIR__ . '/vendor/peehaa/oauth/src/PHPoAuthImpl/bootstrap.php';

/**
 * Setup autoloading
 */
$autoloader = new Autoloader(__NAMESPACE__, __DIR__ . '/src');
$autoloader->register();

/**
 * Setup the request
 */
$request = new Request(
    new ImmutableArray(explode('/', trim(preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']), '/'))),
    new ImmutableArray($_GET),
    new ImmutableArray($_POST),
    new ImmutableArray($_SERVER),
    new ImmutableArray($_FILES),
    new ImmutableArray($_COOKIE)
);

/**
 * Initialize the oauth services
 */
$services = new Collection;

$services->add('Twitter', $credentials['twitter']['key'], $credentials['twitter']['secret']);

/**
 * Setup routing and content template
 */
ob_start();

if ($request->getPath() === '/') {
    require __DIR__ . '/templates/overview.phtml';
} elseif (preg_match('#^/authorize/([^\/]+)$#', $request->getPath()) === 1 && $request->get('oauth_token') !== null) {
    $services->getAccessToken(
        $request->path(1),
        $request->get('oauth_token'),
        $request->get('oauth_verifier')
    );

    header('Location: ' . $request->getBaseUrl());
    exit;
} elseif (preg_match('#^/authorize/([^\/]+)$#', $request->getPath()) === 1) {
    $services->authorize($request->path(1));
} elseif (preg_match('#^/([^\/]+)$#', $request->getPath()) === 1) {
    require __DIR__ . '/templates/service/' . strtolower($request->path(0)) . '.phtml';
} elseif (preg_match('#^/([^\/]+)/(.*)$#', $request->getPath()) === 1) {
    $result = $services->request($request->pathIterator());

    require __DIR__ . '/templates/result.phtml';
} else {
    require __DIR__ . '/templates/not-found.phtml';
}

$content = ob_get_clean();
ob_end_clean();

require __DIR__ . '/templates/page.phtml';
