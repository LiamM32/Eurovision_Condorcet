<?php

namespace EurovisionVoting;

class tools
{
    public static function large(array $array, int $rank)
    {
        sort($array);
        return $array[sizeof[$array]-$rank];
    }

   // Copied from here: https://stackoverflow.com/a/11872928/6157763
    public static function getRandomWeightedElement(array $weightedValues) {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
    }

    public static function array_reciprocal ($array) {
        foreach ($array as &$element) {
            $element = 1 / $element;
        }
        return $array;
    }

    // From https://stackoverflow.com/a/1320156/6157763
    public static function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }
}