<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Console\CondorcetApplication;
use CondorcetPHP\ModulesSkeletons\Method\MyVerySimpleMethod;

require_once __DIR__ . '/../vendor/autoload.php';

# Add your method, now available on the method list
Condorcet::addMethod(MyVerySimpleMethod::class);

CondorcetApplication::run();

# php bin/CondorcetCommandLine.php
