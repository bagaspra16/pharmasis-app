# Adaptive follow-up questions

**Status: off. Not reviewed by a clinician and not tested with users.**

## How it works

1. The first message is checked for emergency wording.
2. Facts stated plainly (age, duration, severity) are noted and not asked again. The first question shows what was noted.
3. The complaint is matched to one or more groups: fever, breathing, digestive, head, urinary, skin. People are not grouped or profiled.
4. Questions, in order: warning signs, age, duration, related symptoms for the group, severity, health conditions, medicines and allergies. Anything already known or irrelevant is left out.
5. AI-written questions (up to 3) are added only when no group matches.
6. After each answer the server re-plans the rest. A new emergency sign stops the questions and shows emergency guidance.
7. The urgency rules and the written conclusion receive the answers. Unanswered or "I don't know" items are listed as not assessed.

Every choice question offers "I don't know" and "Prefer not to say". Questions stop when the list is used up.

## Turning it on

It needs urgency assessment to be active (see `TRIAGE.md`). Local testing:

```env
TRIAGE_ENABLED=true
TRIAGE_ALLOW_UNREVIEWED=true
INTAKE_ENABLED=true
```

Groups and questions: `config/intake.php`. Wording: `lang/en/intake.php`, `lang/id/intake.php`.

## Privacy

- Collects age band, pregnancy or health condition (optional), and medicines or allergies (optional). It does not ask for name, gender, address or ID.
- Re-planning sends answers to this server only. No AI call.
- At the conclusion, answers are sent to the AI provider (Groq).
- Answers are kept in the server session history, and in the browser's local history if the user accepted cookies. **Review whether health answers should be stored at all.**

## Validation

| Proposed target | Result |
| --- | --- |
| Question relevance at least 95% | Not measured. Needs clinician rating |
| Required information completeness at least 90% | Not measured. No approved list of required information |
| User comprehension at least 95% | Not measured. Needs user testing |
| Unnecessary questions at most 5% | Not measured. In 10 developer-written scenarios, no already-stated fact was asked again |
| Critical information capture 100% in approved scenarios | Not measured. No approved scenarios |

Automated checks: `tests/Feature/IntakeTest.php`, `tests/Fixtures/intake_scenarios.json`. The page's question stepper was also run once in Node with a mocked server (exclusive choices, plan merging, server failure, emergency interrupt). Not run in a real browser.

## Number of questions (before the role question)

| Setup | Questions |
| --- | --- |
| Current default (all off) | 3 to 5, written by AI |
| Urgency assessment on, intake off | 7 to 9 |
| Intake on, in the 10 scenarios | 4 to 7 (average 5.7) |

## Known limits

- Mid-question emergency checks use word patterns and selected answers only. Patterns miss many phrasings (see `TRIAGE-VALIDATION.md`).
- A noted fact cannot be edited, only seen. If the message is about someone else, a noted age could be wrong.
- Conflicting answers are not queried. A reported symptom outweighs a later "none".
- English and Indonesian only. Other languages see English questions.
- Frequency and onset are not asked as separate questions.
- AI-written questions for unmatched complaints may overlap with fixed ones.

## Needed before enabling

1. Clinician review of groups, questions and what counts as sufficient information.
2. Usability test in Indonesian and English, including mobile and screen reader.
3. A decision on storing health answers.
