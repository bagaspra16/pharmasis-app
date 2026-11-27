<?php

return [
    'labels' => [
        'title' => 'Before you act on this',
        'possibility' => 'A possible explanation, not a confirmed diagnosis.',
        'recommendation' => 'General suggestions. Check them with a pharmacist or doctor.',
    ],
    'warned' => 'Some wording here sounds more certain or reassuring than this tool can be. Do not rely on it.',
    'follow_assessment' => 'Where the text and the urgency assessment differ, follow the assessment.',
    'delay' => 'Some advice here mentions waiting. Do not delay because of it: follow the urgency assessment.',
    'unsafe' => 'Do not rely on this sentence: “:a”',
    'contradiction' => 'Two statements may disagree: “:a” and “:b”. Ask a pharmacist or doctor which applies.',
    'off_topic' => 'This answer may not address what you asked. Try describing it differently, or ask a health professional.',
    'limits' => [
        'no_exam' => 'Nobody has examined you. The explanation is a possibility based only on what you typed.',
        'general' => 'This is general information. It does not take your own health into account.',
        'unanswered' => ':count question(s) were not answered, so the picture is incomplete.',
        'medicine' => 'Medicines mentioned here have not been checked by a pharmacist against your allergies, conditions or other medicines.',
        'language' => 'Wording checks only work in English and Indonesian.',
        'review_failed' => 'The check for contradictions could not run.',
    ],
];
