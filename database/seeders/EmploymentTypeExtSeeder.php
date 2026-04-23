<?php

namespace Database\Seeders;

use App\Models\EmploymentTypeExt;
use Illuminate\Database\Seeder;

class EmploymentTypeExtSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Job Order',
            'Contract of Service',
            'Plantilla',
        ];

        foreach ($types as $type) {
            EmploymentTypeExt::firstOrCreate(['type_name' => $type]);
        }
    }
}
