<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'user1',
                'email' => 'user1@test.test',
                'password' => '$2y$10$RCMPJnIZtXaHbMiolKpi6eYVJCfN8huf5AMbOQSND7oERQJKMz4rK',
                'is_admin' => true,
                'max_amount' => 100
            ],[
                'name' => 'user2',
                'email' => 'user2@test.test',
                'password' => '$2y$10$RCMPJnIZtXaHbMiolKpi6eYVJCfN8huf5AMbOQSND7oERQJKMz4rK',
                'is_admin' => false,
                'max_amount' => 20
            ],[
                'name' => 'user3',
                'email' => 'user3@test.test',
                'password' => '$2y$10$RCMPJnIZtXaHbMiolKpi6eYVJCfN8huf5AMbOQSND7oERQJKMz4rK',
                'is_admin' => false,
                'max_amount' => 10
            ]
        ]);
    }
}
