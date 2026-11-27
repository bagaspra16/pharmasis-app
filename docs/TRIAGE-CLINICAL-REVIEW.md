# Triage rules for clinical review

Status: **NOT REVIEWED. NOT APPROVED.**

Ruleset: 0.1-draft, hash `89658f4474f498f9`. Generated 2026-11-26.

The rules were drafted by an AI assistant from public pages. "Adaptation" shows where a rule differs from its source. Please check every rule, the word patterns, the emergency numbers in `config/triage.php`, and the user messages in `lang/*/triage.php`.

## Rules

| ID | Level | When | Source and quote | Adaptation | Approve / change | Comment |
| --- | --- | --- | --- | --- | --- | --- |
| EM01 | emergency | any of: Chest pain, pressure or tightness | [NHS: Chest pain](https://www.nhs.uk/symptoms/chest-pain/) "you get sudden pain or discomfort in your chest that does not go away" | Any chest pain escalates, including intermittent pain that the source sends to a GP. | | |
| EM02 | emergency | any of: Severe difficulty breathing | [NHS: Shortness of breath](https://www.nhs.uk/symptoms/shortness-of-breath/) "you have severe difficulty breathing" | Any "cannot or hard to breathe" wording escalates. | | |
| EM03 | emergency | any of: Blue, grey or very pale lips or skin | [NHS: Shortness of breath](https://www.nhs.uk/symptoms/shortness-of-breath/) "your lips or skin are turning very pale, blue or grey" | - | | |
| EM04 | emergency | any of: Face drooping, arm or leg weakness, or speech trouble | [CDC: Signs and symptoms of stroke](https://www.cdc.gov/stroke/signs-symptoms/index.html) "Sudden numbness or weakness in the face, arm, or leg, especially on one side of the body." | Escalates even when onset is not described as sudden. | | |
| EM05 | emergency | any of: Passed out or hard to wake | [NHS: Fainting](https://www.nhs.uk/conditions/fainting/) "cannot be woken up within 1 minute" | Any fainting escalates; the source sends recovered fainting to a GP. | | |
| EM06 | emergency | any of: Seizure or fit | [NHS: Fainting](https://www.nhs.uk/conditions/fainting/) "is shaking or jerking (having a seizure or fit)" | Any seizure escalates. | | |
| EM07 | emergency | any of: Heavy bleeding or bleeding that will not stop | [NHS: Cuts and grazes](https://www.nhs.uk/conditions/cuts-and-grazes/) "you have a cut and cannot stop the bleeding" | Applied to bleeding from any site. | | |
| EM08 | emergency | any of: Vomiting or coughing up much blood, or black stool | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "vomit blood or have vomit that looks like ground coffee" | Black stool grouped here. | | |
| EM09 | emergency | any of: Thoughts of suicide or self-harm | [NHS: Help for suicidal thoughts](https://www.nhs.uk/mental-health/feelings-symptoms-behaviours/behaviours/help-for-suicidal-thoughts/) "you feel that you may be about to harm yourself, call 999" | Any suicidal or self-harm statement escalates. | | |
| EM10 | emergency | any of: Swelling of throat, tongue or lips, or cannot swallow | [NHS: Anaphylaxis](https://www.nhs.uk/conditions/anaphylaxis/) "your lips, mouth, throat or tongue suddenly become swollen" | - | | |
| EM11 | emergency | any of: Sudden severe headache | [NHS: Headaches](https://www.nhs.uk/symptoms/headaches/) "it started suddenly and is extremely painful" | - | | |
| EM12 | emergency | any of: Stiff neck, or a rash that does not fade | [NHS: Headaches](https://www.nhs.uk/symptoms/headaches/) "has a very high temperature, a stiff neck, or bright lights are bothering you" | Stiff neck alone escalates. | | |
| EM13 | emergency | any of: Sudden confusion | [NHS: Shortness of breath](https://www.nhs.uk/symptoms/shortness-of-breath/) "you feel suddenly confused" | - | | |
| EM14 | emergency | any of: Swallowed something harmful or overdose | [NHS: Poisoning](https://www.nhs.uk/conditions/poisoning/) "may have swallowed, touched or breathed in something harmful" | - | | |
| EM15 | emergency | any of: Sudden or severe stomach pain | [NHS: Stomach ache](https://www.nhs.uk/symptoms/stomach-ache/) "it started suddenly and is severe" | Severe OR sudden escalates; the source requires both. | | |
| EM16 | emergency | all of: Dengue fever mentioned; any of: Bleeding gums or nose, Repeated vomiting or cannot keep fluids down, Sudden or severe stomach pain | [CDC: Symptoms of dengue and testing](https://www.cdc.gov/dengue/signs-symptoms/index.html) "Immediately go to a local clinic or emergency room if you or a family member has any of the following warning signs" | - | | |
| EM17 | emergency | all of: Baby under 3 months, Fever | [NHS: Sepsis](https://www.nhs.uk/conditions/sepsis/) "38C or more in babies under 3 months old" | SOURCE CONFLICT: NHS "fever in children" says call 111 (urgent); NHS "sepsis" says call 999. Higher level chosen. | | |
| EM18 | emergency | all of: Very painful eye, Change in vision | [NHS: Red eye](https://www.nhs.uk/symptoms/red-eye/) "you have any changes to your sight" | Source applies this to a red eye. | | |
| UR01 | urgent | all of: Fever; duration: 3_to_7, over_7 | [NHS: High temperature (fever) in adults](https://www.nhs.uk/symptoms/fever-in-adults/) "not getting better or is getting worse" | 3 days chosen as the cut-off; the source gives none. | | |
| UR02 | urgent | any of: Repeated vomiting or cannot keep fluids down | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "cannot keep fluid down" | - | | |
| UR03 | urgent | any of: Signs of dehydration | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "signs of dehydration" | Applied without first trying rehydration solution. | | |
| UR04 | urgent | all of: Diarrhoea; duration: over_7 | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "diarrhoea for more than 7 days" | - | | |
| UR05 | urgent | any of: Blood in stool | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "bloody diarrhoea or bleeding from the bottom" | - | | |
| UR06 | urgent | all of: Pain when urinating; any of: Lower back or flank pain, Fever, Pregnant | [NHS: Urinary tract infections](https://www.nhs.uk/conditions/urinary-tract-infections-utis/) "you have pain in the lower tummy or in the back, just under the ribs" | - | | |
| UR07 | urgent | any of: Animal or human bite | [NHS: Animal and human bites](https://www.nhs.uk/conditions/animal-and-human-bites/) "you were bitten on your hands, feet, face or head" | Any bite is at least urgent. Local rabies protocol must be reviewed. | | |
| UR08 | urgent | any of: Hot, swollen or pus-filled skin or wound | [NHS: Cellulitis](https://www.nhs.uk/conditions/cellulitis/) "your skin is painful, hot and swollen" | - | | |
| UR09 | urgent | any of: Very painful eye | [NHS: Red eye](https://www.nhs.uk/symptoms/red-eye/) "your eye is very painful and red" | Redness not required. | | |
| UR10 | urgent | any of: Head injury | [NHS: Head injury and concussion](https://www.nhs.uk/conditions/head-injury-and-concussion/) "recently had a head injury" | Any recent head injury is at least urgent. | | |
| UR11 | urgent | any of: Pregnant | [NHS: Urinary tract infections](https://www.nhs.uk/conditions/urinary-tract-infections-utis/) "you're pregnant" | Any complaint in pregnancy is at least urgent; the source applies this to urine infection only. | | |
| UR12 | urgent | any of: Coughing up blood | [NHS: Shortness of breath](https://www.nhs.uk/symptoms/shortness-of-breath/) "you're coughing up blood" | - | | |
| UR13 | urgent | any of: Baby under 3 months | [NHS: Diarrhoea and vomiting](https://www.nhs.uk/symptoms/diarrhoea-and-vomiting/) "you're worried about a baby under 12 months" | Any complaint in a baby under 3 months is at least urgent. | | |
| UR14 | urgent | risk answer: immune | [NHS: Sore throat](https://www.nhs.uk/symptoms/sore-throat/) "you have a weakened immune system" | Applied to any complaint. | | |
| UR15 | urgent | risk answer: pregnant | [NHS: Urinary tract infections](https://www.nhs.uk/conditions/urinary-tract-infections-utis/) "you're pregnant" | As UR11, from the context question. | | |
| UR16 | urgent | any of: Change in vision | [NHS: Headaches](https://www.nhs.uk/symptoms/headaches/) "vision or eye problems" | Headache not required. | | |
| RT01 | routine | Cold symptoms | [NHS: Common cold](https://www.nhs.uk/conditions/common-cold/) "your cold symptoms do not get better after 10 days" | Only for ages 5 to 64, with all warning signs explicitly denied, a stated duration, no risk condition and no other symptom. | | |
| RT02 | routine | Sore throat | [NHS: Sore throat](https://www.nhs.uk/symptoms/sore-throat/) "sore throat does not improve after a week" | Only for ages 5 to 64, with all warning signs explicitly denied, a stated duration, no risk condition and no other symptom. | | |
| RT03 | routine | Headache | [NHS: Headaches](https://www.nhs.uk/symptoms/headaches/) "not getting better or is getting worse" | Only for ages 5 to 64, with all warning signs explicitly denied, a stated duration, no risk condition and no other symptom. | | |

Anything that matches no rule, or has missing information, is "unable to assess".

## Word patterns

- **Chest pain, pressure or tightness** (`chest_pain`): `chest (pain|pressure|tightness|discomfort)` `(pain|pressure|tight\w*|heav\w+|crushing|squeezing) (in|on) (my |the |his |her )?chest` `chest\b.{0,25}\b(hurts?|tight|heavy|crushing|painful)` `nyeri dada` `sakit dada` `dada\b.{0,25}\b(sakit|nyeri|tertekan|terhimpit|ditindih)`
- **Severe difficulty breathing** (`severe_breathing`): `(can ?not|can'?t|unable to|struggling to|hard to|difficulty) breath\w*` `severe (shortness of breath|breathing)` `gasping` `choking` `sesak (na[pf]as )?(berat|parah|sekali|banget)` `(barely|hardly) (catch my breath|breathe)` `suffocat\w+` `breathing (feels )?impossible` `(na[pf]as|bernapas).{0,15}sesak (berat|sekali|parah)` `(tidak|gak|ga|nggak|susah|sulit) (bisa )?(ber)?na[pf]as`
- **Blue, grey or very pale lips or skin** (`blue_skin`): `(lips?|skin|face)\b.{0,25}\b(blue|grey|gray)` `(blue|grey|gray) lips` `(bibir|kulit).{0,20}(biru|membiru|kebiruan)`
- **Face drooping, arm or leg weakness, or speech trouble** (`stroke_signs`): `face\b.{0,20}droop\w*` `droop\w* (face|on one side)` `slurr?ed speech` `speech\b.{0,15}slurr?\w*` `(can ?not|can'?t|trouble|difficulty) (speak|talk)\w*` `(numb\w*|weak\w*)\b.{0,30}\b(one side|face|arm|leg)` `(face|arm|leg)\b.{0,20}\b(numb|weak|paraly)\w*` `sudden\w*.{0,20}(trouble seeing|vision loss|lost (my )?vision)` `\bpelo\b` `(wajah|muka|mulut).{0,15}(mencong|miring|turun)` `(tangan|lengan|kaki|separuh badan).{0,25}(lemas|lemah|lumpuh|mati rasa|tidak bisa (di)?(angkat|gerak))` `bicara.{0,12}(cadel|tidak jelas)`
- **Passed out or hard to wake** (`unconscious`): `passed out` `pass(ing)? out` `faint(ed|ing)\b` `unconscious` `unresponsive` `not waking( up)?` `(can ?not|can'?t|won'?t) (be )?(woken|wake)` `lost consciousness` `pingsan` `tidak sadar\w*` `tak sadar\w*` `(tidak|susah|sulit) (bisa )?dibangunkan`
- **Seizure or fit** (`seizure`): `seizures?\b` `convuls\w+` `having a fit` `kejang`
- **Heavy bleeding or bleeding that will not stop** (`heavy_bleeding`): `bleed\w*.{0,20}(won'?t|will not|does not|doesn'?t|cannot|can'?t) stop` `(won'?t|will not|can'?t|cannot) stop (the )?bleed\w*` `(heavy|severe|a lot of) (bleeding|blood)` `(per)?darah\w*.{0,20}(tidak|gak|ga|nggak) (mau |bisa )?berhenti` `perdarahan (hebat|banyak)` `terus berdarah` `bleeding non-?stop` `blood is pouring`
- **Vomiting or coughing up much blood, or black stool** (`vomit_blood`): `(vomit\w*|throw\w* up) blood` `coffee ground` `ground coffee` `black (and sticky )?(poo|stool)\w*` `muntah darah` `(bab|tinja|feses) (berwarna )?hitam`
- **Thoughts of suicide or self-harm** (`suicidal`): `suicid\w+` `kill(ing)? (myself|himself|herself|themselves)` `end(ing)? (my|his|her) life` `thinking about ending it` `(do not|don'?t) want to (be alive|live)( anymore)?` `want to die` `hurt(ing)? myself` `self[- ]harm\w*` `bunuh diri` `ingin mati` `mau mati` `mengakhiri hidup` `(menyakiti|melukai) diri` `no way out` `hidup.{0,25}(tidak|gak|nggak) ada harapan`
- **Swelling of throat, tongue or lips, or cannot swallow** (`airway_swelling`): `(throat|tongue|lips?|mouth)\b.{0,20}\bswell\w*` `swollen (throat|tongue|lips?)` `(can ?not|can'?t|unable to|hard to|difficulty) swallow\w*` `anaphyla\w+` `(tenggorokan|lidah|bibir).{0,20}bengkak` `(tidak|susah|sulit) (bisa )?menelan`
- **Sudden severe headache** (`sudden_severe_headache`): `worst headache` `sudden\w*.{0,20}(severe|extreme\w*|terrible) headache` `headache.{0,40}sudden\w*` `thunderclap` `sakit kepala (hebat |parah )?(men)?dadak` `sakit kepala (paling )?(hebat|parah) (seumur hidup|yang pernah)`
- **Stiff neck, or a rash that does not fade** (`stiff_neck_or_rash`): `stiff neck` `neck (is )?stiff` `rash.{0,30}(not|n't) fade` `leher kaku` `kaku kuduk`
- **Sudden confusion** (`confusion`): `confus(ed|ion)` `disoriented` `linglung` `bicara ngawur`
- **Swallowed something harmful or overdose** (`poisoning`): `overdos\w+` `poison(ed|ing)\b` `swallow\w*.{0,30}(pills|bleach|chemical|poison|battery|bottle)` `(took|taken|taking) too many (pills|tablets)` `minum (terlalu )?banyak obat` `keracunan (obat|bahan kimia)` `(minum|menelan|tertelan).{0,25}(racun|pemutih|bahan kimia|baterai|obat terlalu banyak)`
- **Sudden or severe stomach pain** (`severe_abdo`): `(severe|sudden) (stomach|abdominal|tummy|belly) pain` `(stomach|abdominal|tummy|belly) pain.{0,30}(severe|sudden)` `(belly|stomach|abdomen) is (rigid|hard)` `nyeri perut (hebat|berat|mendadak)` `sakit perut (hebat|parah|mendadak|sekali)`
- **Head injury** (`head_injury`): `(hit|hurt|bumped|banged) (my|his|her|their) head` `head (injury|trauma)` `kepala.{0,15}(terbentur|terpukul)` `(terbentur|benturan).{0,15}kepala` `cedera kepala`
- **Dengue fever mentioned** (`dengue`): `dengue` `demam berdarah` `\bdbd\b`
- **Bleeding gums or nose** (`gum_nose_bleed`): `bleeding gums` `gums (are )?bleeding` `nose ?bleed\w*` `gusi berdarah` `mimisan`
- **Repeated vomiting or cannot keep fluids down** (`persistent_vomiting`): `keep (vomiting|being sick|throwing up)` `(can ?not|can'?t) keep (any )?(fluids?|water|anything) down` `vomit\w* (non-?stop|repeatedly|constantly)` `muntah (terus|berkali)`
- **Fever** (`fever`): `fever(ish)?\b` `high temperature` `demam`
- **Signs of dehydration** (`dehydration`): `dehydrat\w+` `(barely|hardly|not) (urinat\w+|pee\w*)` `very dizzy` `dehidrasi` `jarang (buang air kecil|kencing|pipis)`
- **Diarrhoea** (`diarrhoea`): `diarrh?o?ea` `loose stools?` `diare` `mencret`
- **Blood in stool** (`blood_in_stool`): `bloody (diarrh\w+|stool\w*|poo)` `blood in (my |the )?(stool|poo)` `(bab|diare|tinja) berdarah`
- **Pain when urinating** (`urinary`): `(burn\w*|pain\w*|hurts?) (when|while) (i )?(pee|urinat)\w*` `(perih|sakit|nyeri|panas) (saat|waktu|ketika) (buang air kecil|kencing|pipis)` `anyang`
- **Lower back or flank pain** (`back_pain`): `lower back` `flank` `nyeri pinggang` `sakit pinggang`
- **Animal or human bite** (`bite`): `(dog|cat|monkey|animal|human|snake|bat)\b.{0,15}\b(bit|bite|bitten)` `bitten by` `(digigit|gigitan) (anjing|kucing|monyet|ular|hewan|orang)`
- **Hot, swollen or pus-filled skin or wound** (`wound_infection`): `\bpus\b` `(wound|cut|skin).{0,40}(hot|swollen)` `cellulitis` `(luka|kulit).{0,30}(nanah|bengkak)`
- **Very painful eye** (`eye_pain`): `eye\b.{0,15}\b(pain\w*|hurts?)` `(painful|sore) eye` `mata.{0,15}(sakit|nyeri)`
- **Change in vision** (`vision_change`): `vision\b.{0,15}\b(blurr?\w*|loss|lost|change\w*)` `(blurr?y|double|loss of) vision` `(penglihatan|pandangan).{0,15}(kabur|hilang|ganda)`
- **Pregnant** (`pregnant`): `\bpregnan\w+` `\bhamil\b`
- **Baby under 3 months** (`infant_young`): `(baby|infant).{0,20}\b([12]|one|two)[ -]months?` `\b([0-9]|1[0-2])[ -]weeks?[ -]old` `bayi.{0,20}\b([012]|satu|dua) bulan` `bayi baru lahir` `newborn`
- **Coughing up blood** (`coughing_blood`): `cough\w* (up )?blood` `blood when (i )?cough` `batuk (ber)?darah`
- **Cold symptoms** (`common_cold`): `runny nose` `stuffy nose` `blocked nose` `\bsneez\w+` `common cold` `\bpilek\b` `hidung (meler|tersumbat)` `bersin`
- **Sore throat** (`sore_throat`): `sore throat` `throat (is )?(sore|scratchy)` `(sakit|radang) tenggorokan` `tenggorokan.{0,12}(sakit|gatal|perih)`
- **Headache** (`headache`): `headache` `sakit kepala`

## Sign-off

Reviewer:

Qualification and registration number:

Date:

Decision (approve / approve with changes / reject):

After approval, copy the reviewer, qualification, date and hash `89658f4474f498f9` into `config/triage.php`.
