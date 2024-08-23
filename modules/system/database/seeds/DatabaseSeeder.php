<?php

namespace System\Database\Seeds;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Winter\Storm\Database\Updates\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguarded(function () {
            $this->call('System\Database\Seeds\SeedSetupMailLayouts');
        });
    }
}
