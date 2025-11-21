<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Application Details - {{ $application->first_name }} {{ $application->last_name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        h2 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .value {
            display: inline-block;
        }
        .long-text {
            margin-top: 5px;
            white-space: pre-wrap;
            text-align: justify;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .meta {
            font-size: 10px;
            color: #666;
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Scholarship Application Details</h1>
        <p><strong>{{ $application->scholarship->title ?? 'N/A' }}</strong></p>
    </div>

    <div class="meta">
        Submitted: {{ $application->submitted_at ? $application->submitted_at->format('M d, Y h:i A') : 'Draft' }}<br>
        Status: {{ ucfirst(str_replace('-', ' ', $application->status)) }}
    </div>

    <div class="section">
        <h2>Personal Information</h2>
        <div class="row"><span class="label">Full Name:</span> <span class="value">{{ $application->first_name }} {{ $application->last_name }}</span></div>
        <div class="row"><span class="label">Email:</span> <span class="value">{{ $application->email }}</span></div>
        <div class="row"><span class="label">Phone:</span> <span class="value">{{ $application->phone }}</span></div>
        <div class="row"><span class="label">Date of Birth:</span> <span class="value">{{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'N/A' }}</span></div>
        <div class="row"><span class="label">Nationality:</span> <span class="value">{{ $application->nationality }}</span></div>
        <div class="row"><span class="label">Country of Residence:</span> <span class="value">{{ $application->country_of_residence }}</span></div>
        <div class="row"><span class="label">Address:</span> <span class="value">{{ $application->address }}</span></div>
    </div>

    <div class="section">
        <h2>Academic Information</h2>
        <div class="row"><span class="label">Education Level:</span> <span class="value">{{ ucfirst(str_replace('-', ' ', $application->current_education_level)) }}</span></div>
        <div class="row"><span class="label">Institution:</span> <span class="value">{{ $application->institution_name }}</span></div>
        <div class="row"><span class="label">Field of Study:</span> <span class="value">{{ $application->field_of_study }}</span></div>
        <div class="row"><span class="label">GPA:</span> <span class="value">{{ $application->gpa ?? 'N/A' }}</span></div>
        <div class="row"><span class="label">Graduation Year:</span> <span class="value">{{ $application->graduation_year ?? 'N/A' }}</span></div>
        
        @if($application->academic_achievements)
        <div class="row">
            <div class="label">Academic Achievements:</div>
            <div class="long-text">{{ $application->academic_achievements }}</div>
        </div>
        @endif
    </div>

    @if($application->research_area || $application->concept_note || $application->research_proposal)
    <div class="section">
        <h2>Research & Concept</h2>
        @if($application->research_area)
        <div class="row"><span class="label">Research Area:</span> <span class="value">{{ $application->research_area }}</span></div>
        @endif
        
        @if($application->concept_note)
        <div class="row">
            <div class="label">Concept Note:</div>
            <div class="long-text">{{ $application->concept_note }}</div>
        </div>
        @endif

        @if($application->research_proposal)
        <div class="row">
            <div class="label">Research Proposal:</div>
            <div class="long-text">{{ $application->research_proposal }}</div>
        </div>
        @endif
    </div>
    @endif

    <div class="section">
        <h2>Motivation & Career Goals</h2>
        <div class="row">
            <div class="label">Motivation Letter:</div>
            <div class="long-text">{{ $application->motivation_letter }}</div>
        </div>
        <div class="row">
            <div class="label">Career Goals:</div>
            <div class="long-text">{{ $application->career_goals }}</div>
        </div>
        <div class="row">
            <div class="label">Why This Scholarship:</div>
            <div class="long-text">{{ $application->why_this_scholarship }}</div>
        </div>
    </div>

    @if($application->financial_need_description || $application->current_funding_sources)
    <div class="section">
        <h2>Financial Need</h2>
        @if($application->financial_need_description)
        <div class="row">
            <div class="label">Description:</div>
            <div class="long-text">{{ $application->financial_need_description }}</div>
        </div>
        @endif
        @if($application->current_funding_sources)
        <div class="row">
            <div class="label">Current Funding:</div>
            <div class="long-text">{{ $application->current_funding_sources }}</div>
        </div>
        @endif
    </div>
    @endif

    @if($application->reference_1_name || $application->reference_2_name)
    <div class="section">
        <h2>References</h2>
        @if($application->reference_1_name)
        <div class="row">
            <strong>Reference 1:</strong> {{ $application->reference_1_name }} 
            @if($application->reference_1_email) ({{ $application->reference_1_email }}) @endif
            @if($application->reference_1_relationship) - {{ $application->reference_1_relationship }} @endif
        </div>
        @endif
        @if($application->reference_2_name)
        <div class="row">
            <strong>Reference 2:</strong> {{ $application->reference_2_name }} 
            @if($application->reference_2_email) ({{ $application->reference_2_email }}) @endif
            @if($application->reference_2_relationship) - {{ $application->reference_2_relationship }} @endif
        </div>
        @endif
    </div>
    @endif

    @if($application->additional_info)
    <div class="section">
        <h2>Additional Information</h2>
        <div class="long-text">{{ $application->additional_info }}</div>
    </div>
    @endif
</body>
</html>
