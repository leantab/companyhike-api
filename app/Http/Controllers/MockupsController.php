<?php

namespace App\Http\Controllers;

use Faker\Factory;
use Illuminate\Http\Request;

class MockupsController extends Controller
{
    public function get_user()
    {
        $faker = Factory::create();
        $arr = [
            'user_id' => $faker->numberBetween(1, 399),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'email' => $faker->email(),
            'avatar' => $faker->imageUrl(),
            'is_admin' => $faker->randomElement([0, 1]),
            'first_login' => $faker->randomElement([true, false]),
            'language' => $faker->randomElement(['en', 'es', 'pt']),
            'communities' => [
                [
                    'id' => $faker->numberBetween(1, 99),
                    'name' => $faker->company(),
                    'logo' => $faker->imageUrl(),
                    'is_admin' => $faker->randomElement([true, false]),
                ]

            ]
        ];
        return response()->json($arr);
    }

    public function match_schema()
    {
        return response('{"version":"1.0","match_parameters":{"name":{"required":true,"type":"string","min_length":3,"max_length":500},"players":{"required":true,"type":"integer","min":4,"max":16},"proficiency_rate":{"required":true,"type":"options","options":["proficiency_trainee","proficiency_junior","proficiency_semi_senior","proficiency_senior","proficiency_big_company_ceo"]},"industry":{"required":true,"type":"options","options":["cars"]},"type":{"required":true,"type":"options","options":["scenario","country","custom"]},"scenario":{"required_if":"type:scenario","type":"options","options":["coronavirus_2020","argentina_crisis_2001"]},"country":{"required_if":"type:country","type":"options","options":["AR"]},"stages":{"required_if":"type:country,custom","type":"integer","min":4,"max":8},"country_income_level":{"required_if":"type:custom","type":"options","options":["country_income_low_income","country_income_lower_middle_income","country_income_upple_middle_income","country_income_high_income"]},"industry_status":{"required_if":"type:country,custom","type":"options","options":["industry_status_war_prices","industry_status_demanding_customes","industry_status_constant_development","industry_status_faithful_clients","random"]},"accounting_period":{"required_if":"type:country,custom","type":"options","options":[1,2,3,4,6]},"positive_random_events":{"required_if":"type:country,custom","type":"options","options":["positive_events_none","positive_events_few","positive_events_medium","positive_events_full","random"]},"risk_limit_min":{"required_if":"type:country,custom","type":"integer","min":10,"max":75,"rule":[["self","<=","risk_limit_max"]]},"risk_limit_max":{"required_if":"type:country,custom","type":"integer","min":10,"max":75,"rule":[["self",">=","risk_limit_min"]]},"initial_eq":{"required_if":"type:country,custom","type":"integer","min":-10,"max":10},"government_side":{"required_if":"type:country,custom","type":"options","options":["government_side_liberal","government_side_moderate","government_side_interventionist","custom"]},"profit_tax":{"required_if":"government_side:custom","type":"integer","min":0,"max":45},"vat_tax":{"required_if":"government_side:custom","type":"integer","min":0,"max":28},"labor_tax":{"required_if":"government_side:custom","type":"integer","min":0,"max":15},"easy_business_score":{"required_if":"government_side:custom","type":"options","options":["easy_business_low","easy_business_medium_low","easy_business_medium_high","easy_business_high","random"]},"compensation_cost":{"required_if":"government_side:custom","type":"integer","min":1,"max":5},"interest_rate":{"required_if":"government_side:custom","type":"integer","min":3,"max":30},"financial_cost":{"required_if":"government_side:custom","type":"integer","rule":[["self","<=","interest_rate"]]}},"government_parameters":{"demand_pull":{"required":true,"type":"integer","min":-10,"max":10},"price_control":{"required":true,"type":"integer","min":0,"max":100},"interest_rate":{"required":true,"type":"options","min":3,"max":30},"financial_cost":{"required":true,"type":"integer","rule":[["self","<","demand_pull"]]}},"ceo_parameters":{"price":{"required":true,"type":"integer","min":0,"max":1000},"production_level":{"required":true,"type":"integer","min":0,"max":100}}}');
    }

    public function match_list($community_id = null)
    {
        $faker = Factory::create();
        $arr = [
            "matches" => [
                [
                    "match_id" => 75,
                    "name" => 'USAL Partida 27',
                    "type" => 'Conqueror',
                    "player_position" => 0,
                    "current_stage" => 0,
                    "is_admin" => 0,
                    "created_at" => '2020-12-27 15:34:17'
                ],
                [
                    "match_id" => 58,
                    "name" => 'USAL Partida 5',
                    "type" => 'Escenario - Argentina 2001',
                    "player_position" => 3,
                    "current_stage" => 4,
                    "is_admin" => 1,
                    "created_at" => '2020-12-09 16:31:51'
                ]
            ]
        ];
        return response()->json($arr);
    }

    public function match_preview($id)
    {
        $faker = Factory::create();
        $arr = [
            "match" => [
                "match_id" => $id,
                "name" => $faker->company(),
                "type" => $faker->companySuffix(),
                "player_position" => $faker->numberBetween(1, 18),
                "current_stage" => $faker->numberBetween(1, 6),
                "is_admin" => $faker->randomElement([true, false]),
                "created_at" => $faker->date('d/m/Y H:i:s')
            ]
        ];
        return response()->json($arr);
    }

    public function match_info($id)
    {
        $faker = Factory::create();
        $arr = [
            "match" => [
                "match_id" => $id,
                "name" => $faker->company(),
                "type" => $faker->companySuffix(),
                "player_position" => $faker->numberBetween(1, 6),
                "current_stage" => $faker->numberBetween(1, 6),
                "is_admin" => $faker->randomElement([true, false]),
                "created_at" => $faker->date('d/m/Y H:i:s'),
                'player_history' => [
                    'round 1' => '',
                    'round 2' => '',
                    'round 3' => '',
                    'round 4' => '',
                    'round 5' => '',
                    'round 6' => '',
                ],
                'other_players' => [
                    [
                        'id' => $faker->numberBetween(1, 998),
                        'name' => $faker->company(),
                        'position' => $faker->numberBetween(1, 6),
                    ],
                    [
                        'id' => $faker->numberBetween(1, 998),
                        'name' => $faker->company(),
                        'position' => $faker->numberBetween(1, 6),
                    ],
                    [
                        'id' => $faker->numberBetween(1, 998),
                        'name' => $faker->company(),
                        'position' => $faker->numberBetween(1, 6),
                    ],
                    [
                        'id' => $faker->numberBetween(1, 998),
                        'name' => $faker->company(),
                        'position' => $faker->numberBetween(1, 6),
                    ],
                    [
                        'id' => $faker->numberBetween(1, 998),
                        'name' => $faker->company(),
                        'position' => $faker->numberBetween(1, 6),
                    ],
                ],
            ]
        ];
        return response()->json($arr);
    }

    public function community_members($id)
    {
        $faker = Factory::create();
        $arr = [];
        for ($i = 0; $i < 8; $i++) {
            $arr['members'][] = [
                'id' => $faker->numberBetween(1, 9999),
                'name' => $faker->name() . $faker->lastName(),
                'email' => $faker->email(),
            ];
        }

        return response()->json($arr);
    }
}
