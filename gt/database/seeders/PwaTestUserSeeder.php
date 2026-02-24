<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Infrastructure\Models\User;
use App\Models\Santri;
use App\Models\Pjgt;

class PwaTestUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create GT Test User
        $gtUser = User::updateOrCreate(
            ['email' => 'gt@example.com'],
            [
                'name' => 'Ahmad GT',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $gtUser->syncRoles(['gt']);

        // Link with a Santri
        $santri = Santri::firstOrCreate(
            ['nis' => '12345'],
            [
                'nama' => 'Ahmad GT',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2000-01-01',
                'angkatan' => 2024,
                'status_tugas' => 'Sedang Bertugas'
            ]
        );
        $santri->update(['user_id' => $gtUser->id]);

        // 2. Create PJGT Test User
        $pjgtUser = User::updateOrCreate(
            ['email' => 'pjgt@example.com'],
            [
                'name' => 'Haji Mansur PJGT',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $pjgtUser->syncRoles(['pjgt']);

        $pjgt = Pjgt::firstOrCreate(
            ['nama' => 'Haji Mansur PJGT'],
            [
                'no_hp' => '08123456789'
            ]
        );
        $pjgt->update(['user_id' => $pjgtUser->id]);

        // 3. Create Korwil Test User (Korwil is a PJGT with korwil role)
        $korwilUser = User::updateOrCreate(
            ['email' => 'korwil@example.com'],
            [
                'name' => 'Ustadz Zaini Korwil',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $korwilUser->syncRoles(['korwil']);

        $korwilPjgt = Pjgt::firstOrCreate(
            ['nama' => 'Ustadz Zaini Korwil'],
            [
                'no_hp' => '08987654321'
            ]
        );
        $korwilPjgt->update(['user_id' => $korwilUser->id]);
    }
}
