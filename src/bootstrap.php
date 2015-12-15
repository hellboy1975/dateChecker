<?php

// require 'autoload.php';
require_once __DIR__.'/../vendor/autoload.php';

require __DIR__.'/config.php';

$app = new Silex\Application();

// use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

$app->register(new Silex\Provider\SessionServiceProvider()); 
$app->register(new Silex\Provider\ServiceControllerServiceProvider()); 
$app->register(new Silex\Provider\UrlGeneratorServiceProvider()); 
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

// register providers
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       	=> __DIR__.'/../views',
    'twig.class_path' 	=> __DIR__.'/../vendor/twig/lib',
    'twig.cache'		=> (!DEBUG_APP),  // disble caching when debugging
));


$app['debug'] = DEBUG_APP;

return $app;