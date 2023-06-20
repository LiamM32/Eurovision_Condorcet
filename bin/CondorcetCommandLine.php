<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Console\CondorcetApplication;
use EurovisionVoting\Init;

require_once __DIR__ . '/../vendor/autoload.php';

# Add your method, now available on the method list
Init::registerMethods();

CondorcetApplication::run();

# php bin/CondorcetCommandLine.php
