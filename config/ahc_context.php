<?php

return [
    'system_instruction' => "You are the AI assistant for the Africa Health Collaborative (AHC).
Your goal is to answer user questions based ONLY on the provided context.
If a question is not related to AHC or cannot be answered by the context, politely refuse.
Do not make up information.

Context:
The Africa Health Collaborative (AHC) is a youth-led organization dedicated to strengthening primary healthcare across Africa.
It operates as a network focused on empowering young people through education, training, and community engagement.
Partners: Addis Ababa University, University of Toronto (Secretariat), Mastercard Foundation, and 8 African Partner Institutions.
Mission: Advance health professions education and research across Africa through collaboration, innovation, and community engagement.
Programs: Health entrepreneurship, digital health, and primary healthcare initiatives.
Scholarships: Mastercard Foundation graduate scholarship program for students at partner universities.
Contact: africasecretariat@utoronto.ca.
",
    'faq' => [
        'General Information' => [
            [
                'question' => 'What is the Africa Health Collaborative (AHC)?',
                'answer' => 'The Africa Health Collaborative (AHC) is a youth-led organization dedicated to strengthening primary healthcare across Africa. It operates as a network focused on empowering young people through education, training, and community engagement.',
            ],
            [
                'question' => 'Who is behind AHC?',
                'answer' => 'AHC is a partnership between Addis Ababa University, the University of Toronto (which serves as the Secretariat), the Mastercard Foundation, and a network of eight African Partner Institutions.',
            ],
            [
                'question' => 'What kind of resources does AHC provide?',
                'answer' => 'AHC curates a comprehensive collection of health resources, including research papers, policy briefs, reports, guidelines, and educational materials to support primary healthcare improvement across Africa.',
            ],
        ],
        'Mission & Programs' => [
            [
                'question' => 'What is the mission of AHC?',
                'answer' => 'The mission of AHC is to advance health professions education and research across Africa through collaboration, innovation, and community engagement, with a focus on strengthening primary healthcare.',
            ],
            [
                'question' => 'What programs does AHC offer?',
                'answer' => 'AHC runs diverse programs aimed at strengthening primary healthcare, with a focus on initiatives in health entrepreneurship and digital health.',
            ],
            [
                'question' => 'Does AHC offer scholarships?',
                'answer' => 'Yes, AHC facilitates the Mastercard Foundation graduate scholarship program for students at partner universities who have demonstrated academic excellence in health-related disciplines.',
            ],
        ],
        'Partners & Collaboration' => [
            [
                'question' => 'Who are AHC\'s main partners?',
                'answer' => 'AHC\'s key partners include Addis Ababa University, the University of Toronto, and the Mastercard Foundation. It collaborates with a total of eight leading institutions across Africa.',
            ],
            [
                'question' => 'How does collaboration work at AHC?',
                'answer' => 'AHC features a networked approach, bringing together partner institutions to foster collaboration, knowledge exchange, and scholarship in health professions education.',
            ],
        ],
        'Contact & Support' => [
            [
                'question' => 'How can I contact AHC?',
                'answer' => 'The AHC Africa Secretariat is located at the University of Toronto and can be reached via email at `africasecretariat@utoronto.ca`. The collaborative also has a strong presence at Addis Ababa University in Ethiopia.',
            ],
            [
                'question' => 'Where can I find news and updates?',
                'answer' => 'You can stay informed with the latest news, events, and updates from AHC by visiting the \"News\" and \"Events\" sections of our website.',
            ],
        ],
    ]
];
