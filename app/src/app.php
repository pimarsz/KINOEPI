<?php
/**
 * Init application.
 */

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;



$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(
    new TwigServiceProvider(),
    [
        'twig.path' => dirname(dirname(__FILE__)).'/templates',
    ]
);
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app->register(
    new TranslationServiceProvider(),
    [
        'locale' => 'pl',
        'locale_fallbacks' => array('en'),
    ]
);

$app->register(new LocaleServiceProvider());

$app->register(
    new SecurityServiceProvider(),
    [
        'security.firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'pattern' => '^.*$',
                'form' => [
                    'login_path' => 'auth_login',
                    'check_path' => 'auth_login_check',
                    'default_target_path' => 'movieList_index',
                    'username_parameter' => 'login_type[login]',
                    'password_parameter' => 'login_type[password]',
                ],
                'anonymous' => true,
                'logout' => [
                    'logout_path' => 'auth_logout',
                    'target_url' => 'movieList_index',
                ],
                'users' => function () use ($app) {
                    return new Provider\UserProvider($app['db']);
                },
            ],
        ],
        'security.access_rules' => [
            ['^/auth.+$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['^/movies.+$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['^/screening/view.+$', 'ROLE_USER'],
            ['^/transaction/add.+$', 'ROLE_USER'],
            ['^/screening/([0-9])*$', 'ROLE_USER'],
            ['^/order.+$', 'ROLE_USER'],
            ['^/order/add/{id}.+$', 'ROLE_USER'],
            ['^/screening/add.+$', 'ROLE_ADMIN'],
            ['^/.+$', 'ROLE_ADMIN'],
        ],
        'security.role_hierarchy' => [
            'ROLE_ADMIN' => ['ROLE_USER'],
        ],
    ]
);

$app->extend('translator', function ($translator, $app) {
    $translator->addResource('xliff', __DIR__.'/../translations/messages.en.xlf', 'en', 'messages');
    $translator->addResource('xliff', __DIR__.'/../translations/validators.en.xlf', 'en', 'validators');
    $translator->addResource('xliff', __DIR__.'/../translations/messages.pl.xlf', 'pl', 'messages');
    $translator->addResource('xliff', __DIR__.'/../translations/validators.pl.xlf', 'pl', 'validators');

    return $translator;
});

$app->register(
    new DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'phpmyadmin',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ],
    ]
);

$app->register(new SessionServiceProvider());


//dump($app['security.encoder.bcrypt']->encodePassword('jTE7cm666Xk6', ''));
//dump($app['security.encoder.bcrypt']->encodePassword('qAWfMuqyVEe5', ''));

return $app;
