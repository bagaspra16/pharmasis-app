# Triage validation, 27 November 2026

**Release decision: not ready.** No clinical review, and emergency detection is below target.

## Results

| Proposed target | Result | Met |
| --- | --- | --- |
| Emergency sensitivity at least 99% | 90.4% on unseen phrasings with AI (47/52) | No |
| Urgent sensitivity at least 95% | 80% (8/10) | No |
| Expert agreement at least 95% | Not measured | No |
| Critical rule execution 100% | All 34 rules fire in automated tests | Yes, for rules given the right findings |
| Zero critical false reassurance | 0 observed rated routine | Yes, on a small set |

## Test sets

**Written scenarios** (`tests/Fixtures/triage_scenarios.json`, 40 cases, rules only): emergency 20/20, urgent 8/10, exact agreement 36/40. The same author wrote the scenarios and the patterns, so this overstates performance.

- Two fever cases gave "unable to assess" because the scenario supplied no duration answer.
- Two expected "urgent" and got "emergency" (baby with fever; painful eye with blurred vision).

**Unseen phrasings** (`tests/Fixtures/triage_paraphrases.json`, 52 emergency messages written by a language model):

| Run | Caught |
| --- | --- |
| Patterns only, first run | 18/52 (34.6%) |
| Patterns and AI tagging, first run | 47/52 (90.4%) |
| Patterns only, after adding wording for overdose, bleeding, breathing and hopelessness | 27/52 (51.9%) |

The last row was tuned on this set, so it is not an independent measure. The AI run was not repeated after that change. No clinician checked whether each message truly describes an emergency. Missed with AI: some severe headache, confusion and overdose messages.

Automated tests (`tests/Feature/TriageTest.php`) cover routine conditions, AI outage, the AI being unable to lower urgency, negation, escalation without any model call, approval by hash, and the screening endpoints. Raw runs are in `docs/triage-runs/`.

## Not tested

- The screen flow in a real browser, on mobile, by keyboard or screen reader.
- Over-triage rate (no set of non-emergency phrasings at scale).
- Real user messages.
- Clinical review of any part.

## Needed before release

1. Clinician review of rules, patterns, messages and numbers.
2. A scenario set written and labelled by clinicians who have not seen the patterns, large enough to support the targets statistically. Showing 99% sensitivity with 95% confidence needs about 300 emergency cases with none missed.
3. Over-triage measurement.
4. Usability testing of the urgency wording in Indonesian and English.
