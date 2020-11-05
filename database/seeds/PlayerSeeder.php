<?php

use Illuminate\Database\Seeder;
use App\Laravue\Models\Player;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $playerList = array(
            array(
                'first_name' => 'Albert',
                'last_name' => 'Einstein',
                'email' => 'albert.einstein@gmail.com',
                'team' => 1
            ),
            array(
                'first_name' => 'Blaise',
                'last_name' => 'Pascal',
                'email' => 'blaise.pascal@gmail.com',
                'team' => 2
            ),
            array(
                'first_name' => 'Caroline',
                'last_name' => 'Herschel',
                'email' => 'caroline.herschel@gmail.com',
                'team' => 1
            ),
            array(
                'first_name' => 'Dorothy',
                'last_name' => 'Hodgkin',
                'email' => 'dorothy.hodgkin@gmail.com',
                'team' => 2
            ),
            array(
                'first_name' => 'Edmond',
                'last_name' => 'Halley',
                'email' => 'edmond.halley@gmail.com',
                'team' => 1
            ),
            array(
                'first_name' => 'Enrico',
                'last_name' => 'Fermi',
                'email' => 'enrico.fermi@gmail.com',
                'team' => 2
            )
        );

        foreach($playerList as $player) {
            Player::create([
                'first_name' => $player['first_name'],
                'last_name' => $player['last_name'],
                'email' => $player['email'],
                'team' => $player['team']
            ]);
        }
    }
}
