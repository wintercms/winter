<?php

namespace System\Database\Seeds;

use System\Models\MailLayout;
use Winter\Storm\Database\Updates\Seeder;

class SeedSetupMailLayouts extends Seeder
{
    public function run()
    {
        MailLayout::createLayouts();
    }
}
