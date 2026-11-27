# Response quality checks

**Status: off. Not complete. The checks missed unsafe statements in real output, and no clinician or user has reviewed anything.**

## How it works

| Step | What happens | Uses AI |
| --- | --- | --- |
| Before writing | Writing rules are added to the prompt: plain language, possibility not diagnosis, no promises, follow the urgency category | No |
| Wording patterns | Promises and false certainty are removed. A sentence that may also carry a warning is kept and flagged | No |
| Advice to wait | Flagged when urgency is Urgent or Unable to assess (patient answers only). Never removed | No |
| Warning-sign fields | Nothing is removed from "when to seek care", "red flags" or "warning signs" | No |
| Second reading | One extra call quotes contradictions, off-topic answers and overconfident sentences. Code keeps only quotes found word for word. It flags and never deletes | Yes |
| Limits shown | Chosen for the case: not examined, general information, unanswered questions, medicines not checked, language | No |
| Evidence | The existing evidence check runs on the cleaned text (see `EVIDENCE.md`) | Yes |

- The urgency category is never changed here.
- The model's own disclaimer is replaced by the limits above.
- A failed second reading is disclosed to the user.
- Applies to the screening conclusion and the question router (`/medicheck/conclude`, `/medicheck/route`).

## Turning it on

```env
QUALITY_ENABLED=true
```

`QUALITY_MODEL_REVIEW=false` drops the extra call. Consistency with urgency also needs urgency assessment to be active (see `TRIAGE.md`).

Patterns: `config/quality.php`. Wording: `lang/en/quality.php`, `lang/id/quality.php`.

## Validation

Run `php artisan quality:validate` (patterns only) or `php artisan quality:validate --live` (real model calls). Results: `quality-runs/`. Blank sheet for an expert: `QUALITY-REVIEW-SHEET.md`.

All cases and expected results were written by the developer (an AI assistant).

| Check | Result |
| --- | --- |
| Pattern cases used to build the patterns | 22/22 as expected |
| Reworded unsafe sentences, patterns only | **0/14 caught** |
| Reworded unsafe sentences, second reading | 14/14 flagged, 0/7 safe sentences flagged (small, same author, listed one after another, not in real answers) |
| Words per sentence in 5 live answers, without then with writing rules | 15.2 to 18.4, then 10.6 to 14.2 |
| Automated tests | `tests/Feature/QualityTest.php` |

| Proposed target | Result |
| --- | --- |
| Medical factuality at least 95% | Not measured. Needs expert rating |
| Evidence consistency at least 95% | Not met. Last evidence run matched 15 of 110 statements (`EVIDENCE-VALIDATION.md`) |
| Zero critical contradictions | Not established. No approved test dataset |
| Critical safety requirements 100% | Not established. No approved scenarios |
| User comprehension at least 90% | Not measured. Sentence length is not comprehension |

## Problems found in live answers

From reading the final run (2026-11-27). This reading is by an AI assistant, not a clinician. **The checks raised no notice on any of the five answers.**

| Case | Problem |
| --- | --- |
| Daily headache (Indonesian) | Blames uncontrolled blood pressure. The user answered "don't know". Repeated in 3 of 3 extra tries despite a writing rule against it |
| Daily headache | The answer's own "when to seek care" lists only severe signs, with no routine visit for a month of daily headache |
| Burning urination | "This is an infection" states a diagnosis |
| Fever for four days | "Usually improves with rest and care" reassures although urgency is unknown |
| Fever for four days | Suggests ibuprofen. Medicines were not disclosed and dengue is not considered |
| Stomach pain after meals for two weeks | Rated Emergency: AI tagging read it as severe or sudden abdominal pain (rule EM15). Likely over-triage |

An earlier run also suggested ibuprofen for urinary symptoms with pregnancy unknown. The second reading flagged the fever reassurance in some runs and not others.

## Other limits

- Writing rules reduce length, which can also drop useful advice.
- One Indonesian generation failed once with a Groq JSON error. Six repeats of that case succeeded.
- Patterns are English and Indonesian only.
- Not covered: `/medicheck/analyze`, drug pages, interaction checker.
- `/medicheck/nearby` (existing code) asks the model for hospital names and contacts. These can be invented.
- The second reading adds one call of delay and quota per answer. It sends the complaint and answer to Groq again.
- Not tested in a real browser, on mobile or with a screen reader.

## Needed before enabling

1. A decision on whether answers may name medicines when pregnancy, allergies or current medicines are unknown.
2. Clinician rating of the review sheet, and review of the patterns and wording.
3. A stronger way to stop unsupported explanations. Writing rules alone did not.
4. Comprehension testing with Indonesian and English readers.
