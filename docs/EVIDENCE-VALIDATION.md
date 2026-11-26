# Evidence feature validation, 27 November 2026

**Release decision: not ready.** The proposed internal targets are not met, and some statements were matched to passages that do not support them.

## Automated tests

PHPUnit covers: rejected invented quotes and unknown sources, changed dose figures, contradictions, outdated sources, source outage, no matching source, AI-inferred interactions, dose-limit sentences in excerpts, limits on what is sent to source services, and the interaction and screening endpoints. External services are faked.

## Live run

Eight synthetic cases (`tests/Fixtures/evidence_release_set.json`), real AI and source services. Raw data: `evidence-validation-2026-11-26.json` (file date is UTC). The first run, before fixes, is `evidence-validation-baseline.json`.

| Measure | Proposed target | Result | Met |
| --- | --- | --- | --- |
| Citation coverage | at least 95% | 13.6% (15 of 110 statements). Baseline 5.7% | No |
| Citation accuracy | at least 95% | 47% (7 of 15), see below | No |
| Source traceability | 100% | All 15 matched statements carry a source and quote | Yes, structurally only |
| Unsupported safety-related statements | 0 | 45, plus wrong matches that this count misses | No |
| Reference links reachable | not set | 7 of 7 | n/a |

One of the eight cases (hypertension) ended as "unavailable".

## Review of the 15 matched statements

Reviewed by the developer assistant (an AI), **not by a clinician**.

| Verdict | Count | Examples |
| --- | --- | --- |
| Quote supports the statement | 7 | Dengue transmission; prednisone with systemic fungal infection |
| Quote supports only part | 4 | Headache warning signs; "1 in 20" dengue severity with added outcomes |
| Wrong | 4 | See below |

Wrong matches:

1. Two statements gave 2400 mg as the maximum daily dose of ibuprofen. The prescribing label limit is 3200 mg. The quote only compares side effects at the two doses. The excerpt given to the model had likely omitted the limit sentence.
2. Advice about enteric-coated aspirin timing was matched to a quote saying the finding cannot be extended to enteric-coated aspirin.
3. An inhaled fluticasone contraindication was matched to the label of a different product (a nasal spray).

## Changes made after the live run

These are **not** reflected in the figures above. The full live set has not been run again.

- Dose-limit sentences are now always kept in excerpts. Checked live: the ibuprofen excerpt contains "Do not exceed 3200 mg". Whether the generated answer is now correct was not re-checked.
- On-screen wording changed from "Supported" to "Matching passage found", with a statement that this is not medical verification.

## Not tested

- Visual check of the panel in a browser, including mobile layout and keyboard use.
- Conflict and outdated-source handling with live data (tests with faked data only).
- `/medicheck/analyze` and the drug page note, live.
- Response time and behaviour under rate limits.
- Clinical review by a qualified person.

## Needed before release

1. Better retrieval: choose passages by label section and by statement, using semantic search.
2. A stricter second check, especially for doses, negations and product formulation.
3. An API tier with enough rate limit to check every statement.
4. A larger test set, reviewed by a pharmacist or doctor.
