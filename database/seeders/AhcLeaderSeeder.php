<?php

namespace Database\Seeders;

use App\Models\AhcLeader;
use Illuminate\Database\Seeder;

class AhcLeaderSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = [
            [
                'name' => 'Dr Dawit Wondimagegn',
                'position' => 'Project Principal Investigator',
                'image' => '/images/leaders/dawit.jpg',
                'description' => 'Associate Professor, Addis Ababa University Consultant Psychiatrist, Tikor Anbessa Hospital Associate professor,Addis Ababa University, Co-chair and Director, Toronto Addis Ababa Academic Collaboration-TAAAC National Lead, African Health Observatory Platform-Ethiopia National Centre.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Assefa Abegaz',
                'position' => 'Co-Principal Investigator/ Manager',
                'image' => '/images/leaders/pro.assefa.jpg',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Prof.Damen H/Mariam',
                'position' => 'Health Ecosystem Lead',
                'image' => '/images/leaders/pro.damen.jpg',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged..',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Anteneh Belete',
                'position' => 'Health Employment Pillar Lead',
                'image' => '/images/leaders/pro.anteneh.jpg',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged..',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Kebede Wondu',
                'position' => 'Health Entrepreneurship Pillar Lead',
                'image' => '/images/leaders/kebede wondu.jpg',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged..',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($leaders as $leader) {
            AhcLeader::updateOrCreate(
                ['name' => $leader['name']],
                $leader
            );
        }
    }
}
