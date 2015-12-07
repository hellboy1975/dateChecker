<?php

require_once __DIR__.'/../vendor/autoload.php';

function my_autoloader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('my_autoloader');