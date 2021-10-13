<?php

use Illuminate\Database\Seeder;

class AccessUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("access_users")
        ->insert([
            'name' => 'pen.lymeng',
        ]);
    }
}
