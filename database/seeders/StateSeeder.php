<?php
namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $colombia = Country::where('code', 'CO')->first();
        $ecuador = Country::where('code', 'EC')->first();

        $states = [
            ['country_id' => $colombia->id, 'name' => 'Valle del Cauca', 'code' => 'VC'],
            ['country_id' => $colombia->id, 'name' => 'Cundinamarca', 'code' => 'CU'],

            ['country_id' => $ecuador->id, 'name' => 'Sucumbíos', 'code' => 'SU'],
            ['country_id' => $ecuador->id, 'name' => 'Pichincha', 'code' => 'PI'],
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                [
                    'country_id' => $state['country_id'],
                    'name' => $state['name'],
                ],
                $state
            );
        }
    }
}
