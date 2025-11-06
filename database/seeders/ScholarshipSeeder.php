<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        $scholarships = [
            [
                'title' => 'Health Innovation Fellowship 2025',
                'description' => 'Full scholarship for graduate studies in health innovation and entrepreneurship, with mentorship and networking opportunities.',
                'program_type' => 'graduate',
                'eligibility_criteria' => 'Graduate students pursuing health innovation, entrepreneurship, or related fields. Must be from an African country.',
                'required_documents' => ['CV', 'Academic Transcript', 'Motivation Letter', 'Recommendation Letters'],
                'benefits' => ['Full tuition coverage', 'Monthly stipend', 'Mentorship program', 'Networking events', 'Research funding'],
                'coverage' => 'Full tuition + $1,500/month stipend',
                'amount' => 50000,
                'deadline' => '2025-12-31',
                'application_start_date' => '2025-09-01',
                'status' => 'open',
                'available_slots' => 10,
            ],
            [
                'title' => 'Undergraduate Research Grant',
                'description' => 'Research funding for undergraduate students conducting health-related research projects.',
                'program_type' => 'undergraduate',
                'eligibility_criteria' => 'Undergraduate students enrolled in health sciences programs. Must have a clear research proposal.',
                'required_documents' => ['CV', 'Transcript', 'Research Proposal', 'Recommendation Letter'],
                'benefits' => ['Research grant up to $5,000', 'Access to research facilities', 'Mentorship'],
                'coverage' => 'Up to $5,000 research grant',
                'amount' => 5000,
                'deadline' => '2026-01-15',
                'application_start_date' => '2025-11-01',
                'status' => 'upcoming',
                'available_slots' => 20,
            ],
            [
                'title' => 'Postgraduate Excellence Award',
                'description' => 'Merit-based scholarship for outstanding postgraduate students in health sciences.',
                'program_type' => 'postgraduate',
                'eligibility_criteria' => 'PhD or Masters students with exceptional academic records (GPA > 3.7). African nationals only.',
                'required_documents' => ['CV', 'Transcripts', 'Research Proposal', 'Two Recommendation Letters'],
                'benefits' => ['Tuition coverage', 'Research allowance', 'Conference attendance'],
                'coverage' => 'Full tuition + $2,000/month',
                'amount' => 75000,
                'deadline' => '2025-11-30',
                'status' => 'open',
                'available_slots' => 5,
            ],
        ];

        foreach ($scholarships as $scholarship) {
            Scholarship::create($scholarship);
        }
    }
}
