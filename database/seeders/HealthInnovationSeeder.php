<?php

namespace Database\Seeders;

use App\Models\Venture;
use App\Models\VentureUpdate;
use Illuminate\Database\Seeder;

class HealthInnovationSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample ventures
        $ventures = [
            [
                'name' => 'MediConnect Africa',
                'tagline' => 'Connecting patients with healthcare providers',
                'description' => 'A telemedicine platform that connects patients across Africa with qualified healthcare professionals through video consultations, chat, and AI-powered symptom assessment.',
                'focus_area' => 'telemedicine',
                'stage' => 'growth',
                'founded_year' => 2021,
                'country' => 'Kenya',
                'website' => 'https://mediconnect.example.com',
                'social_links' => ['twitter' => '@mediconnect', 'linkedin' => 'mediconnect-africa'],
                'founders' => 'Dr. Amina Osman (CEO), John Kamau (CTO)',
                'team_size' => 15,
                'funding_raised' => 1200000,
                'patients_impacted' => 50000,
                'countries_reached' => 5,
                'votes_count' => 125,
                'featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'PharmaTrack',
                'tagline' => 'Fighting counterfeit drugs with blockchain',
                'description' => 'Using blockchain technology to track pharmaceutical supply chains and ensure drug authenticity across Africa.',
                'focus_area' => 'pharmaceuticals',
                'stage' => 'early-stage',
                'founded_year' => 2022,
                'country' => 'Nigeria',
                'website' => 'https://pharmatrack.example.com',
                'founders' => 'Chioma Adebayo (CEO), Ibrahim Mohammed (CTO)',
                'team_size' => 8,
                'funding_raised' => 500000,
                'patients_impacted' => 10000,
                'countries_reached' => 3,
                'votes_count' => 87,
                'featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'MindHealth',
                'tagline' => 'Mental health support at your fingertips',
                'description' => 'Mobile app providing accessible mental health counseling, therapy sessions, and wellness resources tailored for African communities.',
                'focus_area' => 'mental-health',
                'stage' => 'growth',
                'founded_year' => 2020,
                'country' => 'South Africa',
                'founders' => 'Dr. Sarah Mbeki (CEO)',
                'team_size' => 12,
                'funding_raised' => 800000,
                'patients_impacted' => 30000,
                'countries_reached' => 4,
                'votes_count' => 156,
                'featured' => false,
                'status' => 'active',
            ],
            [
                'name' => 'DiagnoAI',
                'tagline' => 'AI-powered disease diagnosis',
                'description' => 'Artificial intelligence platform for early disease detection and diagnosis, specializing in malaria, TB, and tropical diseases.',
                'focus_area' => 'diagnostics',
                'stage' => 'prototype',
                'founded_year' => 2023,
                'country' => 'Uganda',
                'founders' => 'Peter Okello (CEO), Grace Nakato (Chief Medical Officer)',
                'team_size' => 6,
                'patients_impacted' => 2000,
                'countries_reached' => 2,
                'votes_count' => 43,
                'featured' => false,
                'status' => 'active',
            ],
        ];

        foreach ($ventures as $ventureData) {
            $venture = Venture::create($ventureData);

            // Add some updates for each venture
            VentureUpdate::create([
                'venture_id' => $venture->id,
                'title' => 'Milestone Achieved!',
                'content' => "We're excited to announce that we've reached {$venture->patients_impacted} patients served!",
                'update_type' => 'milestone',
                'likes_count' => rand(10, 50),
                'comments_count' => rand(2, 15),
            ]);

            if ($venture->funding_raised > 0) {
                VentureUpdate::create([
                    'venture_id' => $venture->id,
                    'title' => 'Funding Round Closed',
                    'content' => "Successfully raised $" . number_format($venture->funding_raised) . " to scale our operations across Africa!",
                    'update_type' => 'funding',
                    'likes_count' => rand(20, 80),
                    'comments_count' => rand(5, 20),
                ]);
            }
        }
    }
}
