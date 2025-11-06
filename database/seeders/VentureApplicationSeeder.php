<?php

namespace Database\Seeders;

use App\Models\VentureApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VentureApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'venture_name' => 'HealthTech Solutions',
                'tagline' => 'Revolutionizing healthcare through technology',
                'description' => 'A digital health platform connecting patients with specialized healthcare providers through AI-powered triage and telemedicine.',
                'focus_area' => 'health-tech',
                'stage' => 'growth',
                'founded_year' => 2023,
                'country' => 'Kenya',
                'website' => 'https://healthtechsolutions.africa',
                'contact_name' => 'Dr. Jane Muthoni',
                'contact_email' => 'jane@healthtechsolutions.africa',
                'contact_phone' => '+254712345678',
                'founders' => 'Dr. Jane Muthoni, John Kamau, Sarah Achieng',
                'team_size' => 15,
                'team_description' => 'Our team consists of experienced healthcare professionals and technology experts dedicated to improving healthcare access in Africa.',
                'problem_statement' => 'Limited access to specialized healthcare in rural and underserved areas leads to delayed diagnosis and treatment.',
                'solution_description' => 'Our platform connects patients with specialists through telemedicine, reducing wait times and improving healthcare outcomes.',
                'target_market' => 'Patients in sub-Saharan Africa requiring specialized healthcare services',
                'unique_value_proposition' => 'AI-powered triage system that matches patients with the most appropriate specialist based on their condition.',
                'current_stage_description' => 'Currently operating in 3 countries with over 100 healthcare providers on our platform.',
                'patients_served' => 5000,
                'revenue_generated' => 250000.00,
                'funding_raised' => 1500000.00,
                'key_milestones' => 'Launched MVP in 2023, Reached 1000 patients in first 6 months, Expanded to 2 additional countries in 2024',
                'funding_sought' => 500000.00,
                'use_of_funds' => 'Expanding to 5 more countries, Enhancing AI capabilities, Marketing and user acquisition',
                'pitch_deck' => 'https://example.com/pitch-deck.pdf',
                'business_plan' => 'https://example.com/business-plan.pdf',
                'financial_projections' => 'https://example.com/financials.pdf',
                'why_apply' => 'We believe this program will provide us with the mentorship and network needed to scale our solution across Africa.',
                'additional_info' => 'Winner of the 2024 Africa Health Innovation Award',
                'status' => 'submitted',
                'submitted_at' => now(),
                'user_id' => 1, // Assuming user with ID 1 exists
            ],
            [
                'venture_name' => 'MediAI Diagnostics',
                'tagline' => 'AI-powered diagnostic solutions for early disease detection',
                'description' => 'Leveraging artificial intelligence to provide accurate and affordable diagnostic solutions for early detection of chronic diseases.',
                'focus_area' => 'diagnostics',
                'stage' => 'early-stage',
                'founded_year' => 2024,
                'country' => 'Nigeria',
                'website' => 'https://mediai.diagnostics',
                'contact_name' => 'Dr. Adebayo Ojo',
                'contact_email' => 'adebayo@mediadiagnostics.com',
                'contact_phone' => '+2348012345678',
                'founders' => 'Dr. Adebayo Ojo, Ngozi Okonkwo, Femi Balogun',
                'team_size' => 8,
                'team_description' => 'A team of AI researchers, medical doctors, and software engineers working to revolutionize diagnostics in Africa.',
                'problem_statement' => 'Late diagnosis of chronic diseases leads to poor health outcomes and higher treatment costs.',
                'solution_description' => 'Our AI-powered diagnostic tool helps detect chronic diseases at early stages using basic medical tests and imaging.',
                'target_market' => 'Primary healthcare centers and diagnostic labs in sub-Saharan Africa',
                'unique_value_proposition' => 'Our solution provides 95% accuracy in detecting early signs of chronic diseases at a fraction of the cost of traditional methods.',
                'current_stage_description' => 'Pilot phase with 5 healthcare centers in Lagos, Nigeria',
                'patients_served' => 1200,
                'revenue_generated' => 75000.00,
                'funding_raised' => 300000.00,
                'key_milestones' => 'Developed MVP, Completed initial pilot testing, Secured first paying customers',
                'funding_sought' => 250000.00,
                'use_of_funds' => 'Product development, Clinical validation studies, Team expansion',
                'why_apply' => 'We need mentorship in healthcare regulations and scaling our operations across Africa.',
                'status' => 'submitted',
                'submitted_at' => now()->subDays(15),
                'user_id' => 2, // Assuming user with ID 2 exists
            ]
        ];

        foreach ($applications as $application) {
            VentureApplication::create($application);
        }
    }
}
