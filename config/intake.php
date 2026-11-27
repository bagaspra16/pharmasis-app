<?php

// Adaptive follow-up questions. Guide: docs/INTAKE.md
// Question selection has not been reviewed by a clinician.
return [
    // Needs the triage feature to be active too, so emergencies take priority.
    'enabled' => env('INTAKE_ENABLED', false),

    // Complaint groups. "ask" lists finding ids from resources/triage/rules.json
    // worth asking about. Signs already in the warning-sign checklist are not repeated.
    'groups' => [
        'fever' => [
            'pattern' => 'fever|high temperature|dengue|demam|dbd',
            'ask' => ['persistent_vomiting', 'dehydration', 'gum_nose_bleed', 'urinary', 'wound_infection'],
        ],
        'breathing' => [
            'pattern' => 'cough|breath|runny nose|stuffy|sore throat|\bcold\b|batuk|sesak|pilek|tenggorokan|flu',
            'ask' => ['fever', 'coughing_blood'],
        ],
        'digestive' => [
            'pattern' => 'stomach|abdom|tummy|belly|nausea|vomit|diarr|perut|lambung|mual|muntah|diare|mencret',
            'ask' => ['persistent_vomiting', 'blood_in_stool', 'dehydration', 'fever'],
        ],
        'head' => [
            'pattern' => 'headache|migraine|dizz|sakit kepala|pusing|migrain',
            'ask' => ['head_injury', 'vision_change', 'persistent_vomiting', 'fever'],
        ],
        'urinary' => [
            'pattern' => '\bpee|urin|kencing|buang air kecil|anyang',
            'ask' => ['fever', 'back_pain'],
        ],
        'skin' => [
            'pattern' => 'skin|rash|wound|\bcut\b|\bbit|itch|kulit|luka|ruam|gatal|digigit|gigitan',
            'ask' => ['wound_infection', 'bite', 'fever'],
        ],
    ],

    // Extra AI-written questions, only for complaints that match no group.
    'max_ai_questions' => 3,
];
