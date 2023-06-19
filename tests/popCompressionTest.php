<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use EurovisionVoting\Contest;

require_once 'vendor/autoload.php';

$contest = new Contest;
$contest->parsePopulations();

echo("\nStarting the compression.\n");
$newPopulations = $contest->getCompressedPopulations(1);
foreach ($contest->populations as $country=>$pop) {
    echo($country." is now ".ceil(100*$newPopulations[$country] / $contest->populations[$country])."% of it's previous amount\n");
}

/*echo("\nStarting the alternative compression.\n");
$newPopulations2 = $contest->getAltCompressedPopulations(1);
foreach ($contest->populations as $country=>$pop) {
    echo($country." is now ".ceil(100*$newPopulations2[$country] / $contest->populations[$country])."% of it's previous amount\n");
}*/