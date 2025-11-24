<?php

use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

$factory->define(Tournament::class, function (Faker\Generator $faker) {
    $dateIni = $faker->dateTimeBetween('now', '+2 weeks');
    $dateFin = $faker->dateTimeBetween($dateIni, '+3 weeks');
    $venues = Venue::all()->pluck('id')->toArray();

    return [
        'user_id'           => factory(config('laravel-tournaments.user.model'))->create()->id,
        'slug'              => $faker->slug(2),
        'name'              => $faker->name,
        'dateIni'           => $dateIni->format('Y-m-d H:i:s'),
        'dateFin'           => $dateFin->format('Y-m-d H:i:s'),
        'registerDateLimit' => $dateIni->format('Y-m-d H:i:s'),
        'sport'             => 1,
        'type'              => $faker->boolean(),
        'venue_id'          => $faker->randomElement($venues),
    ];
});
