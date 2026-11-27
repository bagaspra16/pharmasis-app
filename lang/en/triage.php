<?php

return [
    'heading' => 'Urgency assessment',
    'not_diagnosis' => 'This shows how quickly to seek care. It is not a diagnosis and can be wrong. If you feel worse or are worried, get medical help.',
    'why' => 'Warning signs found in what you told us',
    'steps' => 'What to do now',
    'unknown' => 'What we could not assess',
    'basis' => 'Based on',
    'unreviewed' => 'TEST MODE: these rules have not been reviewed by a clinician.',
    'call_button' => 'Call',
    'title' => [
        'emergency' => 'Emergency',
        'urgent' => 'Urgent',
        'routine' => 'Routine',
        'unable_to_assess' => 'Unable to assess',
    ],
    'meaning' => [
        'emergency' => 'You may need emergency medical care now.',
        'urgent' => 'A health professional should assess you promptly, ideally today.',
        'routine' => 'No warning sign was reported. A non-emergency consultation or self-care may be enough.',
        'unable_to_assess' => 'There is not enough information to judge urgency. This does not mean you are safe.',
    ],
    'next' => [
        'emergency' => [
            'Do not drive yourself. Ask someone to stay with you.',
            'Do not wait for more advice from this app.',
        ],
        'urgent' => [
            'Contact a doctor or clinic today.',
            'If you cannot be seen today, go to an urgent care or hospital emergency unit.',
        ],
        'routine' => [
            'Speak with a pharmacist or doctor if it does not improve or lasts more than a week.',
            'Get help sooner if anything new or worse appears.',
        ],
        'unable_to_assess' => [
            'Speak with a health professional to have this assessed.',
        ],
    ],
    'call_country' => 'Call :number (:country) or go to the nearest emergency department. If you are not in :country, call your local emergency number.',
    'call_default' => 'Call your local emergency number (:number in many countries, 119 in Indonesia) or go to the nearest emergency department.',
    'none' => 'None of these',
    'questions' => [
        'red_flags' => 'Do you have any of these right now?',
        'age' => 'How old is the person with the symptoms?',
        'duration' => 'How long have the symptoms lasted?',
        'risk' => 'Does any of these apply?',
    ],
    'ages' => ['Under 3 months', '3 months to 4 years', '5 to 17 years', '18 to 64 years', '65 years or older'],
    'durations' => ['Less than 3 days', '3 to 7 days', 'More than 7 days'],
    'risks' => ['Pregnant', 'Weak immune system, diabetes or chemotherapy', 'Heart, lung or kidney disease', 'None of these'],
    'missing' => [
        'age' => 'Age was not given.',
        'duration' => 'How long the symptoms have lasted was not given.',
        'risk' => 'Pregnancy and health conditions were not answered.',
        'warning_signs' => 'Warning signs were not all ruled out.',
        'recognised_complaint' => 'The complaint is not one this tool can rate as routine.',
        'age_group_not_covered' => 'This tool does not rate this age group as routine.',
        'risk_condition' => 'A health condition makes a routine rating unsafe.',
        'language' => 'This tool cannot rate urgency as routine in this language.',
    ],
];
