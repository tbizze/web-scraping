<?php

namespace Database\Seeders;

use App\Models\PessoaStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PessoaStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            'Regular',
            'Meio regular',
            'Irregular',
            'Cancelado'
        ];
        foreach ($list as $item) {
            PessoaStatus::create([
                'description' => $item,
            ]);
        }
    }
}
