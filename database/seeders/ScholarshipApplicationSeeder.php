<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipApplicationStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ScholarshipApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Create test applicants if they don't exist
        $applicants = $this->createTestApplicants();

        // Get existing scholarships
        $scholarships = Scholarship::all();

        if ($scholarships->isEmpty()) {
            $this->command->warn('No scholarships found. Please run ScholarshipSeeder first.');
            return;
        }

        // Sample application data templates
        $applicationTemplates = $this->getApplicationTemplates();

        // Create applications for each template
        foreach ($applicationTemplates as $index => $template) {
            $applicant = $applicants[$index % count($applicants)];
            $scholarship = $scholarships->random();

            $application = ScholarshipApplication::create([
                'scholarship_id' => $scholarship->id,
                'user_id' => $applicant->id,
                'first_name' => $template['first_name'],
                'last_name' => $template['last_name'],
                'email' => $template['email'],
                'phone' => $template['phone'],
                'date_of_birth' => $template['date_of_birth'],
                'nationality' => $template['nationality'],
                'country_of_residence' => $template['country_of_residence'],
                'address' => $template['address'],
                'current_education_level' => $template['current_education_level'],
                'institution_name' => $template['institution_name'],
                'field_of_study' => $template['field_of_study'],
                'gpa' => $template['gpa'],
                'graduation_year' => $template['graduation_year'],
                'academic_achievements' => $template['academic_achievements'],
                'research_area' => $template['research_area'],
                'concept_note' => $template['concept_note'],
                'research_proposal' => $template['research_proposal'],
                'motivation_letter' => $template['motivation_letter'],
                'career_goals' => $template['career_goals'],
                'why_this_scholarship' => $template['why_this_scholarship'],
                'financial_need_description' => $template['financial_need_description'],
                'current_funding_sources' => $template['current_funding_sources'],
                'reference_1_name' => $template['reference_1_name'],
                'reference_1_email' => $template['reference_1_email'],
                'reference_1_relationship' => $template['reference_1_relationship'],
                'reference_2_name' => $template['reference_2_name'],
                'reference_2_email' => $template['reference_2_email'],
                'reference_2_relationship' => $template['reference_2_relationship'],
                'additional_info' => $template['additional_info'],
                'status' => $template['status'],
                'submitted_at' => $template['submitted_at'],
            ]);

            // Add status history
            if ($application->status !== 'draft') {
                ScholarshipApplicationStatusHistory::create([
                    'application_id' => $application->id,
                    'status' => 'submitted',
                    'note' => 'Application submitted by applicant',
                    'timestamp' => $application->submitted_at,
                ]);

                if (in_array($application->status, ['under-review', 'shortlisted', 'interviewed', 'accepted', 'rejected'])) {
                    ScholarshipApplicationStatusHistory::create([
                        'application_id' => $application->id,
                        'status' => 'under-review',
                        'note' => 'Application moved to under review',
                        'timestamp' => $application->submitted_at->addDays(2),
                    ]);
                }

                if (in_array($application->status, ['shortlisted', 'interviewed', 'accepted'])) {
                    ScholarshipApplicationStatusHistory::create([
                        'application_id' => $application->id,
                        'status' => 'shortlisted',
                        'note' => 'Applicant shortlisted for interview',
                        'timestamp' => $application->submitted_at->addDays(7),
                    ]);
                }

                if ($application->status === 'rejected') {
                    ScholarshipApplicationStatusHistory::create([
                        'application_id' => $application->id,
                        'status' => 'rejected',
                        'note' => 'Application does not meet minimum requirements',
                        'timestamp' => $application->submitted_at->addDays(5),
                    ]);
                }
            }

            $this->command->info("Created application for {$template['first_name']} {$template['last_name']}");
        }

        $this->command->info('Scholarship applications seeded successfully!');
    }

    private function createTestApplicants(): array
    {
        $applicants = [];

        $testUsers = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@example.com', 'username' => 'john.smith'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.johnson@example.com', 'username' => 'sarah.johnson'],
            ['first_name' => 'Michael', 'last_name' => 'Chen', 'email' => 'michael.chen@example.com', 'username' => 'michael.chen'],
            ['first_name' => 'Amina', 'last_name' => 'Hassan', 'email' => 'amina.hassan@example.com', 'username' => 'amina.hassan'],
            ['first_name' => 'David', 'last_name' => 'Okonkwo', 'email' => 'david.okonkwo@example.com', 'username' => 'david.okonkwo'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'username' => $userData['username'],
                    'password' => Hash::make('password'),
                ]
            );
            $applicants[] = $user;
        }

        return $applicants;
    }

    private function getApplicationTemplates(): array
    {
        return [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+254712345678',
                'date_of_birth' => '1998-05-15',
                'nationality' => 'Kenyan',
                'country_of_residence' => 'Kenya',
                'address' => '123 Nairobi Road, Nairobi, Kenya',
                'current_education_level' => 'undergraduate',
                'institution_name' => 'University of Nairobi',
                'field_of_study' => 'Public Health',
                'gpa' => '3.8',
                'graduation_year' => 2026,
                'academic_achievements' => "Dean's List for 3 consecutive semesters\nPublished research paper in Journal of African Health Sciences\nPresident of Public Health Students Association\nVolunteer at Kenyatta National Hospital",
                'research_area' => 'Maternal and Child Health',
                'concept_note' => 'My research focuses on improving maternal health outcomes in rural Kenyan communities through community-based interventions and mobile health technologies.',
                'research_proposal' => "Title: Mobile Health Intervention for Maternal Health in Rural Kenya\n\nBackground: Maternal mortality remains a significant challenge in rural Kenya, with limited access to healthcare facilities and prenatal care.\n\nObjectives: To develop and test a mobile health application that provides prenatal care information, appointment reminders, and emergency contact systems for pregnant women in rural areas.\n\nMethodology: Mixed-methods approach combining quantitative surveys and qualitative interviews with 200 pregnant women across 5 rural counties.\n\nExpected Outcomes: Improved prenatal care attendance, reduced maternal complications, and increased health literacy among participants.",
                'motivation_letter' => "Growing up in a rural village, I witnessed firsthand the challenges pregnant women face in accessing quality healthcare. My mother lost her sister during childbirth due to complications that could have been prevented with proper prenatal care. This tragedy shaped my determination to pursue public health and work towards reducing maternal mortality in underserved communities.\n\nThrough my undergraduate studies, I have developed a strong foundation in epidemiology, health systems management, and community health. I have volunteered at local health clinics where I've seen the impact of health education and early intervention on maternal outcomes.\n\nThis scholarship would enable me to advance my research on mobile health solutions and contribute to improving maternal health outcomes in Kenya and across Africa.",
                'career_goals' => 'To become a leading public health researcher focused on maternal and child health in Africa, developing innovative, technology-driven solutions to improve healthcare access in rural communities.',
                'why_this_scholarship' => "This scholarship aligns perfectly with my research interests in health innovation and community-based interventions. The mentorship opportunities and networking with health innovators would be invaluable for my career development. Additionally, the funding would allow me to focus entirely on my research without financial constraints.",
                'financial_need_description' => 'I come from a low-income family. My parents are subsistence farmers who struggle to provide for our family of 7 siblings. I currently support my education through part-time work and small scholarships, but I need additional funding to complete my degree and pursue research.',
                'current_funding_sources' => 'Part-time tutoring (covers ~30% of expenses), Small university scholarship (covers tuition only)',
                'reference_1_name' => 'Dr. Grace Mwangi',
                'reference_1_email' => 'g.mwangi@uonbi.ac.ke',
                'reference_1_relationship' => 'Academic Supervisor',
                'reference_2_name' => 'Prof. James Kibet',
                'reference_2_email' => 'j.kibet@uonbi.ac.ke',
                'reference_2_relationship' => 'Department Head',
                'additional_info' => 'I have strong programming skills (Python, R) and experience with data analysis tools which would be valuable for implementing mobile health solutions.',
                'status' => 'submitted',
                'submitted_at' => now()->subDays(10),
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'phone' => '+234803456789',
                'date_of_birth' => '1996-08-22',
                'nationality' => 'Nigerian',
                'country_of_residence' => 'Nigeria',
                'address' => '45 Victoria Island, Lagos, Nigeria',
                'current_education_level' => 'graduate',
                'institution_name' => 'University of Lagos',
                'field_of_study' => 'Health Economics',
                'gpa' => '3.9',
                'graduation_year' => 2025,
                'academic_achievements' => "First Class Honours in Economics\nRecipient of USAID Research Fellowship\nPresented at African Health Economics Conference 2024\nPublished 2 peer-reviewed articles on health financing",
                'research_area' => 'Universal Health Coverage and Health Insurance',
                'concept_note' => 'Examining the sustainability and effectiveness of community-based health insurance schemes in achieving universal health coverage in Nigeria.',
                'research_proposal' => "Title: Evaluating Community-Based Health Insurance for Universal Health Coverage in Nigeria\n\nResearch Question: How can community-based health insurance schemes be optimized to achieve sustainable universal health coverage in Nigeria?\n\nMethodology: Comparative analysis of 10 CBHI schemes across 6 Nigerian states, examining enrollment rates, financial sustainability, and health outcomes over 5 years.\n\nSignificance: Findings will inform policy recommendations for scaling CBHI as a pathway to UHC in Nigeria and similar African contexts.",
                'motivation_letter' => "As Nigeria strives to achieve universal health coverage by 2030, I am passionate about contributing to sustainable health financing solutions. My research experience with the National Health Insurance Scheme revealed significant gaps in coverage, particularly for informal sector workers and rural populations.\n\nCommunity-based health insurance offers a promising model for extending coverage to these underserved groups. Through my graduate studies, I aim to develop evidence-based recommendations for policymakers on optimizing CBHI schemes.\n\nThis scholarship would provide the financial support and research resources necessary to conduct rigorous fieldwork across multiple Nigerian states.",
                'career_goals' => 'To work with international health organizations and African governments in designing and implementing sustainable health financing systems that achieve universal health coverage.',
                'why_this_scholarship' => 'The scholarship\'s focus on health innovation aligns with my research on innovative financing mechanisms. The networking opportunities with health economists and policymakers would be invaluable for my research and career trajectory.',
                'financial_need_description' => 'I am currently self-funded through my graduate program while supporting my younger siblings\' education. The research component of my thesis requires extensive travel and data collection across 6 states, which is financially challenging.',
                'current_funding_sources' => 'Teaching assistant stipend (insufficient for research costs), Family contribution (minimal)',
                'reference_1_name' => 'Prof. Oluwaseun Adeyemi',
                'reference_1_email' => 'o.adeyemi@unilag.edu.ng',
                'reference_1_relationship' => 'Thesis Supervisor',
                'reference_2_name' => 'Dr. Chinwe Okafor',
                'reference_2_email' => 'c.okafor@nhis.gov.ng',
                'reference_2_relationship' => 'Professional Mentor (NHIS)',
                'additional_info' => 'Proficient in econometric analysis using STATA and R. Experience in survey design and implementation.',
                'status' => 'under-review',
                'submitted_at' => now()->subDays(15),
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@example.com',
                'phone' => '+27821234567',
                'date_of_birth' => '1994-11-30',
                'nationality' => 'South African',
                'country_of_residence' => 'South Africa',
                'address' => '78 Medical School Road, Cape Town, South Africa',
                'current_education_level' => 'postgraduate',
                'institution_name' => 'University of Cape Town',
                'field_of_study' => 'Biomedical Engineering',
                'gpa' => '4.0',
                'graduation_year' => 2024,
                'academic_achievements' => "PhD candidate with perfect GPA\nInvented low-cost diagnostic device for TB detection\nWinner of African Innovation Challenge 2024\nPublished in Nature Biomedical Engineering\n3 patent applications filed",
                'research_area' => 'Point-of-care diagnostics for infectious diseases',
                'concept_note' => 'Developing affordable, rapid diagnostic tools for tuberculosis and other infectious diseases using microfluidic technology and smartphone integration.',
                'research_proposal' => "Title: Low-Cost Microfluidic Diagnostic Platform for TB Detection\n\nInnovation: A smartphone-based diagnostic device costing <$50 that can detect tuberculosis in 15 minutes using saliva samples.\n\nTechnical Approach: Microfluidic chip with LAMP amplification, combined with smartphone image analysis using machine learning algorithms.\n\nImpact: Enable TB diagnosis in remote areas without laboratory infrastructure, reducing time to treatment initiation from weeks to hours.\n\nCommercial Potential: Target market includes rural clinics across Sub-Saharan Africa. Manufacturing partnerships established with local companies.",
                'motivation_letter' => "The TB burden in Africa remains unacceptably high, partly due to limited access to rapid, affordable diagnostics. Current PCR-based tests are expensive and require sophisticated laboratory infrastructure unavailable in rural areas.\n\nMy PhD research has led to the development of a low-cost, smartphone-based diagnostic platform that could revolutionize TB detection in resource-limited settings. The device has shown 95% sensitivity and 98% specificity in pilot studies with 500 patients.\n\nThis scholarship would support the clinical validation phase and regulatory approval process, bringing this innovation closer to deployment in African healthcare systems.",
                'career_goals' => 'To establish a medical device company focused on developing affordable diagnostic technologies for African healthcare systems, while maintaining academic research collaborations.',
                'why_this_scholarship' => 'The scholarship\'s emphasis on health innovation and entrepreneurship perfectly matches my goals of commercializing research innovations. The mentorship from successful health entrepreneurs would be crucial for navigating the path from research to market.',
                'financial_need_description' => 'Clinical trials and regulatory approval processes are expensive. While I have university funding for my PhD, additional support is needed for device manufacturing, clinical validation, and patent costs.',
                'current_funding_sources' => 'National Research Foundation PhD scholarship (basic stipend), Small innovation grant (insufficient for clinical trials)',
                'reference_1_name' => 'Prof. David Thompson',
                'reference_1_email' => 'd.thompson@uct.ac.za',
                'reference_1_relationship' => 'PhD Supervisor',
                'reference_2_name' => 'Dr. Lisa Martinez',
                'reference_2_email' => 'l.martinez@who.int',
                'reference_2_relationship' => 'WHO Collaborator',
                'additional_info' => 'Strong background in electrical engineering, molecular biology, and machine learning. Fluent in English, Afrikaans, and Mandarin.',
                'status' => 'shortlisted',
                'submitted_at' => now()->subDays(20),
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Hassan',
                'email' => 'amina.hassan@example.com',
                'phone' => '+251911234567',
                'date_of_birth' => '1999-03-10',
                'nationality' => 'Ethiopian',
                'country_of_residence' => 'Ethiopia',
                'address' => '12 Unity Road, Addis Ababa, Ethiopia',
                'current_education_level' => 'undergraduate',
                'institution_name' => 'Addis Ababa University',
                'field_of_study' => 'Nursing',
                'gpa' => '3.6',
                'graduation_year' => 2027,
                'academic_achievements' => "Top 10% of nursing cohort\nVolunteer at Black Lion Hospital (500+ hours)\nLeader of Student Nurses Association\nCompleted advanced maternal health training",
                'research_area' => 'Nursing practices in maternal health',
                'concept_note' => 'Investigating the role of nurse-led interventions in reducing postpartum complications in Ethiopian health centers.',
                'research_proposal' => "Title: Nurse-Led Postpartum Care Interventions in Ethiopian Health Centers\n\nBackground: Ethiopia has made progress in institutional deliveries, but postpartum complications remain high due to inadequate follow-up care.\n\nObjective: To evaluate the effectiveness of structured nurse-led postpartum home visits in reducing complications.\n\nMethods: Randomized controlled trial with 400 women receiving either standard care or nurse-led home visits at days 3, 7, and 14 postpartum.\n\nMeasures: Incidence of postpartum hemorrhage, infection, and maternal satisfaction scores.",
                'motivation_letter' => "My passion for nursing began when I witnessed the dedication of nurses who saved my mother's life during a complicated childbirth. Their compassion and clinical expertise inspired me to pursue nursing as a career.\n\nDuring my clinical rotations, I observed that many postpartum complications could be prevented with proper home follow-up. However, resource constraints limit the ability of health centers to provide comprehensive postpartum care.\n\nI believe nurse-led interventions can bridge this gap. This scholarship would enable me to conduct research that contributes evidence for policy changes and improved nursing practices in maternal health across Ethiopia.",
                'career_goals' => 'To become a maternal health nursing specialist and educator, training the next generation of nurses to provide high-quality, evidence-based maternal care in Ethiopia.',
                'why_this_scholarship' => 'This scholarship would provide both the financial support to continue my studies and the research opportunities to contribute to maternal health improvement in Ethiopia. The networking with other health professionals would broaden my perspective on maternal health challenges.',
                'financial_need_description' => 'I come from a rural farming family with limited resources. My parents have sacrificed greatly to support my education, but they cannot afford the additional costs of university life and research activities.',
                'current_funding_sources' => 'University tuition waiver, Weekend work at local clinic (covers basic living expenses only)',
                'reference_1_name' => 'Sr. Tigist Alemayehu',
                'reference_1_email' => 't.alemayehu@aau.edu.et',
                'reference_1_relationship' => 'Clinical Instructor',
                'reference_2_name' => 'Dr. Solomon Tesfaye',
                'reference_2_email' => 's.tesfaye@blacklion.gov.et',
                'reference_2_relationship' => 'Supervisor at Black Lion Hospital',
                'additional_info' => 'Fluent in Amharic, English, and Oromo. Experience in community health education and health worker training.',
                'status' => 'accepted',
                'submitted_at' => now()->subDays(25),
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Okonkwo',
                'email' => 'david.okonkwo@example.com',
                'phone' => '+233244567890',
                'date_of_birth' => '1997-07-18',
                'nationality' => 'Ghanaian',
                'country_of_residence' => 'Ghana',
                'address' => '34 Ridge Road, Accra, Ghana',
                'current_education_level' => 'graduate',
                'institution_name' => 'University of Ghana',
                'field_of_study' => 'Epidemiology',
                'gpa' => '3.5',
                'graduation_year' => 2026,
                'academic_achievements' => "Research Assistant at Ghana Health Service\nContributed to COVID-19 response data analysis\nCo-authored report on malaria surveillance\nTraining in GIS and spatial epidemiology",
                'research_area' => 'Infectious disease surveillance and outbreak response',
                'concept_note' => 'Developing improved surveillance systems for early detection of infectious disease outbreaks in West Africa using digital health technologies and AI.',
                'research_proposal' => "Title: AI-Enhanced Surveillance System for Infectious Disease Outbreak Detection\n\nProblem: Traditional surveillance systems have delays in detecting outbreaks, leading to late responses and higher mortality.\n\nSolution: Integrate syndromic surveillance data from health facilities with social media monitoring and environmental data, using machine learning for early outbreak prediction.\n\nImplementation: Pilot in 20 health districts across Ghana, with plans for regional expansion.\n\nExpected Impact: Reduce outbreak detection time from weeks to days, enabling faster public health response.",
                'motivation_letter' => "The West African Ebola epidemic demonstrated the critical importance of early outbreak detection and rapid response. As a research assistant during Ghana's COVID-19 response, I saw how timely surveillance data saved lives.\n\nHowever, our current surveillance systems have significant gaps. Many outbreaks are detected only after significant spread. I am passionate about leveraging technology and data science to improve disease surveillance in Africa.\n\nThis scholarship would support my research on AI-enhanced surveillance systems that could transform how we detect and respond to infectious disease threats in West Africa.",
                'career_goals' => 'To lead infectious disease surveillance programs for African CDC, developing innovative early warning systems for outbreak detection and response across the continent.',
                'why_this_scholarship' => 'The scholarship\'s focus on health innovation aligns with my work on digital surveillance technologies. Access to mentorship from epidemiologists and data scientists would accelerate my research and professional development.',
                'financial_need_description' => 'My graduate program requires expensive software licenses, field data collection, and travel to conferences. Current funding barely covers tuition and basic living expenses.',
                'current_funding_sources' => 'Graduate assistant stipend (covers tuition), Occasional consulting work (irregular income)',
                'reference_1_name' => 'Prof. Kwame Asante',
                'reference_1_email' => 'k.asante@ug.edu.gh',
                'reference_1_relationship' => 'Academic Advisor',
                'reference_2_name' => 'Dr. Ama Boateng',
                'reference_2_email' => 'a.boateng@ghs.gov.gh',
                'reference_2_relationship' => 'Research Supervisor at Ghana Health Service',
                'additional_info' => 'Strong skills in Python, R, SQL, and GIS tools. Experience with outbreak investigation and epidemiological modeling.',
                'status' => 'rejected',
                'submitted_at' => now()->subDays(18),
            ],
        ];
    }
}
