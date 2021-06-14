<?php

use App\Models\Firewall;

session_start();

require __DIR__ . '/../vendor/autoload.php';

define('RULES', require __DIR__ . '/config/rules.php');

define('DEBUG', true);

(new Firewall());
