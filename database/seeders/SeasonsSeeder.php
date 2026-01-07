<?php

namespace Database\Seeders;

use App\Models\V5\Season;
use Illuminate\Database\Seeder;

class SeasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'id' => 1,
                'name' => '2019/2020',
                'organizer_id' => 1,
            ],
            [
                'id' => 2,
                'name' => '2020/2021',
                'organizer_id' => 1,
            ],
            [
                'id' => 3,
                'name' => '2020/2021',
                'organizer_id' => 3,
            ],
            [
                'id' => 4,
                'name' => '2020/2021',
                'organizer_id' => 4,
            ],
            [
                'id' => 5,
                'name' => '2021/2022',
                'organizer_id' => 1,
            ],
            [
                'id' => 6,
                'name' => '2021/2022',
                'organizer_id' => 3,
            ],
            [
                'id' => 7,
                'name' => '2021/2022',
                'organizer_id' => 4,
            ],
            [
                'id' => 8,
                'name' => '2021/2022',
                'organizer_id' => 5,
            ],
            [
                'id' => 9,
                'name' => '2021/2022',
                'organizer_id' => 6,
            ],
            [
                'id' => 10,
                'name' => '2021/2022',
                'organizer_id' => 7,
            ],
            [
                'id' => 11,
                'name' => '2022/2023',
                'organizer_id' => 1,
            ],
            [
                'id' => 12,
                'name' => '2022/2023',
                'organizer_id' => 3,
            ],
            [
                'id' => 13,
                'name' => '2022/2023',
                'organizer_id' => 4,
            ],
            [
                'id' => 14,
                'name' => '2022/2023',
                'organizer_id' => 6,
            ],
            [
                'id' => 15,
                'name' => '2022/2023',
                'organizer_id' => 7,
            ],
            [
                'id' => 16,
                'name' => '2022/2023',
                'organizer_id' => 8,
            ],
            [
                'id' => 17,
                'name' => '2023/2024',
                'organizer_id' => 1,
            ],
            [
                'id' => 18,
                'name' => '2023/2024',
                'organizer_id' => 4,
            ],
            [
                'id' => 19,
                'name' => '2024/2025',
                'organizer_id' => 1,
            ],
            [
                'id' => 20,
                'name' => '2024/2025',
                'organizer_id' => 4,
            ],
        ];

        foreach ($seasons as $season) {
            Season::updateOrCreate(
                ['id' => $season['id']],
                $season
            );
        }
    }
}

