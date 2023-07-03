<?php

namespace EurovisionVoting;

class countrydata
{
    public static function getCountryNames(string $lang = 'en'): array
    {
        $names['en'] = [
            'ALB' => 'Albania',
            'AND' => 'Andorra',
            'ARM' => 'Armenia',
            'AUS' => 'Australia',
            'AUT' => 'Austria',
            'AZE' => 'Azerbaijan',
            'BEL' => 'Belgium',
            'BGR' => 'Bulgaria',
            'BIH' => 'Bosnia and Herzegovina',
            'BLR' => 'Belarus',
            'CHE' => 'Switzerland',
            'CYP' => 'Cyprus',
            'CZE' => 'Czechia',
            'DEU' => 'Germany',
            'DNK' => 'Denmark',
            'DZA' => 'Algeria',
            'EGY' => 'Egypt',
            'ESP' => 'Spain',
            'EST' => 'Estonia',
            'FIN' => 'Finland',
            'FRA' => 'France',
            'FRO' => 'Faroe Islands',
            'GBR' => 'United Kingdom',
            'GEO' => 'Georgia',
            'GRC' => 'Greece',
            'GRD' => 'Grenada',
            'GRL' => 'Greenland',
            'HRV' => 'Croatia',
            'HUN' => 'Hungary',
            'IMN' => 'Isle of Man',
            'IRL' => 'Ireland',
            'ISL' => 'Iceland',
            'ISR' => 'Israel',
            'ITA' => 'Italy',
            'JOR' => 'Jordan',
            'KAZ' => 'Kazakhstan',
            'LBN' => 'Lebanon',
            'LIE' => 'Liechtenstein',
            'LTU' => 'Lithuania',
            'LUX' => 'Luxembourg',
            'LVA' => 'Latvia',
            'MAR' => 'Morocco',
            'MCO' => 'Monaco',
            'MDA' => 'Moldova',
            'MKD' => 'North Macedonia',
            'MLT' => 'Malta',
            'MNE' => 'Montenegro',
            'NLD' => 'Netherlands',
            'NOR' => 'Norway',
            'POL' => 'Poland',
            'PRT' => 'Portugal',
            'ROU' => 'Romania',
            'RUS' => 'Russia',
            'SMR' => 'San Marino',
            'SRB' => 'Serbia',
            'SVK' => 'Slovakia',
            'SVN' => 'Slovenia',
            'SWE' => 'Sweden',
            'TUN' => 'Tunisia',
            'TUR' => 'Turkey',
            'UKR' => 'Ukraine',
            'WLD' => 'World',
            'XKX' => 'Kosovo'
        ];
        $names['fr'] = [
            'ALB' => "Albanie",
            'ARM' => "Arménie",
            'AUS' => "Australie",
            'AUT' => "Autrich",
            'BEL' => "Belgique",
            'BGR' => "Bulgarie",
            'BIH' => "Bosnie-Herzégovine",
            'CHE' => "Suisse",
            'CYP' => "Chypre",
            'CZE' => "la Tchéquie",
            'DEU' => "l'Allemagne",
            'DNK' => "Danemark",
            'ESP' => "l'Espagne",
            'EST' => "l'Estonie",
            'FIN' => "la Finlande",
            'FRA' => "la France",
            'GBR' => "Royaume-Uni",
            'GEO' => "la Géorgie",
            'GRC' => "la Grèce",
            'HRV' => "la Croatie",
            'HUN' => "la Hongrie",
            'IRL' => "l'Irlande",
            'ISL' => "l'Islande",
            'ISR' => "Israël",
            'ITA' => "l'Italie",
            'LTU' => "la Lituanie",
            'LUX' => "Luxembourg",
            'LVA' => "la Lettonie",
            'MDA' => "la Moldavie",
            'MNE' => "Monténégro",
            'NLD' => "Pays-Bas",
            'NOR' => "la Norvège",
            'POL' => "la Pologne",
            'PRT' => "Portugal",
            'ROU' => "la Roumanie",
            'RUS' => "la Russie",
            'SRB' => "la Serbie",
            'SVN' => "la Slovénie",
            'SWE' => "la Suède",
        ];
        return $names[$lang];
    }

    public static function replaceInString(string $string, $lang = 'en'): string
    {
        $countryNames = self::getCountryNames($lang);
        return str_replace(array_keys($countryNames), $countryNames, $string);
    }
}