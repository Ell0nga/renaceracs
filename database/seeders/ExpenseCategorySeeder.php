<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Marketing',
            'Sueldos',
            'Materiales',
            'Arriendo',
            'Servicios Básicos',
            'Transporte',
            'Otros'
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category]);
        }
    }
}
