<?php

use Illuminate\Database\Seeder;
use App\Laravue\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teamList = [
            'Team 1',
            'Team 2'
        ];

        foreach($teamList as $team) {
            Team::create([
                'name' => $team
            ]);
        }
    }
}
