# Urgency assessment (triage)

**Status: off, awaiting clinical review.** No clinician has reviewed the rules, word patterns, messages or emergency numbers.

## How it works

| Step | What happens | Uses AI |
| --- | --- | --- |
| First message | Warning-sign word patterns (English, Indonesian). A match shows emergency guidance at once | No |
| Screening | Warning-sign checklist first. Selecting any item shows emergency guidance at once. Then age, duration, health conditions, then follow-up questions | No for these four |
| Conclusion | Patterns, checklist answers, then AI tagging of free text. The rules decide the category | Tagging only |

- The AI can only add findings. It cannot remove one or choose the category.
- Categories: Emergency, Urgent, Routine, Unable to assess.
- Routine needs all of: a cold, sore throat or headache; age 5 to 64; duration given; every warning sign explicitly denied; no health condition; AI tagging succeeded and found nothing else; English or Indonesian; symptoms not reported as severe.
- Anything missing gives Unable to assess.
- Rules are in `resources/triage/rules.json`. Each has a source quote and states how it differs from the source.

## Turning it on

Local testing only:

```env
TRIAGE_ENABLED=true
TRIAGE_ALLOW_UNREVIEWED=true
```

For real users:

1. A clinician reviews `docs/TRIAGE-CLINICAL-REVIEW.md`.
2. Apply their changes and run `php artisan triage:review-sheet`.
3. Record their name, qualification, date and the ruleset hash in `config/triage.php`.
4. Set `TRIAGE_ENABLED=true`. Leave `TRIAGE_ALLOW_UNREVIEWED` unset.

Any later rule change alters the hash and turns the feature off until it is approved again.

## Risk assessment

Prepared by an AI assistant. A clinician must review severity and acceptability.

| Hazard | Possible harm | Control in place | Remaining risk |
| --- | --- | --- | --- |
| Free text describes an emergency in unlisted words | Delayed care | Checklist asked first; AI tagging at conclusion; the existing AI router can still flag emergencies | **High.** Patterns alone caught about half of unseen phrasings |
| AI service down or rate limited | Missed warning sign | Patterns and checklist work without it; routine is blocked | High for free text |
| Emergency typed into a follow-up answer | Delay until the conclusion | Checked after each answer when intake is on (see INTAKE.md), otherwise at the conclusion | Medium |
| False reassurance | Delayed care | Strict routine conditions; comforting opener removed unless routine | Medium. The AI-written conclusion text below the category is not constrained by it and may sound milder |
| Informational question names a symptom ("what causes chest pain?") | Unnecessary alarm; the factual answer is not shown | Back button | **Likely to happen.** Needs a product and clinical decision |
| Over-triage | Alarm fatigue, unnecessary visits | None | Not measured |
| Negated phrases | Missed or false sign | Negation only directly before the phrase; never for breathing, bleeding or suicide | Medium |
| Other interface languages | Missed sign | Never rated routine | High. Patterns and checklist are English or Indonesian only |
| Wrong emergency number | Delayed care | Fixed table, no AI; text also says to use the local number | Location comes from IP address and can be wrong |
| Unreviewed or changed rules reach users | Unsafe advice | Off by default; approval tied to ruleset hash | `TRIAGE_ALLOW_UNREVIEWED` bypasses it |
| Source pages change | Outdated rule | `triage:review-sheet` checks each quote | Run it regularly |
| `/medicheck/analyze` (no current page uses it) | No assessment | None | Remove the route or cover it |
| User taps a checklist item by mistake | False alarm | Back button | Low |

## For the clinician to decide

1. Fever in a baby under 3 months: the two NHS pages give different levels. Emergency was chosen.
2. Sources are mainly UK and US. Check fit with Indonesian practice, including dengue, rabies and tuberculosis.
3. Whether the routine category should exist at all in the first release.
4. Whether any chest pain, fainting or stiff neck should always be Emergency.
5. How informational questions that name symptoms should be handled.
