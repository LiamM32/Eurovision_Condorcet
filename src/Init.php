<?php

declare(strict_types=1);

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze1;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Method\EurovisionSchulze3;

class Init {
    public static array $options = ['v'=>0, 'mode'=>'default'];
    public static int $optCount = 0;
    public static function settings($argv) {
        self::$options['verbose'] = &self::$options['v'];
        $opts = getopt('Nf::qVv::', ['mode::'], $rest_index);
        self::$optCount = $rest_index - 1;

        if (isset($opts['v']) && $opts['v']===false) self::$options['v'] += 1;
        if (isset($opts['q'])) self::$options['v']--;
        if (isset($opts['V'])) self::$options['v'] += 2;
        if (isset($opts['v']) && is_numeric($opts['v'])) self::$options['v'] = (int) $opts['v'];

        if (isset($opts['N'])) self::$options['mode'] = 'Narrative';
        if (isset($opts['mode'])) self::$options['mode'] = ucfirst($opts['mode']);

        if (isset($opts['f'])) {
            if ($opts['f'] === false) self::$options['fast'] = 1;
            if (is_numeric($opts['f'])) self::$options['fast'] = (int)$opts['f'];
        } else self::$options['fast'] = 0;
    }
    public static function registerMethods(): void {
        Condorcet::addMethod(EurovisionSchulze::class);
        Condorcet::addMethod(EurovisionSchulze1::class);
        Condorcet::addMethod(EurovisionSchulze2::class);
        // Condorcet::addMethod(EurovisionSchulze3::class);
    }
}

