# Nearby healthcare facilities

**Status: on, but not ready for real users. In the last live test only 1 of 15 searches returned data.**

## How it works

| Part | Behaviour |
| --- | --- |
| Where | `/facilities`, the patient result screen and the emergency screens |
| Location | "Use my location" (browser permission prompt) or a typed address. Several matches are offered to the user |
| Data | OpenStreetMap (Overpass for facilities, Nominatim for addresses) plus operator records in `resources/facilities/curated.json` |
| Missing details | Shown as "Not listed". Nothing is written by a model |
| Distance | Straight line, labelled as approximate |
| Opening hours | Shown as listed. The app never says a place is open |
| Emergency | Call button first. Hospitals only. Those with a listed emergency department come first. Those marked as having none are left out |
| Duplicates | Same name within 150 m is merged |
| Old data | Map entries not edited for 2 years are flagged "May be outdated" |
| Map service down | An older copy (up to 14 days) is shown and labelled. Otherwise "not available", a Google Maps link and the emergency number |

This replaces `/medicheck/nearby`, which asked an AI model to write hospital names, addresses and contacts.

## Verification labels

| Label | When |
| --- | --- |
| Officially registered | Operator record with an official source link, checked within 365 days |
| Verified | Operator record with a source link, checked within 365 days |
| Community-submitted, not verified | Every OpenStreetMap record |
| Unverified | Operator record missing a source or a recent check |

**No record is currently verified or registered.** The curated file is empty.

Curated record format:

```json
{"records": [{
  "name": "", "category": "hospital", "lat": 0.0, "lon": 0.0,
  "address": null, "phone": null, "website": null, "opening_hours": null,
  "services": [], "emergency": "yes",
  "status": "verified", "source_name": "", "source_url": "https://", "checked_on": "2026-01-31"
}]}
```

`category`: `hospital`, `puskesmas`, `clinic`, `pharmacy`, `doctor`, `dentist` or `laboratory`.

## Privacy

| Data | Goes to | Kept |
| --- | --- | --- |
| Device position (about 100 m) | This server, in the request body | Not stored |
| Search area (about 1 km grid) | Overpass | Facility data for the area is cached 14 days, not linked to a user |
| Typed address | This server and Nominatim | Results cached 1 day under a hashed key |
| Directions | Google Maps, only when the user taps | Google's terms |
| Visitor IP address | `ip-api.com` or `ipapi.co`, only after cookie consent | 24 hours, to pick the emergency number |

- Positions and results are not written to browser storage.
- The IP lookup used to run on every home page visit, and the browser also called `ipapi.co`. The first now needs consent. The second is removed.
- A failed IP lookup no longer defaults to Jakarta.
- `ip-api.com` is called over plain HTTP (existing behaviour).

## Settings

`FACILITIES_ENABLED` (default true), `FACILITIES_OVERPASS_URLS`, `FACILITIES_GEOCODER_URL`, `FACILITIES_USER_AGENT`. Radius, cache and age limits are in `config/facilities.php`.

**The default map servers are free community services with usage limits.** Use a self-hosted or paid service before real traffic, and follow the [Nominatim policy](https://operations.osmfoundation.org/policies/nominatim/) and [OpenStreetMap licence](https://www.openstreetmap.org/copyright).

## Validation

`php artisan facilities:validate` searches five public landmarks. Results are in `facility-runs/`. Automated tests: `tests/Feature/FacilityTest.php`.

Live run of 2026-11-27, 10:50:

| Proposed target | Result |
| --- | --- |
| Location accuracy at least 98% | Not established. Address search put 5 of 5 landmarks within 0.7 km. Expected positions were entered by the developer |
| Facility data completeness at least 95% | **Not met.** Address 81.8%, phone 13.6%, hours 0%, website 27.3%, services 4.5% |
| Verification status documented 100% | Met for the records returned (22 of 22) |
| Search relevance at least 90% | Not measured. Needs human rating |
| Response time p95 at most 2 s | **Not met.** 14 of 15 searches failed. The one success took 7.9 s. Its cached repeat took 1 ms |

- Completeness comes from 22 hospital records in one area (Surabaya). It does not describe other places or types.
- 72.7% of those records had not been edited for over 2 years.
- 3 of the 22 listed an emergency department.
- Failures were server-capacity errors (`502`) and connection timeouts. The test could not separate the public servers from the test network.
- An earlier run the same day returned no data at all.

## Other limits

- Specialist hospitals (such as eye or maternity) are not filtered out of emergency results.
- Facility type and emergency tags depend on what map contributors entered. Missing places are possible.
- No in-app submission or correction form. The "Source and corrections" link goes to OpenStreetMap.
- Wording is English and Indonesian only.
- "Use my location" needs HTTPS (or `localhost`).
- Behind a misconfigured proxy, the IP lookup would see a private address and use a Jakarta one (existing behaviour).
- Checked in a headless browser: the page's first screen only. Searching, mobile devices and screen readers are not tested.
- `partials/hero-medicheck-scripts.blade.php` still calls the removed route. No page includes it.

## Needed before relying on it

1. A reliable map data service.
2. An official facility source and checked emergency departments in the curated file.
3. Real-device testing and relevance rating.
