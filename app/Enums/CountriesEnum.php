<?php

namespace App\Enums;

enum CountriesEnum: string
{
    case Albania = 'Albania';
    case Austria = 'Austria';
    case Bangladesh = 'Bangladesh';
    case Belgium = 'Belgium';
    case Bosnia_and_Herzegovina = 'Bosnia and Herzegovina';
    case Brazil = 'Brazil';
    case Bulgaria = 'Bulgaria';
    case Bulgary = 'Bulgary';
    case Cambodia = 'Cambodia';
    case Cameroon = 'Cameroon';
    case Canada = 'Canada';
    case Chile = 'Chile';
    case Colombia = 'Colombia';
    case Costa_Rica = 'Costa Rica';
    case Croatia = 'Croatia';
    case Cyprus = 'Cyprus';
    case Czech = 'Czech';
    case Czech_Republic = 'Czech Republic';
    case Czech_Republic2 = "Czech Republic'";
    case Czechia = 'Czechia';
    case Denmark = 'Denmark';
    case Dominican_Republic = 'Dominican Republic';
    case Ecuador = 'Ecuador';
    case Egypt = 'Egypt';
    case France = 'France';
    case Germany = 'Germany';
    case Ghana = 'Ghana';
    case Greece = 'Greece';
    case Guatemala = 'Guatemala';
    case Honduras = 'Honduras';
    case Hong_Kong = 'Hong Kong';
    case Hungary = 'Hungary';
    case India = 'India';
    case Indonesia = 'Indonesia';
    case Italia = 'Italia';
    case Italy = 'Italy';
    case Japan = 'Japan';
    case Jordan = 'Jordan';
    case Kazakhstan = 'Kazakhstan';
    case Kenya = 'Kenya';
    case Kyrgyzstan = 'Kyrgyzstan';
    case Latvia = 'Latvia';
    case Lithuania = 'Lithuania';
    case Malaysia = 'Malaysia';
    case Mexico = 'Mexico';
    case Moldova = 'Moldova';
    case Morocco = 'Morocco';
    case Netherlands = 'Netherlands';
    case Nigeria = 'Nigeria';
    case North_Macedonia = 'North Macedonia';
    case Norway = 'Norway';
    case Peru = 'Peru';
    case Philippines = 'Philippines';
    case Poland = 'Poland';
    case Portugal = 'Portugal';
    case Portugalia = 'Portugalia';
    case Romania = 'Romania';
    case Romania2 = 'Romania/';
    case Saudi_Arabia = 'Saudi Arabia';
    case Serbia = 'Serbia';
    case Singapore = 'Singapore';
    case Slovakia = 'Slovakia';
    case Slovenia = 'Slovenia';
    case South_Africa = 'South Africa';
    case South_Korea = 'South Korea';
    case Spain = 'Spain';
    case Sri_Lanka = 'Sri Lanka';
    case Sweden = 'Sweden';
    case Switzerland = 'Switzerland';
    case Tanzania = 'Tanzania';
    case Thailand = 'Thailand';
    case Turkey = 'Turkey';
    case Ukraine = 'Ukraine';
    case United_Arab_Emirates = 'United Arab Emirates';
    case United_Kingdom = 'United Kingdom';
    case United_States = 'United States';
    case Venezuela = 'Venezuela';
    case Vietnam = 'Vietnam';


    public function toString() {
        return match ($this) {
            self::Albania => 'Albania',
            self::Austria => 'Austria',
            self::Bangladesh => 'Bangladesh',
            self::Belgium => 'Belgium',
            self::Bosnia_and_Herzegovina => 'Bosnia and Herzegovina',
            self::Brazil => 'Brazil',
            self::Bulgaria => 'Bulgaria',
            self::Bulgary => 'Bulgary',
            self::Cambodia => 'Cambodia',
            self::Cameroon => 'Cameroon',
            self::Canada => 'Canada',
            self::Chile => 'Chile',
            self::Colombia => 'Colombia',
            self::Costa_Rica => 'Costa_Rica',
            self::Croatia => 'Croatia',
            self::Cyprus => 'Cyprus',
            self::Czech => 'Czech',
            self::Czech_Republic => 'Czech Republic',
            self::Czech_Republic2 => 'Czech Republic2',
            self::Czechia => 'Czechia',
            self::Denmark => 'Denmark',
            self::Dominican_Republic => 'Dominican Republic',
            self::Ecuador => 'Ecuador',
            self::Egypt => 'Egypt',
            self::France => 'France',
            self::Germany => 'Germany',
            self::Ghana => 'Ghana',
            self::Greece => 'Greece',
            self::Guatemala => 'Guatemala',
            self::Honduras => 'Honduras',
            self::Hong_Kong => 'Hong_Kong',
            self::Hungary => 'Hungary',
            self::India => 'India',
            self::Indonesia => 'Indonesia',
            self::Italia => 'Italia',
            self::Italy => 'Italy',
            self::Japan => 'Japan',
            self::Jordan => 'Jordan',
            self::Kazakhstan => 'Kazakhstan',
            self::Kenya => 'Kenya',
            self::Kyrgyzstan => 'Kyrgyzstan',
            self::Latvia => 'Latvia',
            self::Lithuania => 'Lithuania',
            self::Malaysia => 'Malaysia',
            self::Mexico => 'Mexico',
            self::Moldova => 'Moldova',
            self::Morocco => 'Morocco',
            self::Netherlands => 'Netherlands',
            self::Nigeria => 'Nigeria',
            self::North_Macedonia => 'North Macedonia',
            self::Norway => 'Norway',
            self::Peru => 'Peru',
            self::Philippines => 'Philippines',
            self::Poland => 'Poland',
            self::Portugal => 'Portugal',
            self::Portugalia => 'Portugalia',
            self::Romania => 'Romania',
            self::Romania2 => 'Romania2',
            self::Saudi_Arabia => 'Saudi Arabia',
            self::Serbia => 'Serbia',
            self::Singapore => 'Singapore',
            self::Slovakia => 'Slovakia',
            self::Slovenia => 'Slovenia',
            self::South_Africa => 'South_Africa',
            self::South_Korea => 'South_Korea',
            self::Spain => 'Spain',
            self::Sri_Lanka => 'Sri_Lanka',
            self::Sweden => 'Sweden',
            self::Switzerland => 'Switzerland',
            self::Tanzania => 'Tanzania',
            self::Thailand => 'Thailand',
            self::Turkey => 'Turkey',
            self::Ukraine => 'Ukraine',
            self::United_Arab_Emirates => 'United_Arab_Emirates',
            self::United_Kingdom => 'United_Kingdom',
            self::United_States => 'United_States',
            self::Venezuela => 'Venezuela',
            self::Vietnam => 'Vietnam',
        };
    }
}





