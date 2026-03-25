<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          Doctor::create([
            'name' => 'Dr Ahmed Benali',
            'specialty' => 'Cardiologue',
            'city' => 'Casablanca',
            'yearsofexperience' => 12,
            'consultation_price' => 300,
            'available_days' => 'Monday, Wednesday, Friday',
        ]);

        Doctor::create([
            'name' => 'Dr Sara El Amrani',
            'specialty' => 'Généraliste',
            'city' => 'Rabat',
            'yearsofexperience' => 8,
            'consultation_price' => 200,
            'available_days' => 'Tuesday, Thursday',
        ]);
        Doctor::create([
            'name' => 'Dr Youssef Tazi',
            'specialty' => 'Dermatologue',
            'city' => 'Fes',
            'yearsofexperience' => 10,
            'consultation_price' => 250,
            'available_days' => 'Monday, Saturday',
        ]);

        Doctor::create([
            'name' => 'Dr Lina Berrada',
            'specialty' => 'Pédiatre',
            'city' => 'Marrakech',
            'yearsofexperience' => 7,
            'consultation_price' => 220,
            'available_days' => 'Wednesday, Friday',
        ]);
    }
    }

