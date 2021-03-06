<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed;

use Auryn\Injector;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Create as CreateAdministrators;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Search as SearchUser;
use PeeHaa\AwesomeFeed\Presentation\Controller\Authorization\LogIn;
use PeeHaa\AwesomeFeed\Presentation\Controller\Authorization\LogOut;
use PeeHaa\AwesomeFeed\Presentation\Controller\Dashboard;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\Create as CreateFeed;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\Edit as EditFeed;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\Create as CreateRepository;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\Search as SearchRepository;
use PeeHaa\AwesomeFeed\Router\Manager as RouteManager;

/** @var Injector $auryn */
$gateKeeper = $auryn->make(GateKeeper::class);
$router     = $auryn->make(RouteManager::class);

$router->get('renderNotFound', '/not-found', [Error::class, 'notFound']);
$router->get('renderMethodNotAllowed', '/method-not-allowed', [Error::class, 'methodNotAllowed']);

if (!$gateKeeper->isAuthorized()) {
    $router->get('home', '/', [LogIn::class, 'render']);
    $router->get('renderLogin', '/login', [LogIn::class, 'render']);
    $router->post('processGitHubLogin', '/github/login', [LogIn::class, 'processGitHubLogIn']);
    $router->get('processGitHubRedirectUri', '/github/login', [LogIn::class, 'processGitHubLogInRedirectUri']);
}

if ($gateKeeper->isAuthorized()) {
    $router->get('home', '/', [Dashboard::class, 'render']);
    $router->post('createFeed', '/feeds/create', [CreateFeed::class, 'process']);
    $router->get('editFeed', '/feeds/{id:\d+}/{slug:.+}/edit', [EditFeed::class, 'render']);
    $router->post('searchUsers', '/feeds/{id:\d+}/{slug:.+}/administrators/search', [SearchUser::class, 'render']);
    $router->post('addAdministrators', '/feeds/{id:\d+}/{slug:.+}/administrators/create', [CreateAdministrators::class, 'process']);
    $router->post('searchRepositories', '/feeds/{id:\d+}/{slug:.+}/repositories/search', [SearchRepository::class, 'render']);
    $router->post('addRepositories', '/feeds/{id:\d+}/{slug:.+}/repositories/create', [CreateRepository::class, 'process']);
    $router->post('logout', '/logout', [LogOut::class, 'process']);
}
