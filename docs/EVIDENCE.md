# Medical evidence and references

## How it works

1. Search terms (drug names, conditions) are derived from the question. Only these short terms are sent to source services, never the user's text.
2. Sources are retrieved live: openFDA drug labels, MedlinePlus, WHO fact sheets, PubMed systematic reviews and meta-analyses.
3. Symptom-checker and fact answers are generated from the retrieved excerpts.
4. The answer is split into statements in code. A model proposes which excerpt supports or contradicts each one.
5. Code rejects any proposal whose quote is not found in the excerpt, or whose figures are not in the quote. AI-inferred interactions are never marked supported.
6. The user sees a status, per-statement results, and only the references that were actually cited.

Titles, organisations, dates and links are read from the source documents. No model writes them.

## Coverage by feature

| Feature | References |
| --- | --- |
| MediCheck facts, combo and check routes | Grounded generation and statement check |
| MediCheck screening conclusion | Grounded generation and statement check |
| `/medicheck/analyze` (no current page uses it) | Statement check only |
| Interaction checker | Statement check |
| Drug page AI simplify and translate | Link to the original label and its date. Output is **not** checked |
| Emergency alert | Not checked, so it is never delayed |
| Nearby facilities | Not covered. Names and addresses are model-generated |
| Home page demo cards | Not covered. Static marketing text |

## Known limits

- A real quote can still fail to support a statement. Citation accuracy needs human review.
- Only excerpts are checked, so a statement the full document supports can show as not verified.
- Conflicts are found only between a statement and the retrieved excerpts, not between all published guidelines.
- No Indonesian Ministry of Health source is included, and no individual clinical trials.
- US drug labels may differ from Indonesian (BPOM) approvals and brand names.
- MedlinePlus search results carry no date; the panel says "Date not stated".
- If sources or the AI service fail or are rate limited, the answer is shown as unverified.
- Each answer needs extra AI calls, which adds latency and API usage.

## Maintenance

| Task | How |
| --- | --- |
| Add a WHO fact sheet | Add its slug and phrases in `config/evidence.php`, then run the source check |
| Check sources monthly | `php artisan evidence:check-sources` |
| Change freshness limits | `stale_after_days` in `config/evidence.php` (proposed values, not a standard) |
| Add a drug name synonym | `drug_synonyms` in `config/evidence.php` |
| Validate before a release | `php artisan evidence:validate`, then review every claim and quote in the JSON file |
| Turn off | `EVIDENCE_ENABLED=false` (answers then show as unverified) |

Source results and reports are cached for 24 hours. `php artisan cache:clear` forces a refresh.

## Release gate (proposed internal targets)

| Target | How to measure |
| --- | --- |
| Citation coverage at least 95% | `citation_coverage` from `evidence:validate` |
| Citation accuracy at least 95% | Human review of each claim and quote |
| Source traceability 100% | Every supported claim has a source id and quote (enforced in code); check `reference_urls_ok` |
| Zero unsupported critical claims | `critical_unsupported` from `evidence:validate` |

Current results are in `docs/EVIDENCE-VALIDATION.md`.
