/**
 * Pharmasis Multilingual Localization Engine (i18n) & AI Drug Humanizer
 * Supports 11 Global Languages with Clean Minimalist iOS Glass Preloader
 */

const PHARMASIS_LANGUAGES = [
    { code: 'en', name: 'English', native: 'English', dir: 'ltr' },
    { code: 'id', name: 'Indonesian', native: 'Bahasa Indonesia', dir: 'ltr' },
    { code: 'ja', name: 'Japanese', native: '日本語', dir: 'ltr' },
    { code: 'es', name: 'Spanish', native: 'Español', dir: 'ltr' },
    { code: 'zh', name: 'Chinese', native: '简体中文', dir: 'ltr' },
    { code: 'ar', name: 'Arabic', native: 'العربية', dir: 'rtl' },
    { code: 'fr', name: 'French', native: 'Français', dir: 'ltr' },
    { code: 'de', name: 'German', native: 'Deutsch', dir: 'ltr' },
    { code: 'ko', name: 'Korean', native: '한국어', dir: 'ltr' },
    { code: 'pt', name: 'Portuguese', native: 'Português', dir: 'ltr' },
    { code: 'ru', name: 'Russian', native: 'Русский', dir: 'ltr' },
];

const PHARMASIS_TRANSLATIONS = {
    // ── 1. ENGLISH ──
    en: {
        nav_home: 'Home',
        nav_interactions: 'Interactions',
        nav_browse: 'Browse Medicines',
        nav_search_placeholder: 'Search medicines, generics, drug classes…',
        nav_search_mobile: 'Search medicines…',
        nav_see_all: 'See all results for',
        nav_no_result: 'No medicine found for',
        
        hero_badge: 'Next-Gen Clinical Intelligence',
        hero_title_1: 'What you feel',
        hero_title_2: 'deserves a real answer.',
        hero_desc: "Describe your symptoms in plain language. Pharmasis' MediCore AI analyzes your input, asks the follow-up questions that matter, and delivers a clinically structured conclusion for both patients and healthcare providers.",
        hero_placeholder: "Describe your symptoms in detail... Example: I've had a dry cough and a 38°C fever since two days ago, along with a headache and fatigue.",
        hero_hint: 'Describe your symptoms... Press Enter to start screening.',
        hero_btn_start: 'Start AI Screening',
        hero_quick_chips: 'Quick Clinical Queries:',

        inquiries_badge: 'Clinical Inquiries Overview',
        inquiries_title: 'Symptom Case Studies & Real-Time Triage',
        inquiries_desc: 'Explore authentic patient presentations processed through the MediCore multi-tier clinical verification engine.',
        inquiries_card_tab: 'CLINICAL PROTOCOL',
        inquiries_reported: 'Reported Symptoms',
        inquiries_action: 'Initiate AI Assessment',

        // Active Medicine Repository
        med_repo_badge: 'Active Medicine Repository',
        med_repo_title_1: 'Continuous pharmacological',
        med_repo_title_2: 'catalog stream',
        med_repo_desc: 'Hover anywhere over the catalog to pause and inspect formulations in detail.',
        med_repo_autoscroll: 'Auto-scrolling · Hover to pause',
        med_repo_browse_btn: 'Browse Full Directory',
        med_repo_quick_info: 'Quick Info',
        med_repo_monograph: 'Monograph',
        med_repo_indications: 'Therapeutic Indications',
        med_repo_adverse: 'Notable Adverse Effects & Precautions',
        med_repo_analyze_btn: 'Analyze with MediCheck',
        med_repo_open_monograph: 'Open Full Monograph',
        med_repo_ai_badge: 'AI Humanized Summary',
        med_repo_context_lang: 'Context Language',
        med_repo_switch_lang_btn: 'Change Context Language',
        med_repo_generic: 'Generic',
        med_repo_standard_formulation: 'Standard Formulation',

        arch_badge: 'Architecture & Engine',
        arch_title: 'MediCore Clinical Architecture',
        arch_desc: 'Three specialized layers engineered to convert unstructured human experience into validated clinical insights.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Pharmacological & Interaction Engine',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Multi-Turn Symptom Triage',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Complex Multi-Drug Compatibility',

        dossier_query_title: '01 / Sample Query & Clinical Entry',
        dossier_pipeline_title: '02 / Execution Pipeline',
        dossier_output_title: '03 / Clinical Output Preview',

        case1_query: 'Patient in mid-50s on long-term Lisinopril for hypertension presents with new-onset dry nagging cough persisting for 4 weeks.',
        case1_patient_out: 'Dry cough is a known, non-allergic effect of ACE inhibitors occurring in 10-20% of patients due to bradykinin accumulation. Not infectious.',
        case1_clinician_out: 'Etiology: ACEI-induced bradykinin/substance-P accumulation. Recommendation: Discontinue Lisinopril, switch to ARB (e.g., Losartan).',

        case2_query: '42yo female reporting 3 days of progressive right upper quadrant abdominal pain postprandially, with mild nausea and low-grade fever.',
        case2_patient_out: 'Symptoms indicate gallbladder inflammation (biliary colic or cholecystitis). Requires direct physical exam and ultrasound.',
        case2_clinician_out: 'Differential: Acute cholecystitis vs symptomatic cholelithiasis. Recommended labs: CBC, LFTs, Lipase, Right upper quadrant ultrasound.',

        case3_query: '70yo male taking Warfarin for atrial fibrillation started on high-dose Fluconazole for oral candidiasis.',
        case3_patient_out: 'Major interaction alert: Fluconazole significantly amplifies Warfarin blood-thinning potency, creating dangerous bleeding risk.',
        case3_clinician_out: 'Mechanism: CYP2C9 inhibition by Fluconazole slows S-warfarin metabolism. Action: Reduce Warfarin dose by 50% & monitor INR closely.',

        mission_badge: 'Clinical Integrity',
        mission_title: 'Our Mission & Clinical Architecture',
        mission_desc: 'Pharmasis was created to bridge the critical gap between confusing internet medical searches and actionable clinical understanding. We do not replace healthcare providers—we empower patients with objective triage and equip clinicians with structured summaries.',
        pillar_1_title: 'Verified Evidence',
        pillar_1_desc: 'Cross-referenced against 16,000+ curated clinical profiles from WebMD and 67,000+ approved drug records from the U.S. FDA OpenFDA database — totaling 83,000+ verified pharmacological entries.',
        pillar_2_title: 'Objective Triage',
        pillar_2_desc: 'Real-time severity stratification based on verified emergency triage standards.',
        pillar_3_title: 'Dual-Format Syntheses',
        pillar_3_desc: 'Produces easy-to-understand explanations for patients and concise summaries for physicians.',
        metric_1_label: 'Verified Drug Records',
        metric_2_label: 'Triage Accuracy Target',
        metric_3_label: 'Clinical Summaries Generated',

        cta_badge: 'Direct Health Navigation',
        cta_title: 'Healthcare Intelligence Platform',
        cta_desc: 'Search our directory of over 67,000 verified medicines, analyze interactions, and access instant symptom screening.',
        cta_btn: 'Launch MediCore Screening',

        preloader_loading_text: 'Changing language...'
    },

    // ── 2. INDONESIAN ──
    id: {
        nav_home: 'Beranda',
        nav_interactions: 'Interaksi Obat',
        nav_browse: 'Direktori Obat',
        nav_search_placeholder: 'Cari obat, generik, golongan obat…',
        nav_search_mobile: 'Cari obat…',
        nav_see_all: 'Lihat semua hasil untuk',
        nav_no_result: 'Obat tidak ditemukan untuk',
        
        hero_badge: 'Kecerdasan Klinis Generasi Baru',
        hero_title_1: 'Apa yang Anda rasakan',
        hero_title_2: 'layak mendapat jawaban nyata.',
        hero_desc: 'Jelaskan gejala Anda dengan bahasa sehari-hari. MediCore AI dari Pharmasis menganalisis keluhan Anda, mengajukan pertanyaan lanjutan yang krusial, dan menghasilkan kesimpulan klinis terstruktur baik bagi pasien maupun tenaga medis.',
        hero_placeholder: 'Jelaskan keluhan Anda secara detail... Contoh: Saya mengalami batuk kering dan demam 38°C sejak 2 hari lalu disertai sakit kepala dan badan lemas.',
        hero_hint: 'Jelaskan gejala Anda... Tekan Enter untuk memulai skrining.',
        hero_btn_start: 'Mulai Skrining AI',
        hero_quick_chips: 'Pencarian Klinis Cepat:',

        inquiries_badge: 'Ikhtisar Konsultasi Klinis',
        inquiries_title: 'Studi Kasus Gejala & Triase Real-Time',
        inquiries_desc: 'Pelajari berbagai presentasi klinis pasien yang diproses melalui mesin verifikasi bertingkat MediCore.',
        inquiries_card_tab: 'PROTOKOL KLINIS',
        inquiries_reported: 'Gejala Dilaporkan',
        inquiries_action: 'Mulai Penilaian AI',

        med_repo_badge: 'Repositori Obat Aktif',
        med_repo_title_1: 'Aliran katalog',
        med_repo_title_2: 'farmakologi berkelanjutan',
        med_repo_desc: 'Arahkan kursor ke katalog untuk menjeda dan memeriksa detail formulasi obat.',
        med_repo_autoscroll: 'Bergulir Otomatis · Arahkan untuk Jeda',
        med_repo_browse_btn: 'Jelajahi Direktori Lengkap',
        med_repo_quick_info: 'Info Ringkas',
        med_repo_monograph: 'Monograf',
        med_repo_indications: 'Indikasi Terapeutik',
        med_repo_adverse: 'Efek Samping & Perhatian Khusus',
        med_repo_analyze_btn: 'Analisis dengan MediCheck',
        med_repo_open_monograph: 'Buka Monograf Lengkap',
        med_repo_ai_badge: 'Ringkasan Dihumanisasi AI',
        med_repo_context_lang: 'Bahasa Konteks Data',
        med_repo_switch_lang_btn: 'Ubah Bahasa Detail',
        med_repo_generic: 'Generik',
        med_repo_standard_formulation: 'Formulasi Standar',

        arch_badge: 'Arsitektur & Mesin Analisis',
        arch_title: 'Arsitektur Klinis MediCore',
        arch_desc: 'Tiga lapisan terspesialisasi yang dirancang untuk mengubah keluhan subjektif menjadi wawasan klinis tervalidasi.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Mesin Farmakologi & Interaksi',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Triase Gejala Multi-Tahap',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Kompatibilitas Multi-Obat Kompleks',

        dossier_query_title: '01 / Pertanyaan Sampel & Entri Klinis',
        dossier_pipeline_title: '02 / Alur Eksekusi Sistem',
        dossier_output_title: '03 / Pratinjau Luaran Klinis',

        case1_query: 'Pasien usia 50-an tahun mengonsumsi Lisinopril jangka panjang untuk hipertensi mengeluhkan batuk kering menetap selama 4 minggu.',
        case1_patient_out: 'Batuk kering merupakan efek samping non-alergi dari obat golongan ACE inhibitor pada 10-20% pasien akibat penumpukan bradikinin. Bukan infeksi.',
        case1_clinician_out: 'Etiologi: Akumulasi bradikinin & substansi-P akibat ACEI. Rekomendasi: Hentikan Lisinopril, ganti ke golongan ARB (misal: Losartan).',

        case2_query: 'Wanita 42 tahun mengeluhkan nyeri perut kanan atas progresif pasca-makan selama 3 hari, mual ringan, dan demam subfebris.',
        case2_patient_out: 'Gejala mengarah pada radang kantung empedu (kolik bilier atau kolesistitis). Memerlukan pemeriksaan fisik dan USG abdomen langsung.',
        case2_clinician_out: 'Diagnosis banding: Kolesistitis akut vs kolelitiasis simtomatik. Usulan lab: Darah lengkap, fungsi hati, lipase, dan USG abdomen kanan atas.',

        case3_query: 'Pria 70 tahun pengguna Warfarin untuk fibrilasi atrium baru saja diresepkan Flukonazol dosis tinggi untuk kandidiasis oral.',
        case3_patient_out: 'Peringatan interaksi kritis: Flukonazol meningkatkan efek pengencer darah Warfarin secara drastis, memicu risiko perdarahan berbahaya.',
        case3_clinician_out: 'Mekanisme: Inhibisi CYP2C9 oleh Flukonazol menurunkan metabolisme S-warfarin. Tindakan: Turunkan dosis Warfarin 50% & pantau ketat INR.',

        mission_badge: 'Integritas Klinis',
        mission_title: 'Misi & Integritas Klinis Kami',
        mission_desc: 'Pharmasis didirikan untuk menjembatani jurang antara pencarian medis internet yang membingungkan dengan pemahaman klinis yang terstruktur. Kami tidak menggantikan dokter—kami memberdayakan pasien dengan triase objektif dan membekali tenaga medis dengan ringkasan terstruktur.',
        pillar_1_title: 'Bukti Ilmiah Terverifikasi',
        pillar_1_desc: 'Divalidasi silang terhadap 16.000+ profil klinis WebMD dan 67.000+ rekam obat FDA AS OpenFDA — total 83.000+ entri farmakologis terverifikasi.',
        pillar_2_title: 'Triase Objektif',
        pillar_2_desc: 'Stratifikasi tingkat keparahan real-time berbasis standar triase gawat darurat medis terverifikasi.',
        pillar_3_title: 'Sintesis Dua Format',
        pillar_3_desc: 'Menghasilkan bahasa yang ramah dipahami pasien sekaligus ringkasan profesional untuk dokter pemeriksa.',
        metric_1_label: 'Rekam Data Obat Terverifikasi',
        metric_2_label: 'Target Akurasi Triase',
        metric_3_label: 'Ringkasan Klinis Dihasilkan',

        cta_badge: 'Navigasi Kesehatan Langsung',
        cta_title: 'Platform Kecerdasan Kesehatan',
        cta_desc: 'Jelajahi direktori lebih dari 67.000 obat terverifikasi, periksa interaksi antar obat, dan dapatkan analisis skrining gejala seketika.',
        cta_btn: 'Mulai Skrining MediCore',

        preloader_loading_text: 'Memperbarui bahasa...'
    },

    // ── 3. JAPANESE ──
    ja: {
        nav_home: 'ホーム',
        nav_interactions: '薬物相互作用',
        nav_browse: '医薬品ディレクトリ',
        nav_search_placeholder: '医薬品名、一般名、薬効分類を検索…',
        nav_search_mobile: '医薬品を検索…',
        nav_see_all: 'すべての検索結果を表示:',
        nav_no_result: '該当する医薬品は見つかりませんでした:',
        
        hero_badge: '次世代臨床インテリジェンス',
        hero_title_1: 'あなたの感じる不安に、',
        hero_title_2: '確かな医学的回答を。',
        hero_desc: '日常の言葉で症状をお伝えください。PharmasisのMediCore AIが訴えを分析し、重要な問診を行い、患者様と医療従事者の双方に向けた構造化された臨床評価をお届けします。',
        hero_placeholder: '症状を詳しく入力してください... 例: 2日前から38度の発熱と乾いた咳が続き、頭痛と全身の倦怠感があります。',
        hero_hint: '症状を入力し、Enterキーでスクリーニングを開始します。',
        hero_btn_start: 'AIスクリーニング開始',
        hero_quick_chips: 'よくある症状の検索:',

        inquiries_badge: '臨床問診ケーススタディ',
        inquiries_title: '症例スタディとリアルタイムトリアージ',
        inquiries_desc: 'MediCoreのマルチレイヤー臨床検証エンジンによって処理された実際の患者症例を確認できます。',
        inquiries_card_tab: '臨床プロトコル',
        inquiries_reported: '報告された主訴',
        inquiries_action: 'AI評価を開始',

        med_repo_badge: '医薬品リポジトリ',
        med_repo_title_1: '継続的な薬理学的',
        med_repo_title_2: 'カタログストリーム',
        med_repo_desc: 'カタログの上にカーソルを置くと一時停止し、詳細な製剤情報を確認できます。',
        med_repo_autoscroll: '自動スクロール・ホバーで停止',
        med_repo_browse_btn: 'すべての医薬品を表示',
        med_repo_quick_info: 'クイック情報',
        med_repo_monograph: 'モノグラフ',
        med_repo_indications: '適応症・主な効能',
        med_repo_adverse: '副作用および注意事項',
        med_repo_analyze_btn: 'MediCheckで分析',
        med_repo_open_monograph: '詳細モノグラフを開く',
        med_repo_ai_badge: 'AI最適化臨床要約',
        med_repo_context_lang: 'データ表示言語',
        med_repo_switch_lang_btn: '言語を切り替える',
        med_repo_generic: '一般名',
        med_repo_standard_formulation: '標準製剤',

        arch_badge: 'アーキテクチャ＆エンジン',
        arch_title: 'MediCore臨床アーキテクチャ',
        arch_desc: '非構造化な主観的症状を検証済みの臨床洞察へと変換する3つの専門レイヤー。',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: '薬理・相互作用エンジン',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: '多段階トリアージエンジン',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: '多剤併用適合性解析',

        dossier_query_title: '01 / サンプル症例入力',
        dossier_pipeline_title: '02 / システム実行パイプライン',
        dossier_output_title: '03 / 臨床出力プレビュー',

        case1_query: '高血圧でリシノプリルを長期服用中の50代患者。4週間持続する空咳を主訴に来院。',
        case1_patient_out: '空咳はACE阻害薬の既知の非アレルギー性副作用（ブラジキニン蓄積による）であり、10〜20%の患者に生じます。感染症ではありません。',
        case1_clinician_out: '病態: ACE阻害によるブラジキニン蓄積。推奨処置: リシノプリルを中止し、ARB（ロサルタン等）への変更を検討。',

        case2_query: '42歳女性。食後に増悪する右季肋部痛が3日間持続、軽度の悪心および微熱を伴う。',
        case2_patient_out: '胆嚢の炎症（胆石疝痛または胆嚢炎）が強く疑われます。医師による直接の触診および超音波検査が必要です。',
        case2_clinician_out: '鑑別診断: 急性胆嚢炎 vs 有症状胆嚢結石。推奨検査: 血算、肝機能、リパーゼ、腹部超音波。',

        case3_query: '心房細動でワルファリン服用中の70歳男性。口腔カンジダ症に対しフルコナゾール高用量が処方された。',
        case3_patient_out: '重大な相互作用警告: フルコナゾールがワルファリンの抗凝固作用を著しく増強し、危険な出血リスクを引き起こします。',
        case3_clinician_out: '機序: フルコナゾールによるCYP2C9阻害でS-ワルファリン代謝低下。処置: ワルファリン用量を半減しINRを厳重監視。',

        mission_badge: '臨床的完全性',
        mission_title: '私たちの使命と臨床的インテグリティ',
        mission_desc: 'Pharmasisは、曖昧なネット検索と専門的な臨床理解のギャップを埋めるために誕生しました。医師の代わりではなく、患者に客観的トリアージを提供し、医療従事者に構造化要約を届けます。',
        pillar_1_title: '検証済みエビデンス',
        pillar_1_desc: 'WebMDの16,000以上の臨床プロファイルと米国FDA OpenFDAデータベースの67,000以上の承認薬記録を相互参照 — 合計83,000以上の検証済み薬理エントリー。',
        pillar_2_title: '客観的トリアージ',
        pillar_2_desc: '国際的な救急トリアージ基準に基づくリアルタイムの重症度層別化。',
        pillar_3_title: 'デュアルフォーマット出力',
        pillar_3_desc: '患者向けの平易な説明文と医師向けの専門的サマリーを同時に生成。',
        metric_1_label: '検証済み医薬品記録',
        metric_2_label: 'トリアージ精度目標',
        metric_3_label: '生成された臨床サマリー',

        cta_badge: 'ヘルスケアナビゲーション',
        cta_title: '医療インテリジェンスプラットフォーム',
        cta_desc: '67,000件以上の認証済み医薬品データベース、相互作用チェック、迅速なAI問診をご活用ください。',
        cta_btn: 'MediCoreスクリーニングを開始',

        preloader_loading_text: '言語を切り替えています...'
    },

    // ── 4. SPANISH ──
    es: {
        nav_home: 'Inicio',
        nav_interactions: 'Interacciones',
        nav_browse: 'Directorio Médico',
        nav_search_placeholder: 'Buscar medicamentos, genéricos, clases…',
        nav_search_mobile: 'Buscar medicamentos…',
        nav_see_all: 'Ver todos los resultados para',
        nav_no_result: 'No se encontraron medicamentos para',
        
        hero_badge: 'Inteligencia Clínica de Próxima Generación',
        hero_title_1: 'Lo que sientes',
        hero_title_2: 'merece una respuesta real.',
        hero_desc: 'Describe tus síntomas en lenguaje natural. MediCore AI analiza tus síntomas, formula las preguntas de seguimiento necesarias y genera una conclusión clínica estructurada para pacientes y profesionales de la salud.',
        hero_placeholder: 'Describe tus síntomas detalladamente... Ejemplo: Tengo tos seca y 38°C de fiebre desde hace 2 días, junto con dolor de cabeza y fatiga.',
        hero_hint: 'Describe tus síntomas... Presiona Enter para iniciar el triaje.',
        hero_btn_start: 'Iniciar Evaluación AI',
        hero_quick_chips: 'Consultas Clínicas Rápidas:',

        inquiries_badge: 'Resumen de Consultas Clínicas',
        inquiries_title: 'Casos de Estudio y Triaje en Tiempo Real',
        inquiries_desc: 'Explora presentaciones reales de pacientes procesadas mediante el motor de verificación clínica MediCore.',
        inquiries_card_tab: 'PROTOCOLO CLÍNICO',
        inquiries_reported: 'Síntomas Notificados',
        inquiries_action: 'Iniciar Triaje AI',

        med_repo_badge: 'Repositorio de Medicamentos Activo',
        med_repo_title_1: 'Catálogo farmacológico',
        med_repo_title_2: 'en transmisión continua',
        med_repo_desc: 'Pasa el cursor sobre el catálogo para pausar e inspeccionar las formulaciones en detalle.',
        med_repo_autoscroll: 'Desplazamiento automático · Pausar al pasar el ratón',
        med_repo_browse_btn: 'Explorar Directorio Completo',
        med_repo_quick_info: 'Info Rápida',
        med_repo_monograph: 'Monografía',
        med_repo_indications: 'Indicaciones Terapéuticas',
        med_repo_adverse: 'Efectos Adversos y Precauciones',
        med_repo_analyze_btn: 'Analizar con MediCheck',
        med_repo_open_monograph: 'Abrir Monografía Completa',
        med_repo_ai_badge: 'Resumen Humanizado por IA',
        med_repo_context_lang: 'Idioma del Contexto',
        med_repo_switch_lang_btn: 'Cambiar Idioma de Datos',
        med_repo_generic: 'Genérico',
        med_repo_standard_formulation: 'Formulación Estándar',

        arch_badge: 'Arquitectura y Motor Clínico',
        arch_title: 'Arquitectura Clínica MediCore',
        arch_desc: 'Tres capas especializadas diseñadas para convertir síntomas humanos en conocimientos clínicos validados.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Motor de Farmacología e Interacciones',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Triaje de Síntomas Multietapa',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Compatibilidad Polifarmacéutica Compleja',

        dossier_query_title: '01 / Consulta de Muestra y Entrada',
        dossier_pipeline_title: '02 / Canal de Ejecución',
        dossier_output_title: '03 / Vista Previa de Salida Clínica',

        case1_query: 'Paciente de 50 años en tratamiento prolongado con Lisinopril para la hipertensión presenta tos seca persistente de 4 semanas.',
        case1_patient_out: 'La tos seca es un efecto secundario no alérgico conocido de los inhibidores de la ECA en 10-20% de pacientes por acumulación de bradicinina. No es infeccioso.',
        case1_clinician_out: 'Etiología: Acumulación de bradicinina inducida por IECA. Recomendación: Suspender Lisinopril y cambiar a un ARA-II (ej. Losartán).',

        case2_query: 'Mujer de 42 años con dolor progresivo en hipocondrio derecho posprandial de 3 días, náuseas leves y febrícula.',
        case2_patient_out: 'Los síntomas sugieren inflamación de la vesícula biliar (cólico biliar o colecistitis). Requiere exploración física y ecografía.',
        case2_clinician_out: 'Diagnóstico diferencial: Colecistitis aguda vs colelitiasis sintomática. Pruebas: Hemograma, perfil hepático, ecografía abdominal.',

        case3_query: 'Varón de 70 años en tratamiento con Warfarina al que se le prescribe Fluconazol a dosis altas para candidiasis oral.',
        case3_patient_out: 'Alerta grave de interacción: El fluconazol incrementa fuertemente el efecto anticoagulante de la warfarina, elevando el riesgo de hemorragia.',
        case3_clinician_out: 'Mecanismo: Inhibición del CYP2C9 por fluconazol. Acción: Reducir dosis de warfarina un 50% y monitorizar INR estrechamente.',

        mission_badge: 'Integridad Clínica',
        mission_title: 'Nuestra Misión e Integridad Clínica',
        mission_desc: 'Pharmasis nació para cerrar la brecha entre las búsquedas médicas confusas y la comprensión clínica real. No reemplazamos al médico: empoderamos al paciente con triaje objetivo.',
        pillar_1_title: 'Evidencia Verificada',
        pillar_1_desc: 'Verificado de forma cruzada con 16,000+ perfiles clínicos de WebMD y 67,000+ registros de medicamentos de la FDA de EE. UU. (OpenFDA) — un total de 83,000+ entradas farmacológicas verificadas.',
        pillar_2_title: 'Triaje Objetivo',
        pillar_2_desc: 'Estratificación de gravedad en tiempo real basada en estándares médicos de urgencias.',
        pillar_3_title: 'Síntesis Dual',
        pillar_3_desc: 'Genera lenguaje comprensible para el paciente y resúmenes estructurados para el médico.',
        metric_1_label: 'Registros de Medicamentos Verificados',
        metric_2_label: 'Precisión de Triaje Objetivo',
        metric_3_label: 'Resúmenes Clínicos Generados',

        cta_badge: 'Navegación Médica Directa',
        cta_title: 'Plataforma de Inteligencia Sanitaria',
        cta_desc: 'Consulta nuestro catálogo de más de 67,000 medicamentos verificados, analiza interacciones y accede a triaje de síntomas con IA.',
        cta_btn: 'Iniciar Triaje MediCore',

        preloader_loading_text: 'Cambiando idioma...'
    },

    // ── 5. CHINESE ──
    zh: {
        nav_home: '首页',
        nav_interactions: '药物相互作用',
        nav_browse: '药品目录',
        nav_search_placeholder: '搜索药品名、通用名、药物分类…',
        nav_search_mobile: '搜索药品…',
        nav_see_all: '查看所有搜索结果：',
        nav_no_result: '未找到相关药品：',
        
        hero_badge: '新一代临床智能系统',
        hero_title_1: '您的身体感受，',
        hero_title_2: '理应获得清晰严谨的答案。',
        hero_desc: '用平实清晰的语言描述您的症状。Pharmasis MediCore AI 将精准分析病情、跟进关键医学问诊，并为患者和医护人员生成结构化的临床评估结论。',
        hero_placeholder: '请详细描述您的症状... 例如：两天前开始出现干咳和38°C发烧，伴随头痛和全身乏力。',
        hero_hint: '描述您的症状... 按 Enter 键开始 AI 筛查。',
        hero_btn_start: '开始 AI 问诊筛查',
        hero_quick_chips: '快速临床检索：',

        inquiries_badge: '临床问诊概览',
        inquiries_title: '症状案例研究与实时分诊',
        inquiries_desc: '探索经过 MediCore 多层级临床验证引擎处理的真实患者主诉案例。',
        inquiries_card_tab: '临床方案',
        inquiries_reported: '报告的主诉症状',
        inquiries_action: '启动 AI 评估',

        med_repo_badge: '活跃药品知识库',
        med_repo_title_1: '持续滚动的',
        med_repo_title_2: '药理学目录流',
        med_repo_desc: '将光标悬停在目录上可暂停滚动并详细查看制剂信息。',
        med_repo_autoscroll: '自动滚动 · 悬停暂停',
        med_repo_browse_btn: '浏览完整目录',
        med_repo_quick_info: '快速信息',
        med_repo_monograph: '专业说明书',
        med_repo_indications: '主要适应症与药效',
        med_repo_adverse: '不良反应与注意事项',
        med_repo_analyze_btn: '使用 MediCheck 分析',
        med_repo_open_monograph: '打开完整药品说明书',
        med_repo_ai_badge: 'AI 通俗化临床摘要',
        med_repo_context_lang: '数据展示语言',
        med_repo_switch_lang_btn: '切换数据语言',
        med_repo_generic: '通用名',
        med_repo_standard_formulation: '标准剂型',

        arch_badge: '系统架构与引擎',
        arch_title: 'MediCore 临床架构',
        arch_desc: '三层专业技术架构，旨在将主观描述转化为经过临床验证的专业洞见。',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: '药理与相互作用引擎',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: '多轮症状分诊系统',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: '复杂多药联用兼容性',

        dossier_query_title: '01 / 样本病例与临床输入',
        dossier_pipeline_title: '02 / 核心执行流程',
        dossier_output_title: '03 / 临床输出预览',

        case1_query: '50多岁高血压患者，长期服用赖诺普利，主诉出现持续4周的干咳。',
        case1_patient_out: '干咳是 ACEI 类降压药已知的非过敏性副作用（缓激肽积聚引起），见于10-20%的患者。非感染所致。',
        case1_clinician_out: '病因：ACEI诱发的缓激肽积聚。处置建议：停用赖诺普利，转换为ARB类药物（如氯沙坦）。',

        case2_query: '42岁女性，进食后右上腹部持续加重疼痛3天，伴轻度恶心及低烧。',
        case2_patient_out: '症状高度提示胆囊炎症（胆绞痛或胆囊炎）。建议尽快进行面对面腹部触诊及超声检查。',
        case2_clinician_out: '鉴别诊断：急性胆囊炎 vs 症状性胆石症。建议检查：血常规、肝功能、脂肪酶及右上腹超声。',

        case3_query: '70岁男性，因房颤服用华法林，近期因口腔念珠菌病加用大剂量氟康唑。',
        case3_patient_out: '重大药物相互作用警报：氟康唑会极大增强华法林的抗凝作用，造成极高出血危险。',
        case3_clinician_out: '机制：氟康唑抑制CYP2C9导致S-华法林代谢减慢。处置：将华法林剂量减半并严密监测INR。',

        mission_badge: '临床诚信',
        mission_title: '我们的使命与临床准则',
        mission_desc: 'Pharmasis 致力于填补网络医疗搜索与真实临床理解之间的鸿沟。我们不取代医生，而是为患者提供客观分诊，并为医生提供清晰的结构化摘要。',
        pillar_1_title: '权威实证依据',
        pillar_1_desc: '交叉比对来自 WebMD 的 16,000+ 临床档案及美国 FDA OpenFDA 数据库的 67,000+ 药品记录 — 累计 83,000+ 项已验证药理条目。',
        pillar_2_title: '客观医学分诊',
        pillar_2_desc: '基于急诊分诊标准的实时病情轻重缓急层级评估。',
        pillar_3_title: '双向格式输出',
        pillar_3_desc: '同时生成通俗易懂的患者健康指南与严谨的医师临床摘要。',
        metric_1_label: '已验证药品记录',
        metric_2_label: '分诊目标准确率',
        metric_3_label: '已生成临床摘要',

        cta_badge: '精准健康导航',
        cta_title: '医疗智能综合平台',
        cta_desc: '查询超过67,000种认证药物信息，分析用药禁忌，即刻体验智能症状筛查。',
        cta_btn: '启动 MediCore 智能分诊',

        preloader_loading_text: '正在切换语言...'
    },

    // ── 6. ARABIC ──
    ar: {
        nav_home: 'الرئيسية',
        nav_interactions: 'التفاعلات الدوائية',
        nav_browse: 'دليل الأدوية',
        nav_search_placeholder: 'ابحث عن الأدوية، الأسماء العلمية، الفئات…',
        nav_search_mobile: 'ابحث عن الأدوية…',
        nav_see_all: 'عرض كافة النتائج لـ',
        nav_no_result: 'لم يتم العثور على أدوية لـ',
        
        hero_badge: 'ذكاء سريري من الجيل القادم',
        hero_title_1: 'ما تشعر به من أعراض',
        hero_title_2: 'يستحق إجابة طبية دقيقة.',
        hero_desc: 'صف أعراضك بلغتك اليومية البسيطة. يحلل نظام MediCore AI من Pharmasis حالتك، ويطرح الأسئلة السريرية اللازمة، ويقدم تقييماً طبياً منظماً للمرضى والأطباء.',
        hero_placeholder: 'صف أعراضك بالتفصيل... مثال: أعاني من سعال جاف وحرارة 38 مئوية منذ يومين مع صداع وإرهاق عام.',
        hero_hint: 'صف أعراضك... اضغط Enter لبدء الفحص السريري.',
        hero_btn_start: 'بدء الفحص بالذكاء الاصطناعي',
        hero_quick_chips: 'استفسارات سريرية شائعة:',

        inquiries_badge: 'نظرة عامة على الاستشارات السريرية',
        inquiries_title: 'دراسات الحالات والفرز الطبي الفوري',
        inquiries_desc: 'استكشف حالات المرضى المعالجة عبر محرك التحقق السريري متعدد الطبقات MediCore.',
        inquiries_card_tab: 'البروتوكول السريري',
        inquiries_reported: 'الأعراض المسجلة',
        inquiries_action: 'بدء التقييم الطبي',

        med_repo_badge: 'مستودع الأدوية النشط',
        med_repo_title_1: 'دليل الأدوية الصيدلانية',
        med_repo_title_2: 'المتجدد باستمرار',
        med_repo_desc: 'مرر المؤشر فوق الدليل للإيقاف المؤقت وفحص التركيبات الصيدلانية بالتفصيل.',
        med_repo_autoscroll: 'تمرير تلقائي · توقف عند التحويم',
        med_repo_browse_btn: 'تصفح الدليل الكامل',
        med_repo_quick_info: 'معلومات سريعة',
        med_repo_monograph: 'النشرة الطبية',
        med_repo_indications: 'دواعي الاستعمال العلاجية',
        med_repo_adverse: 'الآثار الجانبية والتحذيرات',
        med_repo_analyze_btn: 'تحليل عبر MediCheck',
        med_repo_open_monograph: 'فتح النشرة الكاملة',
        med_repo_ai_badge: 'ملخص طبي مبسط بالذكاء الاصطناعي',
        med_repo_context_lang: 'لغة بيانات الدواء',
        med_repo_switch_lang_btn: 'تغيير لغة الشرح',
        med_repo_generic: 'الاسم العلمي',
        med_repo_standard_formulation: 'تركيبة قياسية',

        arch_badge: 'البنية الهندسية والمحرك',
        arch_title: 'الهندسة السريرية لـ MediCore',
        arch_desc: 'ثلاث طبقات متخصصة مصممة لتحويل الأعراض البشرية إلى رؤى طبية موثوقة.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'محرك الصيدلة والتفاعلات',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'فرز الأعراض متعدد المراحل',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'توافق الأدوية المتعددة المعقدة',

        dossier_query_title: '01 / نموذج الاستشارة والمدخلات السريرية',
        dossier_pipeline_title: '02 / مسار التنفيذ والمعالجة',
        dossier_output_title: '03 / معاينة المخرجات السريرية',

        case1_query: 'مريض في منتصف الخمسينيات يتناول ليسينوبريل لضغط الدم يعاني من سعال جاف مستمر منذ 4 أسابيع.',
        case1_patient_out: 'السعال الجاف هو أثر جانبي غير تحسسي معروف لمثبطات ACE يصيب 10-20% من المرضى بسبب تراكم مادة البراديكينين. ليس عدوى.',
        case1_clinician_out: 'السبب: تراكم البراديكينين الناتج عن مثبطات ACE. التوصية: إيقاف ليسينوبريل والتحويل إلى مضادات مستقبلات الأنجيوتنسين (مثل لوسارتان).',

        case2_query: 'سيدة تبلغ 42 عاماً تعاني من ألم متصاعد في الجزء العلوي الأيمن من البطن بعد الأكل منذ 3 أيام مع غثيان وحمى خفيفة.',
        case2_patient_out: 'تشير الأعراض إلى التهاب المرارة. تتطلب الحالة فحصاً سريرياً مباشراً وموجات فوق صوتية للبطن.',
        case2_clinician_out: 'التشخيص التفريقي: التهاب المرارة الحاد مقابل حصوات المرارة المصحوبة بأعراض. الفحوصات: صورة دم كاملة، وظائف كبد، سونار بطن.',

        case3_query: 'مريض عمره 70 عاماً يتناول وارفارين ورُصف له فلوكونازول بجرعة عالية لعلاج داء المبيضات الفموي.',
        case3_patient_out: 'تحذير تفاعل دوائي خطير: يزيد فلوكونازول من فاعلية الوارفارين في سيولة الدم بشكل كبير مما يشكل خطراً شديداً للنزيف.',
        case3_clinician_out: 'الآلية: تثبيط CYP2C9 بفعل فلوكونازول يبطئ استقلاب الوارفارين. الإجراء: خفض جرعة الوارفارين بنسبة 50% ومراقبة INR بدقة.',

        mission_badge: 'النزاهة السريرية',
        mission_title: 'مهمتنا والنزاهة السريرية',
        mission_desc: 'أُنشئت Pharmasis لسد الفجوة بين عمليات البحث الطبي العشوائية والفهم السريري الحقيقي. نحن لا نستبدل الطبيب بل نُمكّن المرضى بفرز موضوعي ونزود الأطباء بملخصات دقيقة.',
        pillar_1_title: 'أدلة علمية موثقة',
        pillar_1_desc: 'تمت المطابقة المرجعية مع أكثر من 16,000 ملف سريري من WebMD و67,000 سجل دوائي من قاعدة بيانات FDA الأمريكية OpenFDA — بإجمالي يتجاوز 83,000 مدخل صيدلاني موثق.',
        pillar_2_title: 'فرز طبي موضوعي',
        pillar_2_desc: 'تصنيف فوري لمستوى خطورة الحالة وفق معايير فرز الطوارئ الطبية المعتمدة.',
        pillar_3_title: 'توليف ثنائي الصيغة',
        pillar_3_desc: 'صيغة واضحة ومبسطة للمريض وملخص علمي دقيق للطبيب المعالج.',
        metric_1_label: 'سجلات الأدوية الموثقة',
        metric_2_label: 'دقة الفرز المستهدفة',
        metric_3_label: 'ملخص سريري تم إنشاؤه',

        cta_badge: 'توجيه صحي مباشر',
        cta_title: 'منصة الذكاء الصحي المتكاملة',
        cta_desc: 'تصفح دليلنا الذي يضم أكثر من 67000 دواء معتمد، وافحص التفاعلات، واستفد من فرز الأعراض الفوري.',
        cta_btn: 'بدء فحص MediCore الذكي',

        preloader_loading_text: 'جاري تغيير اللغة...'
    },

    // ── 7. FRENCH ──
    fr: {
        nav_home: 'Accueil',
        nav_interactions: 'Interactions',
        nav_browse: 'Directoire Médical',
        nav_search_placeholder: 'Rechercher médicaments, génériques, classes…',
        nav_search_mobile: 'Rechercher un médicament…',
        nav_see_all: 'Voir tous les résultats pour',
        nav_no_result: 'Aucun médicament trouvé pour',
        
        hero_badge: 'Intelligence Clinique de Nouvelle Génération',
        hero_title_1: 'Ce que vous ressentez',
        hero_title_2: 'mérite une réponse médicale claire.',
        hero_desc: "Décrivez vos symptômes en langage simple. L'IA MediCore de Pharmasis analyse vos symptômes, pose les questions de suivi pertinentes et génère une synthèse clinique structurée pour les patients et les praticiens.",
        hero_placeholder: 'Décrivez vos symptômes en détail... Exemple : Toux sèche et fièvre à 38°C depuis 2 jours accompagnées de maux de tête et de fatigue.',
        hero_hint: 'Décrivez vos symptômes... Appuyez sur Entrée pour lancer le tri.',
        hero_btn_start: 'Lancer le Tri IA',
        hero_quick_chips: 'Consultations Cliniques Rapides :',

        inquiries_badge: 'Aperçu des Consultations Cliniques',
        inquiries_title: 'Études de Cas et Triage en Temps Réel',
        inquiries_desc: 'Explorez des présentations cliniques réelles traitées par le moteur de vérification MediCore.',
        inquiries_card_tab: 'PROTOCOLE CLINIQUE',
        inquiries_reported: 'Symptômes Notifiés',
        inquiries_action: 'Initier le Triage IA',

        med_repo_badge: 'Répertoire Médicamenteux Actif',
        med_repo_title_1: 'Catalogue pharmacologique',
        med_repo_title_2: 'en flux continu',
        med_repo_desc: 'Survolez le catalogue pour mettre en pause et examiner les formulations en détail.',
        med_repo_autoscroll: 'Défilement automatique · Pause au survol',
        med_repo_browse_btn: 'Parcourir le Directoire Complet',
        med_repo_quick_info: 'Info Rapide',
        med_repo_monograph: 'Monographie',
        med_repo_indications: 'Indications Thérapeutiques',
        med_repo_adverse: 'Effets Indésirables et Précautions',
        med_repo_analyze_btn: 'Analyser avec MediCheck',
        med_repo_open_monograph: 'Ouvrir la Monographie Complète',
        med_repo_ai_badge: 'Synthèse Humanisée par IA',
        med_repo_context_lang: 'Langue du Contexte',
        med_repo_switch_lang_btn: 'Modifier la Langue',
        med_repo_generic: 'Générique',
        med_repo_standard_formulation: 'Formulation Standard',

        arch_badge: 'Architecture & Moteur Clinique',
        arch_title: 'Architecture Clinique MediCore',
        arch_desc: 'Trois couches spécialisées conçues pour convertir les plaintes subjectives en conclusions médicales validées.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Moteur Pharmacologique & Interactions',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Triage de Symptômes Multi-Étapes',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Compatibilité Polymédicamenteuse',

        dossier_query_title: '01 / Exemple de Requête & Entrée Clinique',
        dossier_pipeline_title: '02 / Pipeline de Traitement',
        dossier_output_title: '03 / Aperçu de la Synthèse Clinique',

        case1_query: 'Patient d’une cinquantaine d’années sous Lisinopril au long cours pour HTA, présentant une toux sèche depuis 4 semaines.',
        case1_patient_out: 'La toux sèche est un effet indésirable non allergique fréquent des IEC (10-20% des patients) par accumulation de bradykinine. Non infectieux.',
        case1_clinician_out: 'Étiologie : Accumulation de bradykinine sous IEC. Conduite : Arrêt du Lisinopril et relais par un ARA-II (ex: Losartan).',

        case2_query: 'Femme de 42 ans se plaignant de douleurs de l’hypochondre droit postprandiales depuis 3 jours avec nausées et fébricule.',
        case2_patient_out: 'Les symptômes évoquent une inflammation biliaire (colique hépatique ou cholécystite). Examen physique et échographie requis.',
        case2_clinician_out: 'Diagnostic différentiel : Cholécystite aiguë vs lithiase biliaire symptomatique. Bilan : NFS, bilan hépatique, échographie abdominale.',

        case3_query: 'Homme de 70 ans sous Warfarine pour FA chez qui on prescrit du Fluconazole à forte dose pour candidose buccale.',
        case3_patient_out: 'Alerte d’interaction majeure : Le fluconazole décuple l’effet anticoagulant de la warfarine, entraînant un risque hémorragique sévère.',
        case3_clinician_out: 'Mécanisme : Inhibition du CYP2C9 par le fluconazole. Action : Réduire la posologie de warfarine de 50% et surveiller l’INR.',

        mission_badge: 'Intégrité Clinique',
        mission_title: 'Notre Mission & Intégrité Clinique',
        mission_desc: 'Pharmasis a été conçu pour combler le fossé entre les recherches médicales confuses sur le web et une réelle compréhension clinique.',
        pillar_1_title: 'Preuves Scientifiques Validées',
        pillar_1_desc: 'Recoupé avec 16 000+ profils cliniques WebMD et 67 000+ dossiers de médicaments de la base FDA OpenFDA — soit un total de 83 000+ entrées pharmacologiques vérifiées.',
        pillar_2_title: 'Triage Objectif',
        pillar_2_desc: 'Stratification de gravité en temps réel basée sur les normes reconnues de régulation médicale d’urgence.',
        pillar_3_title: 'Double Format de Sortie',
        pillar_3_desc: 'Génère des explications simples pour le patient et des synthèses structurées pour le médecin.',
        metric_1_label: 'Dossiers Médicamenteux Vérifiés',
        metric_2_label: 'Objectif de Précision de Triage',
        metric_3_label: 'Synthèses Médicales Générées',

        cta_badge: 'Navigation Santé Directe',
        cta_title: 'Plateforme d’Intelligence Médicale',
        cta_desc: 'Consultez plus de 67 000 médicaments répertoriés, analysez les interactions et profitez du triage intelligent.',
        cta_btn: 'Lancer le Triage MediCore',

        preloader_loading_text: 'Changement de langue en cours...'
    },

    // ── 8. GERMAN ──
    de: {
        nav_home: 'Startseite',
        nav_interactions: 'Interaktionen',
        nav_browse: 'Arzneimittelverzeichnis',
        nav_search_placeholder: 'Medikamente, Wirkstoffe, Wirkstoffklassen suchen…',
        nav_search_mobile: 'Medikamente suchen…',
        nav_see_all: 'Alle Ergebnisse anzeigen für',
        nav_no_result: 'Keine Medikamente gefunden für',
        
        hero_badge: 'Klinische Intelligenz der nächsten Generation',
        hero_title_1: 'Was Sie empfinden,',
        hero_title_2: 'verdient eine fundierte Antwort.',
        hero_desc: 'Beschreiben Sie Ihre Symptome in einfachen Worten. Die MediCore KI von Pharmasis analysiert Ihre Angaben, stellt gezielte Nachfragen und liefert eine strukturierte klinische Einschätzung für Patienten und Ärzte.',
        hero_placeholder: 'Beschreiben Sie Ihre Beschwerden im Detail... Beispiel: Ich habe seit 2 Tagen trockenen Husten und 38°C Fieber mit Kopfschmerzen und Abgeschlagenheit.',
        hero_hint: 'Symptome beschreiben... Enter drücken, um das Screening zu starten.',
        hero_btn_start: 'KI-Screening starten',
        hero_quick_chips: 'Häufige klinische Anfragen:',

        inquiries_badge: 'Klinische Fallübersicht',
        inquiries_title: 'Symptom-Fallstudien & Echtzeit-Triage',
        inquiries_desc: 'Entdecken Sie authentische Patientenfälle, verarbeitet durch die mehrstufige MediCore Verifizierungs-Engine.',
        inquiries_card_tab: 'KLINISCHES PROTOKOLL',
        inquiries_reported: 'Gemeldete Symptome',
        inquiries_action: 'KI-Beurteilung starten',

        med_repo_badge: 'Aktives Medikamenten-Verzeichnis',
        med_repo_title_1: 'Fortlaufender',
        med_repo_title_2: 'pharmakologischer Katalog',
        med_repo_desc: 'Fahren Sie mit der Maus über den Katalog, um das Scrollen zu pausieren und Details zu prüfen.',
        med_repo_autoscroll: 'Automatischer Bildlauf · Anhalten bei Hover',
        med_repo_browse_btn: 'Gesamtes Verzeichnis durchsuchen',
        med_repo_quick_info: 'Kurzinfo',
        med_repo_monograph: 'Monographie',
        med_repo_indications: 'Therapeutische Indikationen',
        med_repo_adverse: 'Wichtige Nebenwirkungen & Vorsichtsmaßnahmen',
        med_repo_analyze_btn: 'Mit MediCheck analysieren',
        med_repo_open_monograph: 'Vollständige Monographie öffnen',
        med_repo_ai_badge: 'KI-optimierte klinische Zusammenfassung',
        med_repo_context_lang: 'Kontext-Sprache',
        med_repo_switch_lang_btn: 'Sprache anpassen',
        med_repo_generic: 'Generikum',
        med_repo_standard_formulation: 'Standard-Formulierung',

        arch_badge: 'Architektur & Engine',
        arch_title: 'Klinische MediCore Architektur',
        arch_desc: 'Drei spezialisierte Ebenen wandeln subjektive Patientenerfahrungen in validierte klinische Erkenntnisse um.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Pharmakologie- & Interaktions-Engine',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Mehrstufige Symptomtriage',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Komplexe Polypharmazie-Kompatibilität',

        dossier_query_title: '01 / Beispielanfrage & Klinische Eingabe',
        dossier_pipeline_title: '02 / Ausführungspipeline',
        dossier_output_title: '03 / Klinische Ergebnisvorschau',

        case1_query: 'Patient (Mitte 50) unter Lisinopril-Dauertherapie wegen Hypertonie klagt über seit 4 Wochen anhaltenden trockenen Reizhusten.',
        case1_patient_out: 'Trockener Husten ist eine bekannte, nicht-allergische Nebenwirkung von ACE-Hemmern (10-20% der Patienten) durch Bradykinin-Akkumulation. Nicht infektiös.',
        case1_clinician_out: 'Ätiologie: ACEI-induzierte Bradykinin-Akkumulation. Empfehlung: Absetzen von Lisinopril, Umstellung auf AT1-Antagonisten (z.B. Losartan).',

        case2_query: '42-jährige Frau berichtet über seit 3 Tagen progrediente postprandiale Schmerzen im rechten Oberbauch mit Übelkeit und subfebrilen Temperaturen.',
        case2_patient_out: 'Symptome weisen auf Gallenblasenentzündung (Gallenkolik/Cholezystitis) hin. Körperliche Untersuchung und Oberbauchsonografie erforderlich.',
        case2_clinician_out: 'Differenzialdiagnose: Akute Cholezystitis vs symptomatische Cholezystolithiasis. Labor/Diagnostik: Blutbild, Leberwerte, Sonografie.',

        case3_query: '70-jähriger Mann unter Warfarin-Therapie erhält hochdosiertes Fluconazol wegen oraler Candidose.',
        case3_patient_out: 'Schwere Interaktionswarnung: Fluconazol verstärkt die gerinnungshemmende Wirkung von Warfarin massiv – extremes Blutungsrisiko.',
        case3_clinician_out: 'Mechanismus: CYP2C9-Inhibition durch Fluconazol hemmt S-Warfarin-Abbau. Maßnahme: Warfarin-Dosis um 50% reduzieren & INR engmaschig prüfen.',

        mission_badge: 'Klinische Integrität',
        mission_title: 'Unsere Mission & Klinische Integrität',
        mission_desc: 'Pharmasis schließt die Lücke zwischen verwirrenden Online-Suchen und fundiertem medizinischen Verständnis.',
        pillar_1_title: 'Validierte Evidenz',
        pillar_1_desc: 'Abgeglichen mit 16.000+ klinischen WebMD-Profilen und 67.000+ FDA OpenFDA-Arzneimitteleinträgen — insgesamt 83.000+ verifizierte pharmakologische Einträge.',
        pillar_2_title: 'Objektive Triage',
        pillar_2_desc: 'Echtzeit-Schweregradeinstufung basierend auf etablierten Notfalltriage-Standards.',
        pillar_3_title: 'Duales Ausgabeformat',
        pillar_3_desc: 'Verständliche Erklärungen für Patienten und präzise strukturierte Zusammenfassungen für Ärzte.',
        metric_1_label: 'Verifizierte Arzneimitteleinträge',
        metric_2_label: 'Triage-Zielgenauigkeit',
        metric_3_label: 'Erstellte klinische Berichte',

        cta_badge: 'Direkte Gesundheitsnavigation',
        cta_title: 'Plattform für Medizinische Intelligenz',
        cta_desc: 'Durchsuchen Sie über 67.000 verifizierte Medikamente, prüfen Sie Wechselwirkungen und nutzen Sie die Symptomtriage.',
        cta_btn: 'MediCore Triage starten',

        preloader_loading_text: 'Sprache wird gewechselt...'
    },

    // ── 9. KOREAN ──
    ko: {
        nav_home: '홈',
        nav_interactions: '약물 상호작용',
        nav_browse: '의약품 디렉토리',
        nav_search_placeholder: '의약품명, 성분명, 약효 분류 검색…',
        nav_search_mobile: '의약품 검색…',
        nav_see_all: '전체 검색 결과 보기:',
        nav_no_result: '일치하는 의약품이 없습니다:',
        
        hero_badge: '차세대 임상 인텔리전스',
        hero_title_1: '당신이 느끼는 증상에,',
        hero_title_2: '명확하고 전문적인 답변을.',
        hero_desc: '일상적인 언어로 증상을 설명해 주세요. Pharmasis의 MediCore AI가 입력을 분석하고 핵심 문진을 진행하여 환자와 의료진 모두를 위한 구조화된 임상 결론을 제공합니다.',
        hero_placeholder: '증상을 상세히 입력해 주세요... 예: 이틀 전부터 마른기침과 38°C 발열이 있고 두통과 피로감이 심합니다.',
        hero_hint: '증상을 입력한 후 Enter 키를 눌러 스크리닝을 시작하세요.',
        hero_btn_start: 'AI 스크리닝 시작',
        hero_quick_chips: '빠른 증상 검색:',

        inquiries_badge: '임상 문진 사례 개요',
        inquiries_title: '증상 사례 연구 및 실시간 트리아지',
        inquiries_desc: 'MediCore의 다층 임상 검증 엔진으로 처리된 실제 환자 진료 사례를 확인하세요.',
        inquiries_card_tab: '임상 프로토콜',
        inquiries_reported: '보고된 주요 증상',
        inquiries_action: 'AI 평가 시작',

        med_repo_badge: '활성 의약품 저장소',
        med_repo_title_1: '지속적으로 흐르는',
        med_repo_title_2: '약리학 카탈로그 스트림',
        med_repo_desc: '카탈로그 위에 마우스를 올리면 스크롤이 일시 정지되어 상세 정보를 확인할 수 있습니다.',
        med_repo_autoscroll: '자동 스크롤 · 마우스 오버 시 일시정지',
        med_repo_browse_btn: '전체 의약품 디렉토리 탐색',
        med_repo_quick_info: '빠른 정보',
        med_repo_monograph: '모노그래프',
        med_repo_indications: '주요 치료 적응증',
        med_repo_adverse: '주요 부작용 및 주의사항',
        med_repo_analyze_btn: 'MediCheck로 분석',
        med_repo_open_monograph: '전체 모노그래프 열기',
        med_repo_ai_badge: 'AI 이해하기 쉬운 임상 요약',
        med_repo_context_lang: '데이터 표시 언어',
        med_repo_switch_lang_btn: '언어 전환하기',
        med_repo_generic: '성분명',
        med_repo_standard_formulation: '표준 제형',

        arch_badge: '아키텍처 및 분석 엔진',
        arch_title: 'MediCore 임상 아키텍처',
        arch_desc: '주관적인 환자 증상을 검증된 임상적 통찰로 변환하는 세 가지 전문 계층.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: '약리학 및 상호작용 엔진',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: '다단계 증상 트리아지',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: '복합 다제약물 병용 적합성',

        dossier_query_title: '01 / 샘플 문진 및 입력 데이터',
        dossier_pipeline_title: '02 / 실행 파이프라인',
        dossier_output_title: '03 / 임상 출력 미리보기',

        case1_query: '고혈압으로 리시노프릴을 장기 복용 중인 50대 환자가 4주간 지속되는 마른기침을 호소.',
        case1_patient_out: '마른기침은 ACE 억제제의 알려진 비알레르기성 부작용(브라디키닌 축적)으로 환자의 10-20%에서 발생합니다. 감염이 아닙니다.',
        case1_clinician_out: '원인: ACEI 유발 브라디키닌 축적. 권고: 리시노프릴 중단 및 ARB 제제(로사르탄 등)로 전환 고려.',

        case2_query: '42세 여성이 3일간 식후 우상복부 통증 악화, 미열 및 경미한 구역감을 호소.',
        case2_patient_out: '담낭염 또는 담석증 가능성이 높습니다. 대면 신체 진찰 및 복부 초음파 검사가 필요합니다.',
        case2_clinician_out: '감별 진단: 급성 담낭염 vs 유증상 담석증. 추천 검사: 일반혈액검사, 간기능검사, 우상복부 초음파.',

        case3_query: '심방세동으로 와파린을 복용 중인 70세 남성에게 구강 칸디다증 치료를 위한 고용량 플루코나졸이 처방됨.',
        case3_patient_out: '중대한 약물 상호작용 경고: 플루코나졸이 와파린의 혈액 희석 효과를 급격히 증가시켜 심각한 출혈 위험을 유발합니다.',
        case3_clinician_out: '기전: 플루코나졸에 의한 CYP2C9 억제로 S-와파린 대사 저하. 조치: 와파린 용량 50% 감량 및 INR 집중 모니터링.',

        mission_badge: '임상적 무결성',
        mission_title: '우리의 사명과 임상적 신뢰성',
        mission_desc: 'Pharmasis는 불확실한 인터넷 검색과 전문 의료 지식 사이의 격차를 해소하기 위해 만들어졌습니다. 의사를 대체하는 것이 아닌, 환자에게 객관적인 트리아지를 제공합니다.',
        pillar_1_title: '검증된 의학적 근거',
        pillar_1_desc: 'WebMD의 16,000개 이상의 임상 프로필 및 미국 FDA OpenFDA 데이터베이스의 67,000개 이상의 의약품 기록과 교차 검증 — 총 83,000개 이상의 검증된 약학 데이터.',
        pillar_2_title: '객관적인 중증도 분류',
        pillar_2_desc: '응급 트리아지 표준에 기반한 실시간 중증도 계층화.',
        pillar_3_title: '맞춤형 이중 포맷',
        pillar_3_desc: '환자용 알기 쉬운 설명과 의료진용 전문 임상 요약을 동시에 생성.',
        metric_1_label: '검증된 의약품 기록',
        metric_2_label: '트리아지 목표 정확도',
        metric_3_label: '생성된 임상 요약 수',

        cta_badge: '직관적 헬스케어 내비게이션',
        cta_title: '헬스케어 인텔리전스 플랫폼',
        cta_desc: '67,000종 이상의 검증된 의약품 데이터베이스를 검색하고, 복약 상호작용을 확인하며 AI 증상 분석을 시작하세요.',
        cta_btn: 'MediCore 스크리닝 시작',

        preloader_loading_text: '언어를 변경하는 중입니다...'
    },

    // ── 10. PORTUGUESE ──
    pt: {
        nav_home: 'Início',
        nav_interactions: 'Interações',
        nav_browse: 'Diretório de Medicamentos',
        nav_search_placeholder: 'Buscar medicamentos, genéricos, classes…',
        nav_search_mobile: 'Buscar medicamentos…',
        nav_see_all: 'Ver todos os resultados para',
        nav_no_result: 'Nenhum medicamento encontrado para',
        
        hero_badge: 'Inteligência Clínica de Próxima Geração',
        hero_title_1: 'O que você sente',
        hero_title_2: 'merece uma resposta real.',
        hero_desc: 'Descreva seus sintomas em linguagem simples. O MediCore AI do Pharmasis analisa seu relato, realiza as perguntas clínicas essenciais e fornece uma avaliação estruturada para pacientes e médicos.',
        hero_placeholder: 'Descreva seus sintomas detalhadamente... Exemplo: Estou com tosse seca e febre de 38°C há 2 dias, além de dor de cabeça e fadiga.',
        hero_hint: 'Descreva seus sintomas... Pressione Enter para iniciar a triagem.',
        hero_btn_start: 'Iniciar Triagem IA',
        hero_quick_chips: 'Consultas Clínicas Rápidas:',

        inquiries_badge: 'Visão Geral das Consultas Clínicas',
        inquiries_title: 'Estudos de Casos e Triagem em Tempo Real',
        inquiries_desc: 'Conheça apresentações reais de pacientes processadas pelo motor de verificação MediCore.',
        inquiries_card_tab: 'PROTOCOLO CLÍNICO',
        inquiries_reported: 'Sintomas Relatados',
        inquiries_action: 'Iniciar Avaliação IA',

        med_repo_badge: 'Repositório Ativo de Medicamentos',
        med_repo_title_1: 'Catálogo farmacológico',
        med_repo_title_2: 'em transmissão contínua',
        med_repo_desc: 'Passe o mouse sobre o catálogo para pausar e inspecionar detalhes das formulações.',
        med_repo_autoscroll: 'Rolagem Automática · Pause ao passar o mouse',
        med_repo_browse_btn: 'Explorar Diretório Completo',
        med_repo_quick_info: 'Informações Rápidas',
        med_repo_monograph: 'Monografia',
        med_repo_indications: 'Indicações Terapêuticas',
        med_repo_adverse: 'Efeitos Adversos e Precauções',
        med_repo_analyze_btn: 'Analisar com MediCheck',
        med_repo_open_monograph: 'Abrir Monografia Completa',
        med_repo_ai_badge: 'Resumo Humanizado por IA',
        med_repo_context_lang: 'Idioma dos Dados',
        med_repo_switch_lang_btn: 'Alterar Idioma',
        med_repo_generic: 'Genérico',
        med_repo_standard_formulation: 'Formulação Padrão',

        arch_badge: 'Arquitetura e Motor Clínico',
        arch_title: 'Arquitetura Clínica MediCore',
        arch_desc: 'Três camadas especializadas desenvolvidas para transformar relatos subjetivos em evidências clínicas validadas.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Motor de Farmacologia e Interações',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Triagem de Sintomas em Múltiplas Etapas',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Compatibilidade em Polifarmácia Complexa',

        dossier_query_title: '01 / Consulta de Amostra e Entrada Clínica',
        dossier_pipeline_title: '02 / Fluxo de Execução',
        dossier_output_title: '03 / Pré-visualização do Relatório Clínico',

        case1_query: 'Paciente de 55 anos em uso contínuo de Lisinopril para hipertensão relata tosse seca persistente há 4 semanas.',
        case1_patient_out: 'A tosse seca é um efeito adverso não alérgico comum dos inibidores da ECA (10-20% dos pacientes) por acúmulo de bradicinina. Não é infeccioso.',
        case1_clinician_out: 'Etiologia: Acúmulo de bradicinina induzido por IECA. Conduta: Suspender Lisinopril e substituir por BRA (ex: Losartana).',

        case2_query: 'Mulher de 42 anos relata dor progressiva em hipocôndrio direito após refeições há 3 dias, náuseas leves e febre baixa.',
        case2_patient_out: 'Sintomas indicam inflamação na vesícula biliar (cólica biliar ou colecistite). Requer exame físico direto e ultrassonografia.',
        case2_clinician_out: 'Diagnóstico diferencial: Colecistite aguda vs colelitíase sintomática. Exames: Hemograma, função hepática e ultrassom de abdome.',

        case3_query: 'Homem de 70 anos em uso de Varfarina para fibrilação atrial inicia tratamento com Fluconazol em alta dose para candidíase oral.',
        case3_patient_out: 'Alerta grave de interação: O fluconazol intensifica a ação anticoagulante da varfarina, gerando alto risco de sangramento.',
        case3_clinician_out: 'Mecanismo: Inibição do CYP2C9 pelo fluconazol. Ação: Reduzir dose da varfarina em 50% e monitorar INR com rigor.',

        mission_badge: 'Integridade Clínica',
        mission_title: 'Nossa Missão e Integridade Clínica',
        mission_desc: 'O Pharmasis nasceu para unir a busca médica na internet ao entendimento clínico real.',
        pillar_1_title: 'Evidência Comprovada',
        pillar_1_desc: 'Verificado em cruzamento com 16.000+ perfis clínicos da WebMD e 67.000+ registros de medicamentos da FDA dos EUA (OpenFDA) — totalizando 83.000+ entradas farmacológicas verificadas.',
        pillar_2_title: 'Triagem Objetiva',
        pillar_2_desc: 'Estratificação de gravidade em tempo real baseada em normas de emergência médica.',
        pillar_3_title: 'Síntese em Dois Formatos',
        pillar_3_desc: 'Gera explicações acessíveis para o paciente e resumos técnicos para o médico.',
        metric_1_label: 'Registros de Medicamentos Verificados',
        metric_2_label: 'Meta de Precisão na Triagem',
        metric_3_label: 'Relatórios Clínicos Emitidos',

        cta_badge: 'Navegação em Saúde Direta',
        cta_title: 'Plataforma de Inteligência Médica',
        cta_desc: 'Consulte mais de 67.000 medicamentos verificados, analise interações e realize triagem instantânea.',
        cta_btn: 'Iniciar Triagem MediCore',

        preloader_loading_text: 'Alterando idioma...'
    },

    // ── 11. RUSSIAN ──
    ru: {
        nav_home: 'Главная',
        nav_interactions: 'Взаимодействия',
        nav_browse: 'Справочник лекарств',
        nav_search_placeholder: 'Поиск лекарств, действующих веществ, групп…',
        nav_search_mobile: 'Поиск лекарств…',
        nav_see_all: 'Все результаты для',
        nav_no_result: 'Лекарства не найдены для',
        
        hero_badge: 'Клинический интеллект нового поколения',
        hero_title_1: 'То, что вы чувствуете,',
        hero_title_2: 'заслуживает профессионального ответа.',
        hero_desc: 'Опишите симптомы простыми словами. MediCore AI от Pharmasis проанализирует жалобы, задаст важные уточняющие вопросы и сформирует структурированное клиническое заключение для пациентов и врачей.',
        hero_placeholder: 'Опишите симптомы подробно... Пример: Сухой кашель и температура 38°C на протяжении 2 дней, головная боль и выраженная слабость.',
        hero_hint: 'Опишите симптомы... Нажмите Enter для запуска скрининга.',
        hero_btn_start: 'Начать AI-скрининг',
        hero_quick_chips: 'Популярные клинические запросы:',

        inquiries_badge: 'Клинические консультации',
        inquiries_title: 'Клинические случаи и триаж в реальном времени',
        inquiries_desc: 'Изучите реальные клинические примеры, обработанные многоуровневым механизмом верификации MediCore.',
        inquiries_card_tab: 'КЛИНИЧЕСКИЙ ПРОТОКОЛ',
        inquiries_reported: 'Заявленные симптомы',
        inquiries_action: 'Запустить AI-оценку',

        med_repo_badge: 'Активный реестр препаратов',
        med_repo_title_1: 'Непрерывный поток',
        med_repo_title_2: 'фармакологического каталога',
        med_repo_desc: 'Наведите курсор на каталог, чтобы приостановить прокрутку и изучить характеристики препаратов.',
        med_repo_autoscroll: 'Автопрокрутка · Наведите для паузы',
        med_repo_browse_btn: 'Открыть весь справочник',
        med_repo_quick_info: 'Кратко',
        med_repo_monograph: 'Монография',
        med_repo_indications: 'Терапевтические показания',
        med_repo_adverse: 'Побочные эффекты и предостережения',
        med_repo_analyze_btn: 'Анализ в MediCheck',
        med_repo_open_monograph: 'Открыть полную монографию',
        med_repo_ai_badge: 'Клиническое резюме от AI',
        med_repo_context_lang: 'Язык данных',
        med_repo_switch_lang_btn: 'Сменить язык описания',
        med_repo_generic: 'Действующее вещество',
        med_repo_standard_formulation: 'Стандартная форма выпуска',

        arch_badge: 'Архитектура и механизмы',
        arch_title: 'Клиническая архитектура MediCore',
        arch_desc: 'Три специализированных уровня, преобразующих субъективные жалобы в проверенные медицинские выводы.',
        tab_medifacts_name: 'MediFacts',
        tab_medifacts_desc: 'Фармакология и взаимодействие',
        tab_medicheck_name: 'MediCheck',
        tab_medicheck_desc: 'Многоэтапный триаж симптомов',
        tab_medicombo_name: 'MediCombo',
        tab_medicombo_desc: 'Совместимость сложной полипрагмазии',

        dossier_query_title: '01 / Пример запроса и первичные данные',
        dossier_pipeline_title: '02 / Процесс обработки данных',
        dossier_output_title: '03 / Предварительный просмотр отчета',

        case1_query: 'Пациент в возрасте 50+ лет, принимающий Лизиноприл от гипертонии, жалуется на сухой кашель в течение 4 недель.',
        case1_patient_out: 'Сухой кашель — известная неаллергическая реакция на ингибиторы АПФ (у 10-20% пациентов) из-за накопления брадикинина. Не инфекция.',
        case1_clinician_out: 'Этиология: Накопление брадикинина на фоне иАПФ. Рекомендация: Отмена Лизиноприла, переход на БРА (напр., Лозартан).',

        case2_query: 'Женщина 42 лет с прогрессирующей болью в правом подреберье после еды в течение 3 дней, тошнотой и субфебрилитетом.',
        case2_patient_out: 'Симптомы указывают на воспаление желчного пузыря (желчная колика или холецистит). Необходим осмотр врача и УЗИ.',
        case2_clinician_out: 'Дифференциальный диагноз: Острый холецистит vs симптоматический холелитиаз. Анализы: ОАК, печеночные пробы, УЗИ ОБП.',

        case3_query: 'Мужчина 70 лет, принимающий Варфарин при фибрилляции предсердий, начал прием высоких доз Флуконазола при кандидозе полости рта.',
        case3_patient_out: 'Критическое лекарственное взаимодействие: Флуконазол резко усиливает действие варфарина, создавая опасность тяжелых кровотечений.',
        case3_clinician_out: 'Механизм: Ингибирование CYP2C9 флуконазолом замедляет метаболизм S-варфарина. Действие: Снизить дозу варфарина на 50% и контролировать МНО.',

        mission_badge: 'Клиническая ответственность',
        mission_title: 'Наша миссия и клинические стандарты',
        mission_desc: 'Pharmasis создан для того, чтобы преодолеть разрыв между противоречивым поиском в интернете и достоверными медицинскими данными.',
        pillar_1_title: 'Доказательная база',
        pillar_1_desc: 'Сверено с 16 000+ клиническими профилями WebMD и 67 000+ записями лекарств FDA OpenFDA — всего 83 000+ проверенных фармакологических записей.',
        pillar_2_title: 'Объективный триаж',
        pillar_2_desc: 'Оценка степени тяжести в реальном времени на основе стандартов неотложной помощи.',
        pillar_3_title: 'Двойной формат вывода',
        pillar_3_desc: 'Доступные пояснения для пациента и профессиональная структурированная сводка для врача.',
        metric_1_label: 'Проверенных записей лекарств',
        metric_2_label: 'Целевая точность триажа',
        metric_3_label: 'Сформировано медицинских отчетов',

        cta_badge: 'Прямая медицинская навигация',
        cta_title: 'Платформа медицинского интеллекта',
        cta_desc: 'Поиск по базе из более чем 67 000 проверенных препаратов, проверка взаимодействий и интеллектуальный триаж.',
        cta_btn: 'Запустить MediCore скрининг',

        preloader_loading_text: 'Переключение языка...'
    }
};

/**
 * AI Multilingual Drug Knowledge Humanizer
 * Translates raw medical monograph definitions into humanized, clinically structured patient summaries
 * across all 11 supported languages.
 */
const PHARMASIS_DRUG_HUMANIZER = {
    // Drug class translations across all 11 supported languages
    classTranslations: {
        'Analgesic / Antipyretic': {
            en: 'Analgesic & Antipyretic', id: 'Analgesik & Antipiretik', ja: '解熱鎮痛薬',
            es: 'Analgésico y Antipirético', zh: '解热镇痛药', ar: 'مسكن ومخفف للحرارة',
            fr: 'Analgésique et antipyrétique', de: 'Analgetikum & Antipyretikum', ko: '해열진통제',
            pt: 'Analgésico e Antipirético', ru: 'Обезболивающее и жаропонижающее'
        },
        'Antibiotic': {
            en: 'Antibacterial / Antibiotic', id: 'Antibiotik / Antibakteri', ja: '抗生物質・抗菌薬',
            es: 'Antibiótico Antibacteriano', zh: '抗生素 / 抗菌药', ar: 'مضاد حيوي ومضاد للبكتيريا',
            fr: 'Antibiotique antibactérien', de: 'Antibiotikum / Antibakteriell', ko: '항생제 / 항균제',
            pt: 'Antibiótico Antibacteriano', ru: 'Антибиотик широкого спектра'
        },
        'Antihypertensive': {
            en: 'Antihypertensive / Blood Pressure', id: 'Antihipertensi / Penurun Tekanan Darah', ja: '降圧薬・血圧降下剤',
            es: 'Antihipertensivo / Presión Arterial', zh: '降压药 / 控制血压', ar: 'خافض لضغط الدم',
            fr: 'Antihypertenseur', de: 'Antihypertensivum / Blutdrucksenker', ko: '혈압강하제 / 항고혈압제',
            pt: 'Anti-hipertensivo', ru: 'Гипотензивный препарат'
        },
        'Antidiabetic': {
            en: 'Antidiabetic / Blood Glucose Control', id: 'Antidiabetes / Pengontrol Gula Darah', ja: '経口血糖降下薬・糖尿病用剤',
            es: 'Antidiabético / Control Glucémico', zh: '降糖药 / 糖尿病用药', ar: 'مضاد لمرض السكري',
            fr: 'Antidiabétique', de: 'Antidiabetikum / Blutzuckersenker', ko: '경구 혈당강하제',
            pt: 'Antidiabético', ru: 'Гипогликемическое средство'
        },
        'Proton Pump Inhibitor': {
            en: 'Proton Pump Inhibitor (Gastric Acid Reducer)', id: 'Inhibitor Pompa Proton (Penurun Asam Lambung)', ja: 'プロトンポンプ阻害薬 (胃酸分泌抑制)',
            es: 'Inhibidor de la Bomba de Protones', zh: '质子泵抑制剂（抑制胃酸）', ar: 'مثبط مضخة البروتون (مخفض لحموضة المعدة)',
            fr: 'Inhibiteur de la pompe à protons', de: 'Protonenpumpenhemmer (Magensäureblocker)', ko: '프로톤 펌프 억제제 (위산분비 억제)',
            pt: 'Inibidor da Bomba de Prótons', ru: 'Ингибитор протонной помпы'
        },
        'HMG-CoA Reductase Inhibitor': {
            en: 'Statin / Lipid-Lowering Agent', id: 'Statin / Penurun Kolesterol & Lipid', ja: 'スタチン系・脂質異常症治療薬',
            es: 'Estatina / Hipolipemiante', zh: '他汀类降脂药', ar: 'ستاتين / مخفض للكوليسترول والدهون',
            fr: 'Statine / Hypolipémiant', de: 'Statin / Lipidsenker', ko: '스타틴계 고지혈증 치료제',
            pt: 'Estatina / Hipolipemiante', ru: 'Статин / Гиполипидемическое средство'
        },
        'Antihistamine': {
            en: 'Antihistamine / Allergy Relief', id: 'Antihistamin / Pereda Alergi', ja: '抗ヒスタミン薬・アレルギー用薬',
            es: 'Antihistamínico / Alivio de Alergias', zh: '抗组胺药 / 抗过敏', ar: 'مضاد للهستامين ومخفف للحساسية',
            fr: 'Antihistaminique / Antiallergique', de: 'Antihistaminikum / Allergiemittel', ko: '항히스타민제 / 알레르기 완화',
            pt: 'Anti-histamínico / Alívio de Alergia', ru: 'Антигистаминный препарат'
        },
        'NSAID': {
            en: 'Nonsteroidal Anti-inflammatory (NSAID)', id: 'Anti-inflamasi Non-Steroid (OAINS)', ja: '非ステロイド性抗炎症薬 (NSAIDs)',
            es: 'Antiinflamatorio No Esteroideo (AINE)', zh: '非甾体抗炎药 (NSAID)', ar: 'مضاد التهاب غير ستيرويدي',
            fr: 'Anti-inflammatoire non stéroïdien (AINS)', de: 'Nichtsteroidales Antirheumatikum (NSAR)', ko: '비스테로이드성 소염진통제 (NSAIDs)',
            pt: 'Anti-inflamatório Não Esteróide (AINE)', ru: 'Нестероидный противовоспалительный препарат (НПВП)'
        },
        'Bronchodilator': {
            en: 'Bronchodilator / Respiratory Agent', id: 'Bronkodilator / Pelega Saluran Napas', ja: '気管支拡張薬・呼吸器用剤',
            es: 'Broncodilatador / Vía Respiratoria', zh: '支气管扩张剂 / 呼吸道用药', ar: 'موسع للشعب الهوائية',
            fr: 'Bronchodilatateur respiratoire', de: 'Bronchodilatator / Atemwegstherapeutikum', ko: '기관지 확장제 / 호흡기 치료제',
            pt: 'Broncodilatador Respiratório', ru: 'Бронходилататор / Препарат для дыхательных путей'
        },
        'Corticosteroid': {
            en: 'Corticosteroid / Anti-inflammatory', id: 'Kortikosteroid / Anti-inflamasi Kuat', ja: '副腎皮質ステロイド薬',
            es: 'Corticosteroide / Antiinflamatorio', zh: '糖皮质激素 / 强效抗炎', ar: 'كورتيكوستيرويد / مضاد للالتهاب',
            fr: 'Corticostéroïde anti-inflammatoire', de: 'Kortikosteroid / Entzündungshemmer', ko: '코르티코스테로이드 / 항염증제',
            pt: 'Corticosteroide / Anti-inflamatório', ru: 'Кортикостероид / Противовоспалительное'
        },
        'Diuretic': {
            en: 'Diuretic / Fluid Reduction', id: 'Diuretik / Pelancar Buang Air Kecil', ja: '利尿薬・降圧利尿剤',
            es: 'Diurético / Reducción de Líquidos', zh: '利尿剂 / 排钠排水', ar: 'مدر للبول / خافض للسوائل',
            fr: 'Diurétique', de: 'Diuretikum / Entwässerungsmittel', ko: '이뇨제 / 체액 저류 완화',
            pt: 'Diurético', ru: 'Диуретик / Мочегонное средство'
        },
        'Beta Blocker': {
            en: 'Beta-Adrenergic Blocker', id: 'Penyekat Beta (Beta Blocker)', ja: 'β遮断薬 (ベータブロッカー)',
            es: 'Betabloqueante', zh: 'β受体阻滞剂', ar: 'حاصرات بيتا',
            fr: 'Bêta-bloquant', de: 'Betablocker', ko: '베타 차단제',
            pt: 'Betabloqueador', ru: 'Бета-адреноблокатор'
        },
        'Calcium Channel Blocker': {
            en: 'Calcium Channel Blocker (CCB)', id: 'Penyekat Saluran Kalsium (CCB)', ja: 'カルシウム拮抗薬 (CCB)',
            es: 'Bloqueador de Canales de Calcio', zh: '钙通道阻滞剂 (CCB)', ar: 'حاصرات قنوات الكالسيوم',
            fr: 'Inhibiteur calcique', de: 'Calciumkanalblocker', ko: '칼슘 채널 차단제',
            pt: 'Bloqueador dos Canais de Cálcio', ru: 'Блокатор кальциевых каналов'
        },
        'ACE Inhibitor': {
            en: 'ACE Inhibitor (Renal / Cardiac)', id: 'Inhibitor ACE (Kardiovaskular & Ginjal)', ja: 'ACE阻害薬',
            es: 'Inhibidor de la ECA', zh: 'ACE抑制剂 / 血管紧张素转化酶抑制剂', ar: 'مثبط الإنزيم المحول للأنجيوتنسين',
            fr: 'Inhibiteur de l’ECA', de: 'ACE-Hemmer', ko: 'ACE 억제제',
            pt: 'Inibidor da ECA', ru: 'Ингибитор АПФ'
        },
        'ARB': {
            en: 'Angiotensin Receptor Blocker (ARB)', id: 'Antagonis Reseptor Angiotensin (ARB)', ja: 'ARB (アンジオテンシンII受容体拮抗薬)',
            es: 'Antagonista de Receptores de Angiotensina', zh: 'ARB (血管紧张素II受体拮抗剂)', ar: 'حاصرات مستقبلات الأنجيوتنسين',
            fr: 'Antagoniste des récepteurs de l’angiotensine', de: 'Angiotensin-Rezeptorblocker (AT1-Antagonist)', ko: '안지오텐신 수용체 차단제 (ARB)',
            pt: 'Bloqueador dos Receptores de Angiotensina', ru: 'Блокатор рецепторов ангиотензина II'
        }
    },

    // Comprehensive clinical monographs for major drug compounds with extensive aliases
    clinicalProfiles: {
        'paracetamol': {
            aliases: ['paracetamol', 'acetaminophen', 'panadol', 'tylenol', 'sanmol', 'biogesic', 'paramex', 'tempra', 'dolo', 'calpol'],
            generic: { en: 'Acetaminophen / Paracetamol', id: 'Parasetamol', ja: 'アセトアミノフェン', es: 'Paracetamol', zh: '对乙酰氨基酚', ar: 'باراسيتامول', fr: 'Paracétamol', de: 'Paracetamol', ko: '아세트아미노펜', pt: 'Paracetamol', ru: 'Парацетамол' },
            classKey: 'Analgesic / Antipyretic',
            uses: {
                en: 'Clinically indicated for the rapid symptomatic relief of mild-to-moderate pain (headaches, muscular aches, toothaches, dysmenorrhea) and effective reduction of elevated body temperature during febrile episodes.',
                id: 'Digunakan secara klinis untuk meredakan nyeri ringan hingga sedang (sakit kepala, nyeri otot, sakit gigi, nyeri haid) serta menurunkan demam secara cepat dan aman.',
                ja: '頭痛、筋肉痛、歯痛、月経痛などの軽度から中等度の痛みを迅速に緩和し、発熱時の体温を効果的に下げるために臨床使用されます。',
                es: 'Indicado clínicamente para el alivio rápido del dolor leve a moderado (cefaleas, dolores musculares, odontalgias) y la reducción eficaz de la fiebre.',
                zh: '临床用于快速缓解轻至中度疼痛（头痛、肌肉酸痛、牙痛、痛经），并在发热时有效降低体温。',
                ar: 'يُستعمل سريرياً لتخفيف الآلام الخفيفة إلى المتوسطة (الصداع، آلام العضلات، آلام الأسنان) وخفض درجات الحرارة المرتفعة أثناء الحمى.',
                fr: 'Indiqué cliniquement pour soulager rapidement les douleurs légères à modérées (maux de tête, courbatures, douleurs dentaires) et réduire efficacement la fièvre.',
                de: 'Klinisch indiziert zur raschen Linderung leichter bis mäßiger Schmerzen (Kopf-, Muskel- und Zahnschmerzen) sowie zur wirksamen Fiebersenkung.',
                ko: '두통, 근육통, 치통 등 경도 및 중등도의 통증을 신속히 완화하고 발열 시 체온을 효과적으로 낮추는 데 사용됩니다.',
                pt: 'Indicado clinicamente para o alívio rápido de dores leves a moderadas (cefaléias, dores musculares, dor de dente) e redução eficaz da febre.',
                ru: 'Клинически применяется для быстрого купирования болей слабой и умеренной интенсивности (головная, мышечная, зубная боль) и снижения температуры при лихорадке.'
            },
            sides: {
                en: 'Generally well-tolerated. High doses or concurrent alcohol intake can lead to hepatotoxicity (liver injury). Never exceed maximum daily limit of 4000mg.',
                id: 'Umumnya ditoleransi dengan baik. Dosis berlebih atau konsumsi alkohol bersamaan berisiko menimbulkan toksisitas hati. Jangan melebihi dosis harian 4000 mg.',
                ja: '通常は良好な耐容性を示します。過量投与やアルコールとの併用は肝機能障害のリスクを高めます。1日最大4000mgを超えないようにしてください。',
                es: 'Generalmente bien tolerado. Dosis excesivas o consumo de alcohol aumentan el riesgo de toxicidad hepática. No exceder 4000 mg al día.',
                zh: '通常耐受性良好。超剂量服用或饮酒可诱发肝毒性。每日总剂量严禁超过4000毫克。',
                ar: 'يتحمله الجسم جيداً بشكل عام. قد يؤدي الإفراط في الجرعات أو تناول الكحول إلى تلف الكبد. لا تتجاوز 4000 مجم يومياً.',
                fr: 'Généralement très bien toléré. Des doses excessives ou l’association avec de l’alcool peuvent causer une toxicité hépatique. Ne pas dépasser 4000 mg/jour.',
                de: 'Im Allgemeinen gut verträglich. Überdosierung oder gleichzeitiger Alkoholkonsum kann Leberschäden verursachen. Maximale Tagesdosis von 4000 mg nicht überschreiten.',
                ko: '일반적으로 안전하게 사용됩니다. 과다 복용 또는 음주 시 간 손상 위험이 있습니다. 하루 최대 4000mg을 초과하지 마십시오.',
                pt: 'Geralmente bem tolerado. Superdosagem ou consumo de álcool pode causar lesão hepática. Não ultrapasse 4000 mg por dia.',
                ru: 'Обычно переносится хорошо. Передозировка или сочетание с алкоголем токсичны для печени. Не превышайте максимальную суточную дозу 4000 мг.'
            }
        },
        'ibuprofen': {
            aliases: ['ibuprofen', 'advil', 'motrin', 'proris', 'nurofen', 'brufen', 'frenadol'],
            generic: { en: 'Ibuprofen', id: 'Ibuprofen', ja: 'イブプロフェン', es: 'Ibuprofeno', zh: '布洛芬', ar: 'إيبوبروفين', fr: 'Ibuprofène', de: 'Ibuprofen', ko: '이부프로펜', pt: 'Ibuprofeno', ru: 'Ибупрофен' },
            classKey: 'NSAID',
            uses: {
                en: 'Potent non-steroidal anti-inflammatory agent indicated for acute inflammatory pain, osteoarthritis flare-ups, migraine, dental pain, and fever reduction.',
                id: 'Obat anti-inflamasi non-steroid (OAINS) untuk mengatasi nyeri peradangan akut, sakit kepala migrain, sakit gigi, radang sendi, dan demam.',
                ja: '急性炎症に伴う疼痛、変形性関節症、片頭痛、歯痛の鎮痛および解熱に広く用いられる非ステロイド性抗炎症薬（NSAIDs）です。',
                es: 'Antiinflamatorio no esteroideo indicado para dolor inflamatorio agudo, artritis, migrañas, dolor dental y reducción de fiebre.',
                zh: '高效非甾体抗炎药，用于缓解急性炎性疼痛、骨关节炎、偏头痛、牙痛及发热。',
                ar: 'مضاد التهاب غير ستيرويدي فعال لتسكين آلام الالتهابات الحادة، التهاب المفاصل، الصداع النصفي، آلام الأسنان وخفض الحرارة.',
                fr: 'Anti-inflammatoire non stéroïdien (AINS) indiqué dans les douleurs inflammatoires aiguës, l’arthrose, la migraine et la fièvre.',
                de: 'Nichtsteroidales Antirheumatikum (NSAR) zur Behandlung von entzündlichen Schmerzen, Arthrose, Migräne, Zahnschmerzen und Fieber.',
                ko: '급성 염증성 통증, 관절염, 편두통, 치통 완화 및 해열에 널리 사용되는 비스테로이드성 소염진통제입니다.',
                pt: 'Anti-inflamatório não esteróide para dores inflamatórias agudas, artrite, enxaqueca, dor de dente e febre.',
                ru: 'Нестероидный противовоспалительный препарат для снятия воспалительной боли, артрита, мигрени, зубной боли и снижения жара.'
            },
            sides: {
                en: 'Gastrointestinal irritation, heartburn, and peptic ulcer risk. Always administer with meals. Use caution in chronic kidney disease and hypertension.',
                id: 'Iritasi lambung, mulas (heartburn), dan risiko tukak peptik. Konsumsi sesudah makan. Hindari pada penderita gagal ginjal dan hipertensi tidak terkontrol.',
                ja: '胃腸障害、胸やけ、胃潰瘍のリスク。必ず食後に服用してください。慢性腎臓病や高血圧の方は医師にご相談ください。',
                es: 'Irritación gastrointestinal y riesgo de úlceras. Tomar siempre con alimentos. Precaución en insuficiencia renal e hipertensión.',
                zh: '可能引起胃肠刺激、胃灼热及溃疡风险。务必随餐或餐后服用。肾功能不全及高血压患者慎用。',
                ar: 'تهيج المعدة، وحرقة الفؤاد، وخطر القرحة الهضمية. يجب تناوله دائماً بعد الوجبات. الحذر لدى مرضى الكلى وارتفاع ضغط الدم.',
                fr: 'Irritation gastrique, brûlures et risque d’ulcère. À prendre impérativement pendant les repas. Prudence en cas d’insuffisance rénale.',
                de: 'Magen-Darm-Reizungen, Sodbrennen und Ulkusrisiko. Immer zu den Mahlzeiten einnehmen. Vorsicht bei Niereninsuffizienz.',
                ko: '위장관 자극, 속쓰림, 위궤양 위험이 있습니다. 반드시 식후 복용하며, 신장 질환 및 고혈압 환자는 주의가 필요합니다.',
                pt: 'Irritação estomacal, azia e risco de úlceras. Tome sempre após as refeições. Cuidado em pacientes com problemas renais.',
                ru: 'Раздражение ЖКТ, изжога, риск эрозий и язв. Принимать строго после еды. Соблюдать осторожность при заболеваниях почек.'
            }
        },
        'aspirin': {
            aliases: ['aspirin', 'acetylsalicylic acid', 'aspilet', 'cardiprin', 'aspro', 'thrombo aspilet', 'ecotrin'],
            generic: { en: 'Acetylsalicylic Acid (Aspirin)', id: 'Asam Asetilsalisilat (Aspirin)', ja: 'アスピリン (アセチルサリチル酸)', es: 'Ácido Acetilsalicílico', zh: '阿司匹林 / 乙酰水杨酸', ar: 'حمض أسيتيل الساليسيليك (أسبرين)', fr: 'Acide acétylsalicylique', de: 'Acetylsalicylsäure (Aspirin)', ko: '아스피린 (아세틸살리실산)', pt: 'Ácido Acetilsalicílico', ru: 'Ацетилсалициловая кислота (Аспирин)' },
            classKey: 'NSAID',
            uses: {
                en: 'Antiplatelet therapy for secondary prevention of myocardial infarction and ischemic stroke, alongside analgesic/antipyretic relief in higher doses.',
                id: 'Antiplatelet/pengencer darah untuk pencegahan serangan jantung dan stroke iskemik, serta pereda nyeri dan penurun demam pada dosis lebih tinggi.',
                ja: '心筋梗塞や虚血性脳卒中の再発予防（抗血小板療法）および高用量での鎮痛・解熱に使用されます。',
                es: 'Terapia antiplaquetaria para la prevención secundaria de infarto de miocardio e ictus isquémico, además de analgésico.',
                zh: '抗血小板聚集药物，用于心肌梗死及缺血性脑卒中的二级预防；高剂量下亦具解热镇痛功效。',
                ar: 'مضاد لتجمع الصفائح الدموية للوقاية الثانوية من النوبات القلبية والسكتات الدماغية، ومسكن ومخفف للحرارة بجرعات أعلى.',
                fr: 'Antiagrégant plaquettaire en prévention des récidives d’infarctus et d’AVC ischémique, et antalgique à plus forte dose.',
                de: 'Thrombozytenaggregationshemmer zur Sekundärprävention von Herzinfarkt und Schlaganfall sowie Schmerz- und Fiebermittel.',
                ko: '심근경색 및 허혈성 뇌졸중의 2차 예방을 위한 항혈소판 요법 및 고용량에서의 해열·진통 목적으로 사용됩니다.',
                pt: 'Antiplaquetário para prevenção de infarto e AVC isquêmico, além de analgésico e antipirético em doses elevadas.',
                ru: 'Антиагрегантное средство для профилактики инфаркта миокарда и ишемического инсульта, а также анальгетик в повышенных дозах.'
            },
            sides: {
                en: 'Increased bleeding tendency, gastric mucosa erosion, and tinnitus. Contraindicated in children and adolescents with viral infections (Reye syndrome risk).',
                id: 'Risiko perdarahan meningkat, iritasi mukosa lambung, dan telinga berdenging. Kontraindikasi pada anak dan remaja dengan infeksi virus (risiko Sindrom Reye).',
                ja: '出血傾向の増大、胃粘膜障害、耳鳴り。ウイルス性感染症の小児・青年の服用はライ症候群のリスクがあるため禁忌です。',
                es: 'Mayor riesgo de hemorragia, erosión gástrica y acúfenos. Contraindicado en niños con infecciones virales (Síndrome de Reye).',
                zh: '出血倾向增加、胃黏膜损伤及耳鸣。患有病毒性感染的儿童及青少年禁用（有诱发瑞氏综合征风险）。',
                ar: 'زيادة القابلية للنزيف، تآكل بطانة المعدة، وطنين الأذن. يمنع للأطفال والمراهقين المصابين بعدوى فيروسية (خطر متلازمة راي).',
                fr: 'Risque accru de saignement, lésions gastriques, acouphènes. Contre-indiqué chez l’enfant avec infection virale (Syndrome de Reye).',
                de: 'Erhöhte Blutungsneigung, Magenschleimhautschäden, Tinnitus. Kontraindiziert bei Kindern mit Virusinfekten (Reye-Syndrom).',
                ko: '출혈 경향 증가, 위점막 손상, 이명. 바이러스 감염 소아 및 청소년에게는 라이 증후군 위험으로 복용 금기입니다.',
                pt: 'Maior risco de sangramento, irritação gástrica e zumbido. Contraindicado em crianças com infecções virais (Síndrome de Reye).',
                ru: 'Повышенная кровоточивость, эрозии слизистой желудка, шум в ушах. Противопоказан детям при вирусных инфекциях (риск синдрома Рея).'
            }
        },
        'amoxicillin': {
            aliases: ['amoxicillin', 'amoxil', 'amoxsan', 'yusimox', 'augmentin', 'clavamox', 'clamoxyl'],
            generic: { en: 'Amoxicillin Trihydrate', id: 'Amoksisilin Trihidrat', ja: 'アモキシシリン水和物', es: 'Amoxicilina', zh: '阿莫西林', ar: 'أموكسيسيلين', fr: 'Amoxicilline', de: 'Amoxicillin', ko: '아목시실린', pt: 'Amoxicilina', ru: 'Амоксициллин' },
            classKey: 'Antibiotic',
            uses: {
                en: 'Broad-spectrum aminopenicillin antibiotic prescribed for bacterial ear, nose, throat, respiratory tract, urinary, and skin infections. Inhibits bacterial cell wall synthesis.',
                id: 'Antibiotik aminopenisilin spektrum luas untuk mengatasi infeksi bakteri pada saluran pernapasan, telinga, hidung, tenggorokan, saluran kemih, dan kulit.',
                ja: '呼吸器感染症、中耳炎、咽頭炎、尿路感染症、皮膚感染症などの細菌感染症の治療に処方される広域アミノペニシリン系抗生物質です。',
                es: 'Antibiótico de amplio espectro prescrito para tratar infecciones bacterianas en vías respiratorias, oído, garganta, vías urinarias y piel.',
                zh: '广谱青霉素类抗生素，用于治疗呼吸道、耳鼻喉、泌尿道和皮肤的敏感细菌感染。',
                ar: 'مضاد حيوي واسع المجال يصف لعلاج الالتهابات البكتيرية في الجهاز التنفسي، الأذن، الحلق، المسالك البولية والجلد.',
                fr: 'Antibiotique aminopénicilline à large spectre prescrit dans les infections bactériennes ORL, respiratoires, urinaires et cutanées.',
                de: 'Breitspektrum-Antibiotikum zur Behandlung bakterieller Infektionen der Atemwege, des HNO-Bereichs, der Harnwege und der Haut.',
                ko: '호흡기 감염, 이비인후과 감염, 요로 및 피부 세균 감염 치료에 처방되는 광범위 페니실린계 항생제입니다.',
                pt: 'Antibiótico de amplo espectro prescrito no tratamento de infecções bacterianas respiratórias, otorrinolaringológicas, urinárias e cutâneas.',
                ru: 'Антибиотик широкого спектра действия группы полусинтетических пенициллинов для лечения бактериальных инфекций дыхательных и мочевыводящих путей, ЛОР-органов и кожи.'
            },
            sides: {
                en: 'Common side effects include gastrointestinal upset, diarrhea, and mild nausea. Contraindicated in individuals with true penicillin allergies.',
                id: 'Efek samping umum mencakup diare, mual, dan kram perut ringan. Kontraindikasi mutlak pada individu dengan riwayat alergi penisilin.',
                ja: '主な副作用は下痢、悪心、軟便です。ペニシリン系薬剤にアレルギーのある方は服用できません。',
                es: 'Efectos comunes incluyen diarrea, náuseas y malestar estomacal. Contraindicado en pacientes con alergia comprobada a penicilinas.',
                zh: '常见不良反应包括轻度腹泻、恶心及消化不良。青霉素过敏者绝对禁用。',
                ar: 'تشمل الآثار الجانبية الشائعة الإسهال والغثيان واضطراب المعدة. يمنع استخدامه لمن يعانون من حساسية البنسلين.',
                fr: 'Effets secondaires fréquents : troubles digestifs, diarrhée, nausées. Contre-indiqué en cas d’allergie avérée aux pénicillines.',
                de: 'Häufige Nebenwirkungen sind Durchfall, Übelkeit und Magenbeschwerden. Kontraindiziert bei Penicillinallergie.',
                ko: '흔한 부작용으로 설사, 메스꺼움, 소화불량이 있습니다. 페니실린 알레르기 환자에게는 절대 금기입니다.',
                pt: 'Efeitos comuns incluem diarreia, náuseas e desconforto abdominal. Contraindicado para quem possui alergia a penicilinas.',
                ru: 'Частые побочные эффекты: диарея, тошнота, дискомфорт в ЖКТ. Абсолютно противопоказан при аллергии на пенициллины.'
            }
        },
        'metformin': {
            aliases: ['metformin', 'glucophage', 'glumin', 'nevox', 'diabex', 'formet', 'glucomet'],
            generic: { en: 'Metformin Hydrochloride', id: 'Metformin Hidroklorida', ja: 'メトホルミン塩酸塩', es: 'Metformina', zh: '盐酸二甲双胍', ar: 'ميتفورمين هيدروكلوريد', fr: 'Metformine', de: 'Metformin-Hydrochlorid', ko: '메트포르민 염산염', pt: 'Metformina', ru: 'Метформин гидрохлорид' },
            classKey: 'Antidiabetic',
            uses: {
                en: 'First-line oral antidiabetic biguanide for Type 2 Diabetes Mellitus. Decreases hepatic glucose production, lowers intestinal absorption, and improves insulin sensitivity.',
                id: 'Terapi lini pertama untuk Diabetes Melitus Tipe 2. Bekerja menurunkan produksi glukosa oleh hati, mengurangi penyerapan gula di usus, dan meningkatkan sensitivitas insulin.',
                ja: '2型糖尿病の第一選択薬（ビグアナイド系）。肝臓での糖新生を抑制し、末梢組織でのインスリン感受性を高めて血糖値を改善します。',
                es: 'Tratamiento de primera línea para la Diabetes Mellitus Tipo 2. Reduce la producción hepática de glucosa y mejora la sensibilidad a la insulina.',
                zh: '2型糖尿病首选口服降糖药物（双胍类）。减少肝脏葡萄糖生成并提高外周组织对胰岛素的敏感性。',
                ar: 'العلاج الأولي لمرض السكري من النوع الثاني. يقلل إنتاج الجلوكوز في الكبد ويحسن حساسية خلايا الجسم للأنسولين.',
                fr: 'Traitement de première intention du diabète de type 2. Réduit la production hépatique de glucose et améliore la sensibilité à l’insuline.',
                de: 'Erstlinientherapie bei Typ-2-Diabetes. Hemmt die Glukoseproduktion in der Leber und verbessert die Insulinempfindlichkeit.',
                ko: '제2형 당뇨병의 1차 표준 치료제. 간의 포도당 생성을 억제하고 인슐린 민감성을 향상시킵니다.',
                pt: 'Medicamento de primeira escolha para Diabetes Tipo 2. Reduz a produção hepática de glicose e melhora a sensibilidade à insulina.',
                ru: 'Препарат первого ряда при сахарном диабете 2 типа (бигуанид). Снижает выработку глюкозы печенью и улучшает чувствительность тканей к инсулину.'
            },
            sides: {
                en: 'Gastrointestinal disturbances (diarrhea, abdominal cramps, metallic taste). Rare but severe risk: Lactic Acidosis. Monitor eGFR renal function regularly.',
                id: 'Gangguan saluran cerna (diare, kembung, rasa logam). Risiko langka namun serius: Asidosis Laktat. Pantau fungsi ginjal (eGFR) secara berkala.',
                ja: '下痢、腹部膨満感、金属味などの消化器症状。稀ですが重大なリスクとして乳酸アシドーシスがあるため腎機能の定期的な確認が必要です。',
                es: 'Molestias digestivas (diarrea, flatulencia, sabor metálico). Riesgo raro pero grave: acidosis láctica. Monitorear función renal.',
                zh: '常见胃肠道不适（腹泻、腹胀、金属异味感）。罕见但严重的风险为乳酸酸中毒。须定期监测肾功能指标。',
                ar: 'اضطرابات الجهاز الهضمي (إسهال، تقلصات بطنية، طعم معدني). خطر نادر ولكنه خطير: الحماض اللبني. ينصح بمراقبة وظائف الكلى.',
                fr: 'Troubles digestifs fréquents (diarrhée, nausées, goût métallique). Risque rare mais grave : acidose lactique. Surveillance rénale requise.',
                de: 'Magen-Darm-Beschwerden (Durchfall, Blähungen, metallischer Geschmack). Seltenes, schweres Risiko: Laktatazidose. Nierenwerte regelmäßig überwachen.',
                ko: '복통, 설사, 금속성 미각 등의 소화기계 불편감. 드물지만 중대한 젖산산증 위험이 있어 정기적인 신장 기능 검사가 필요합니다.',
                pt: 'Desconforto gastrointestinal (diarreia, gases, gosto metálico). Risco raro mas grave: Acidose Lática. Monitorar função renal.',
                ru: 'Диспепсические расстройства (диарея, тошнота, металлический привкус во рту). Редкий, но опасный риск: лактатацидоз. Требуется контроль функции почек.'
            }
        },
        'omeprazole': {
            aliases: ['omeprazole', 'prilosec', 'losec', 'omevell', 'ozid', 'gastrofer', 'lokit', 'omz'],
            generic: { en: 'Omeprazole Magnesium', id: 'Omeprazol', ja: 'オメプラゾール', es: 'Omeprazol', zh: '奥美拉唑', ar: 'أوميبرازول', fr: 'Oméprazole', de: 'Omeprazol', ko: '오메프라졸', pt: 'Omeprazol', ru: 'Омепразол' },
            classKey: 'Proton Pump Inhibitor',
            uses: {
                en: 'Potent proton pump inhibitor for gastroesophageal reflux disease (GERD), erosive esophagitis, peptic ulcers, and eradication of Helicobacter pylori.',
                id: 'Pereda produksi asam lambung kuat untuk mengatasi penyakit refluks asam lambung (GERD), tukak lambung/usus dua belas jari, dan radang esofagus.',
                ja: '胃食道逆流症（GERD）、胃潰瘍、十二指腸潰瘍、ピロリ菌除菌療法において胃酸分泌を強力に抑制するプロトンポンプ阻害薬です。',
                es: 'Potente inhibidor de la bomba de protones para el reflujo gastroesofágico (ERGE), úlceras pépticas y erradicación de Helicobacter pylori.',
                zh: '高效质子泵抑制剂，用于治疗胃食管反流病（GERD）、胃及十二指肠溃疡，并配合根除幽门螺杆菌。',
                ar: 'مثبط قوي لمضخة البروتون لعلاج ارتجاع المريء (GERD)، قرحة المعدة والاثني عشر، والقضاء على بكتيريا الملوية البوابية.',
                fr: 'Inhibiteur puissant de la pompe à protons prescrit dans le reflux gastro-œsophagien (RGO), les ulcères gastriques et l’éradication d’H. pylori.',
                de: 'Wirksamer Säureblocker zur Behandlung von gastroösophagealer Refluxkrankheit (GERD), Magengeschwüren und Eradikation von H. pylori.',
                ko: '위식도역류질환(GERD), 위·십이지장 궤양 치료 및 헬리코박터 파일로리 제균을 위한 강력한 위산분비 억제제입니다.',
                pt: 'Inibidor potente da acidez gástrica para refluxo gastroesofágico (DRGE), úlceras pépticas e erradicação de H. pylori.',
                ru: 'Эффективный блокатор желудочной секреции для лечения гастроэзофагеального рефлюкса (ГЭРБ), язвенной болезни желудка и эрадикации H. pylori.'
            },
            sides: {
                en: 'Headache, mild constipation or diarrhea. Long-term continuous use may reduce absorption of vitamin B12, calcium, and magnesium.',
                id: 'Sakit kepala, sembelit ringan, atau diare. Penggunaan jangka panjang dapat menurunkan penyerapan vitamin B12, kalsium, dan magnesium.',
                ja: '頭痛、便秘、軟便など。長期連用によりビタミンB12、カルシウム、マグネシウムの吸収低下が起こる可能性があります。',
                es: 'Cefalea, estreñimiento o diarrea leve. El uso a largo plazo puede reducir la absorción de vitamina B12, calcio y magnesio.',
                zh: '头痛、轻微便秘或腹泻。长期持续使用可能影响维生素B12、钙及镁的吸收。',
                ar: 'صداع، إمساك أو إسهال خفيف. الاستخدام طويل الأمد قد يقلل امتصاص فيتامين B12 والكالسيوم والمغنيسيوم.',
                fr: 'Céphalées, constipation ou diarrhée passagère. Un usage prolongé peut diminuer l’absorption de vitamine B12, calcium et magnésium.',
                de: 'Kopfschmerzen, Verstopfung oder Durchfall. Eine Langzeiteinnahme kann die Aufnahme von Vitamin B12, Calcium und Magnesium verringern.',
                ko: '두통, 변비 또는 경미한 설사. 장기 복용 시 비타민 B12, 칼슘, 마그네슘의 흡수가 저하될 수 있습니다.',
                pt: 'Dor de cabeça, constipação ou diarreia leve. O uso prolongado pode diminuir a absorção de vitamina B12, cálcio e magnésio.',
                ru: 'Головная боль, диарея или запор. Длительный бесконтрольный прием может снижать усвоение витамина B12, кальция и магния.'
            }
        },
        'atorvastatin': {
            aliases: ['atorvastatin', 'lipitor', 'atorsan', 'truvada', 'torvast', 'stator', 'lipivas'],
            generic: { en: 'Atorvastatin Calcium', id: 'Atorvastatin Kalsium', ja: 'アトルバスタチンカルシウム', es: 'Atorvastatina', zh: '阿托伐他汀钙', ar: 'أتورفاستاتين', fr: 'Atorvastatine', de: 'Atorvastatin', ko: '아토르바스타틴 칼슘', pt: 'Atorvastatina', ru: 'Аторвастатин' },
            classKey: 'HMG-CoA Reductase Inhibitor',
            uses: {
                en: 'High-intensity HMG-CoA reductase inhibitor for primary hypercholesterolemia, mixed dyslipidemia, and prevention of cardiovascular events (myocardial infarction, stroke).',
                id: 'Penurun kolesterol intensitas tinggi untuk mengendalikan kadar LDL/kolesterol jahat dan trigliserida, serta mencegah risiko serangan jantung dan stroke.',
                ja: '高コレステロール血症や脂質異常症を改善し、動脈硬化、心筋梗塞、脳卒中などの心血管イベントを予防する高強度スタチン系薬剤です。',
                es: 'Estatina de alta intensidad para hipercolesterolemia y prevención de eventos cardiovasculares como infartos y accidentes cerebrovasculares.',
                zh: '高强度他汀类降脂药，用于治疗原发性高胆固醇血症，显著降低低密度脂蛋白（LDL），预防心脑血管意外。',
                ar: 'ستاتين عالي الفعالية لخفض الكوليسترول الضار (LDL) والدهون الثلاثية، والوقاية من النوبات القلبية والسكتات الدماغية.',
                fr: 'Statine de haute intensité indiquée dans l’hypercholestérolémie et la prévention des événements cardiovasculaires majeurs (infarctus, AVC).',
                de: 'Statin zur Senkung von LDL-Cholesterin und Triglyceriden sowie zur primären und sekundären Prävention kardiovaskulärer Ereignisse.',
                ko: '혈중 나쁜 콜레스테롤(LDL)과 중성지방을 낮추고 심근경색 및 뇌졸중 발생 위험을 예방하는 스타틴계 고지혈증 치료제입니다.',
                pt: 'Estatina potente para redução do colesterol LDL e triglicerídeos, prevenindo infartos e acidentes vasculares cerebrais.',
                ru: 'Гиполипидемический препарат группы статинов для снижения уровня холестерина ЛПНП и триглицеридов, профилактики инфаркта и инсульта.'
            },
            sides: {
                en: 'Myalgia (muscle ache/weakness), mild digestive upset, and elevated transaminases. Promptly report unexplained severe muscle soreness or dark-colored urine.',
                id: 'Nyeri atau kram otot (mialgia), gangguan pencernaan ringan, dan kenaikan enzim hati. Laporkan segera jika mengalami nyeri otot hebat atau urin berwarna gelap.',
                ja: '筋肉痛（ミオパチー）、消化器の不快感、肝機能値の変動。原因不明の筋肉の痛みや赤褐色尿が現れた場合は直ちに医師へご相談ください。',
                es: 'Mialgias (dolores musculares), molestias digestivas y elevación de enzimas hepáticas. Notificar inmediatamente dolor muscular inexplicable.',
                zh: '肌肉酸痛（肌痛症）、消化系统不适及转氨酶升高。如出现不明原因的肌无力或浓茶色尿液，应立即就医。',
                ar: 'آلام عضلية (اعتلال عضلي)، اضطراب هضمي، وارتفاع إنزيمات الكبد. يجب إبلاغ الطبيب فوراً عند الشعور بألم عضلي غير مبرر أو تغير لون البول إلى الداكن.',
                fr: 'Myalgies (douleurs musculaires), troubles digestifs, élévation des enzymes hépatiques. Consulter en cas de douleurs musculaires inexpliquées.',
                de: 'Muskelschmerzen (Myalgie), Magen-Darm-Störungen, Transaminasenanstieg. Bei unerklärlichen Muskelschmerzen oder dunklem Urin sofort Arzt kontaktieren.',
                ko: '근육통, 소화기계 불편감, 간 수치 상승. 원인 불명의 극심한 근육통이나 짙은 색 소변이 나타나면 즉시 의료진과 상담해야 합니다.',
                pt: 'Dores musculares (mialgias), distúrbios digestivos e alteração nas enzimas hepáticas. Relatar imediatamente dor muscular severa.',
                ru: 'Мышечные боли (миалгия), диспепсия, повышение печеночных ферментов. При появлении выраженной мышечной слабости или темной мочи немедленно обратитесь к врачу.'
            }
        },
        'lisinopril': {
            aliases: ['lisinopril', 'zestril', 'prinivil', 'tensopril', 'inopril'],
            generic: { en: 'Lisinopril Dihydrate', id: 'Lisinopril', ja: 'リシノプリル', es: 'Lisinopril', zh: '赖诺普利', ar: 'ليزينوبريل', fr: 'Lisinopril', de: 'Lisinopril', ko: '리시노프릴', pt: 'Lisinopril', ru: 'Лизиноприл' },
            classKey: 'ACE Inhibitor',
            uses: {
                en: 'Angiotensin-Converting Enzyme (ACE) inhibitor for systemic hypertension, adjunctive therapy in congestive heart failure, and renal protection in diabetic nephropathy.',
                id: 'Inhibitor ACE untuk menurunkan tekanan darah tinggi (hipertensi), terapi gagal jantung kongestif, dan melindungi fungsi ginjal pada penderita diabetes.',
                ja: 'ACE阻害薬（アンジオテンシン変換酵素阻害薬）。高血圧症の治療、慢性心不全の補助療法、糖尿病性腎症の進行抑制に使用されます。',
                es: 'Inhibidor de la ECA para la hipertensión arterial, insuficiencia cardíaca y protección renal en nefropatía diabética.',
                zh: '血管紧张素转换酶（ACE）抑制剂，用于高血压、充血性心力衰竭及糖尿病肾病的肾脏保护。',
                ar: 'مثبط للإنزيم المحول للأنجيوتنسين (ACE) لعلاج ارتفاع ضغط الدم وفشل القلب الاحتقاني وحماية الكلى لدى مرضى السكري.',
                fr: 'Inhibiteur de l’ECA indiqué dans le traitement de l’hypertension artérielle, l’insuffisance cardiaque et la néphropathie diabétique.',
                de: 'ACE-Hemmer zur Behandlung von Bluthochdruck, Herzinsuffizienz sowie zum Nierenschutz bei diabetischer Nephropathie.',
                ko: '고혈압 치료, 만성 심부전 보조 요법 및 당뇨병성 신증 환자의 신장 보호를 위한 ACE 억제제입니다.',
                pt: 'Inibidor da ECA para hipertensão arterial, insuficiência cardíaca e proteção renal em nefropatía diabética.',
                ru: 'Ингибитор АПФ для лечения артериальной гипертензии, хронической сердечной недостаточности и диабетической нефропатии.'
            },
            sides: {
                en: 'Persistent non-productive dry cough (due to bradykinin build-up), dizziness/orthostatic hypotension, and hyperkalemia (high potassium).',
                id: 'Batuk kering terus-menerus (akibat akumulasi bradikinin), pusing saat berdiri, dan peningkatan kadar kalium darah (hiperkalemia).',
                ja: '空咳（ブラジキニン蓄積による乾性咳嗽）、起立性低血圧、めまい、高カリウム血症などが起こることがあります。',
                es: 'Tos seca persistente (por bradicinina), mareos posturales e hiperpotasemia (potasio elevado).',
                zh: '持续性刺激性干咳（缓激肽蓄积所致）、直立性头晕及血钾偏高（高钾血症）。',
                ar: 'سعال جاف مستمر (بسبب تراكم البراديكينين)، دوخة وانخفاض ضغط الدم الانتصابي، وارتفاع بوتاسيوم الدم.',
                fr: 'Toux sèche persistante (accumulation de bradykinine), vertiges orthostatiques et hyperkaliémie.',
                de: 'Hartnäckiger Reizhusten (durch Bradykinin-Akkumulation), Schwindelgefühl und Hyperkaliämie (erhöhtes Kalium).',
                ko: '지속적인 마른기침(브라디키닌 축적에 기인), 기립성 어지럼증, 고칼륨혈증 등이 나타날 수 있습니다.',
                pt: 'Tosse seca persistente (por acúmulo de bradicinina), tontura e elevação dos níveis de potássio (hipercalemia).',
                ru: 'Сухой упорный кашель (из-за накопления брадикинина), головокружение при смене позы, гиперкалиемия.'
            }
        },
        'amlodipine': {
            aliases: ['amlodipine', 'norvasc', 'tensivask', 'amlocor', 'divask', 'lodipin'],
            generic: { en: 'Amlodipine Besylate', id: 'Amlodipin Besilat', ja: 'アムロジピンベシル酸塩', es: 'Amlodipino', zh: '苯磺酸氨氯地平', ar: 'أملوديبين', fr: 'Amlodipine', de: 'Amlodipin', ko: '암로디핀 베실산염', pt: 'Anlodipino', ru: 'Амлодипин' },
            classKey: 'Calcium Channel Blocker',
            uses: {
                en: 'Long-acting dihydropyridine calcium channel blocker for systemic essential hypertension and chronic stable angina pectoris (chest pain).',
                id: 'Penyekat saluran kalsium (CCB) kerja panjang untuk menurunkan tekanan darah tinggi (hipertensi) dan mengontrol nyeri dada angina.',
                ja: '長時間作用型ジヒドロピリジン系カルシウム拮抗薬。本態性高血圧症および労作性狭心症の治療に処方されます。',
                es: 'Bloqueador de los canales de calcio de acción prolongada para la hipertensión arterial y la angina de pecho crónica estable.',
                zh: '长效二氢吡啶类钙通道阻滞剂，用于治疗高血压及慢性稳定性心绞痛。',
                ar: 'حاصرات قنوات الكالسيوم طويلة المفعول لعلاج ارتفاع ضغط الدم الأساسي والذبحة الصدرية المزمنة المستقرة.',
                fr: 'Inhibiteur calcique dihydropyridine à longue durée d’action indiqué dans l’hypertension artérielle et l’angor stable.',
                de: 'Langwirksamer Dihydropyridin-Calciumantagonist zur Behandlung von essenzieller Hypertonie und stabiler Angina pectoris.',
                ko: '고혈압 및 만성 안정형 협심증 치료에 널리 사용되는 장기 지속형 칼슘 채널 차단제입니다.',
                pt: 'Bloqueador dos canais de cálcio de longa ação para tratamento de hipertensão arterial e angina estável.',
                ru: 'Блокатор кальциевых каналов длительного действия для лечения артериальной гипертензии и стабильной стенокардии.'
            },
            sides: {
                en: 'Peripheral edema (swelling of ankles/feet), facial flushing, dizziness, and palpitations. Monitor for dependent lower extremity swelling.',
                id: 'Pembengkakan pada pergelangan kaki (edema perifer), wajah memerah (flushing), pusing, dan jantung berdebar. Pantau retensi cairan kaki.',
                ja: '足首・下肢の浮腫（むくみ）、顔面紅潮、めまい、動悸。下肢のむくみに注意してください。',
                es: 'Edema periférico (hinchazón de tobillos), rubor facial, mareos y palpitaciones. Vigilar hinchazón en extremidades.',
                zh: '下肢及踝部水肿、面部潮红、头晕及心悸。用药期间请注意观察腿部浮肿情况。',
                ar: 'وذمة محيطية (تورم الكاحلين والقدمين)، احمرار الوجه، دوخة، وخفقان القلب. راقب تورم الأطراف السفلية.',
                fr: 'Œdème périphérique (gonflement des chevilles), bouffées vasomotrices, vertiges et palpitations.',
                de: 'Periphere Ödeme (Knöchelschwellungen), Gesichtsrötung (Flushing), Schwindel und Herzklopfen.',
                ko: '발목 및 하지 부종, 안면 홍조, 어지럼증, 두근거림이 발생할 수 있습니다.',
                pt: 'Edema periférico (inchaço nos tornozelos), rubor facial, tontura e palpitações.',
                ru: 'Периферические отеки (лодыжек и стоп), приливы крови к лицу, головокружение, учащенное сердцебиение.'
            }
        },
        'cetirizine': {
            aliases: ['cetirizine', 'zyrtec', 'incidal', 'ryvel', 'cerini', 'cetrizet', 'alrigo'],
            generic: { en: 'Cetirizine Hydrochloride', id: 'Setirizin Hidroklorida', ja: 'セチリジン塩酸塩', es: 'Cetirizina', zh: '盐酸西替利嗪', ar: 'سيتريزين هيدروكلوريد', fr: 'Cétirizine', de: 'Cetirizin', ko: '세티리진 염산염', pt: 'Cetirizina', ru: 'Цетиризин гидрохлорид' },
            classKey: 'Antihistamine',
            uses: {
                en: 'Second-generation non-sedating H1-antihistamine indicated for allergic rhinitis (sneezing, runny nose), perennial allergic conjunctivitis, and chronic urticaria (hives).',
                id: 'Antihistamin generasi kedua untuk meredakan gejala rinitis alergi (bersin-bersin, hidung meler/gatal), mata berair, dan gatal biduran (urtikaria).',
                ja: 'アレルギー性鼻炎（くしゃみ、鼻水、鼻づまり）および慢性蕁麻疹、皮膚掻痒症の症状を緩和する第2世代抗ヒスタミン薬です。',
                es: 'Antihistamínico H1 de segunda generación para la rinitis alérgica, conjuntivitis y urticaria crónica (ronchas).',
                zh: '第二代非镇静抗组胺药，用于缓解过敏性鼻炎（打喷嚏、流涕）、过敏性结膜炎及慢性荨麻疹。',
                ar: 'مضاد للهستامين من الجيل الثاني لعلاج التهاب الأنف التحسسي، حكة العينين، والشرى المزمن (حساسية الجلد).',
                fr: 'Antihistaminique H1 de seconde génération indiqué dans la rhinite allergique, conjonctivite et l’urticaire chronique.',
                de: 'H1-Antihistaminikum der 2. Generation zur Linderung von Heuschnupfen, allergischer Rhinitis und chronischer Nesselsucht.',
                ko: '알레르기성 비염(재채기, 콧물), 알레르기성 결막염 및 만성 두드러기 완화를 위한 2세대 항히스타민제입니다.',
                pt: 'Anti-histamínico H1 de segunda geração para rinite alérgica, coceira nos olhos e urticária crônica.',
                ru: 'Антигистаминный препарат 2-го поколения для устранения симптомов аллергического ринита, конъюнктивита и крапивницы.'
            },
            sides: {
                en: 'Mild drowsiness or fatigue in sensitive individuals, dry mouth, and headache. Avoid operating heavy machinery if sedation occurs.',
                id: 'Rasa kantuk ringan pada individu sensitif, mulut kering, dan sakit kepala. Hindari mengemudi jika merasa mengantuk.',
                ja: '軽度の眠気、倦怠感、口渇、頭痛。眠気を感じる場合は車の運転や機械操作をお控えください。',
                es: 'Somnolencia leve en personas sensibles, sequedad de boca y cefalea. Evitar conducir si presenta sedación.',
                zh: '敏感人群可能出现轻度嗜睡、口干及头痛。服药后若有困意请勿驾驶车辆。',
                ar: 'نعاس خفيف أو إرهاق لدى بعض الأشخاص، جفاف الفم، وصداع. تجنب القيادة إذا شعرت بالنعاس.',
                fr: 'Légère somnolence chez certains patients, sécheresse buccale, maux de tête. Prudence au volant.',
                de: 'Leichte Schläfrigkeit bei empfindlichen Personen, Mundtrockenheit, Kopfschmerzen. Vorsicht im Straßenverkehr.',
                ko: '민감한 환자에게서 경미한 졸음, 구강 건조, 두통이 나타날 수 있습니다. 졸릴 경우 운전을 피하십시오.',
                pt: 'Sonolência leve em pessoas sensíveis, boca seca e dor de cabeça. Evite dirigir se sentir sono.',
                ru: 'Легкая сонливость у чувствительных пациентов, сухость во рту, головная боль. Соблюдать осторожность за рулем.'
            }
        },
        'salbutamol': {
            aliases: ['salbutamol', 'albuterol', 'ventolin', 'proair', 'proventil', 'asthalin', 'lasal'],
            generic: { en: 'Salbutamol / Albuterol Sulfate', id: 'Salbutamol Sulfat', ja: 'サルブタモール硫酸塩', es: 'Salbutamol', zh: '硫酸沙丁胺醇', ar: 'سالبوتامول', fr: 'Salbutamol', de: 'Salbutamol', ko: '살부타몰 황산염', pt: 'Salbutamol', ru: 'Сальбутамол' },
            classKey: 'Bronchodilator',
            uses: {
                en: 'Short-acting beta-2 adrenergic agonist (SABA) for rapid rescue relief and prevention of bronchospasm in asthma and COPD.',
                id: 'Bronkodilator kerja cepat (SABA) sebagai obat pelega saluran napas darurat untuk mengatasi sesak napas pada asma dan PPOK.',
                ja: '気管支喘息や慢性閉塞性肺疾患（COPD）における急性気管支痙攣・息切れを迅速に解除する短時間作用型β2刺激薬です。',
                es: 'Agonista beta-2 de acción corta para el alivio rápido del broncoespasmo en asma y enfermedad pulmonar obstructiva crónica (EPOC).',
                zh: '短效β2受体激动剂，用于支气管哮喘和慢性阻塞性肺病（COPD）急性支气管痉挛的快速平喘解痉。',
                ar: 'موسع قصبي سريع المفعول للإغاثة الفورية من ضيق التنفس والتشنج القصبي في الربو ومرض الانسداد الرئوي المزمن.',
                fr: 'Bêta-2 mimétique d’action rapide de secours pour soulager rapidement les crises d’asthme et les bronchospasmes de la BPCO.',
                de: 'Kurzwirksames Beta-2-Sympathomimetikum zur raschen Notfall-Linderung von Bronchospasmen bei Asthma bronchiale und COPD.',
                ko: '기관지 천식 및 만성 폐쇄성 폐질환(COPD) 시 급성 기관지 경련과 호흡 곤란을 신속히 완화하는 속효성 흡입 기관지 확장제입니다.',
                pt: 'Broncodilatador de ação rápida para alívio imediato do broncoespasmo na asma e DPOC.',
                ru: 'Бета-2-адреномиметик короткого действия для быстрого купирования приступов удушья и бронхоспазма при астме и ХОБЛ.'
            },
            sides: {
                en: 'Fine skeletal muscle tremor (especially hands), tachycardia, palpitations, and transient hypokalemia with high usage.',
                id: 'Gemetar halus pada tangan (tremor), detak jantung lebih cepat (takikardia), berdebar, dan sedikit gelisah.',
                ja: '手の細かなふるえ（振戦）、頻脈、動悸、一過性の血清カリウム値低下など。',
                es: 'Temblor fino en extremidades, taquicardia, palpitaciones y nerviosismo transitorio.',
                zh: '手部细微震颤、心动过速、心悸及短暂性紧张不安。',
                ar: 'رعشة خفيفة في اليدين، تسارع ضربات القلب، خفقان، وشعور مؤقت بالعصبية.',
                fr: 'Tremblements fins des extrémités, tachycardie, palpitations et nervosité passagère.',
                de: 'Feinschlägiger Tremor (Händezittern), Herzrasen (Tachykardie), Palpitationen und Unruhe.',
                ko: '손의 미세한 떨림, 빈맥, 가슴 두근거림 및 일시적 긴장감이 나타날 수 있습니다.',
                pt: 'Tremor fino nas mãos, taquicardia, palpitações e agitação passageira.',
                ru: 'Мелкий тремор рук, тахикардия, сердцебиение, кратковременное чувство тревоги.'
            }
        },
        'dexamethasone': {
            aliases: ['dexamethasone', 'decadron', 'dexona', 'kalmethasone', 'cortidex', 'indexon', 'lanadexon'],
            generic: { en: 'Dexamethasone Sodium Phosphate', id: 'Deksametason', ja: 'デキサメタゾン', es: 'Dexametasona', zh: '地塞米松', ar: 'ديكساميثازون', fr: 'Dexaméthasone', de: 'Dexamethason', ko: '덱사메타손', pt: 'Dexametasona', ru: 'Дексаметазон' },
            classKey: 'Corticosteroid',
            uses: {
                en: 'Potent synthetic glucocorticoid with powerful anti-inflammatory and immunosuppressive actions for severe allergies, autoimmune exacerbations, and cerebral edema.',
                id: 'Kortikosteroid potensi kuat dengan efek anti-inflamasi dan imunosupresif untuk mengatasi alergi berat, radang akut, dan penyakit autoimun.',
                ja: '強力な抗炎症作用および免疫抑制作用を持つ合成副腎皮質ホルモン剤。重症アレルギー、自己免疫疾患の急性増悪等に用いられます。',
                es: 'Glucocorticoide sintético potente con marcada acción antiinflamatoria e inmunosupresora para alergias graves y crisis autoinmunes.',
                zh: '高效人工合成糖皮质激素，具有强效抗炎和免疫抑制作用，用于严重过敏、自身免疫性疾病及水肿治疗。',
                ar: 'جلوكوكورتيكويد صناعي قوي ذو تأثيرات مضادة للالتهاب ومثبطة للمناعة للحساسية الشديدة ونوبات أمراض المناعة الذاتية.',
                fr: 'Glucocorticoïde puissant à action anti-inflammatoire et immunosuppressive majeure pour allergies sévères et poussées auto-immunes.',
                de: 'Hochwirksames Glukokortikoid mit starker entzündungshemmender und immunsuppressiver Wirkung bei schweren Allergien und Entzündungen.',
                ko: '강력한 항염증 및 면역 억제 작용을 지닌 합성 글루코코르티코이드로, 중증 알레르기 및 자가면역 질환 치료에 사용됩니다.',
                pt: 'Glicocorticoide sintético de alta potência com ação anti-inflamatória e imunossupressora para alergias graves.',
                ru: 'Синтетический глюкокортикостероид с мощным противовоспалительным и иммунодепрессивным действием при тяжелых аллергиях и воспалениях.'
            },
            sides: {
                en: 'Fluid retention, elevated blood glucose, insomnia, gastric irritation. Do not discontinue abruptly after prolonged use to prevent adrenal insufficiency.',
                id: 'Peningkatan gula darah, retensi cairan, insomnia, dan iritasi lambung. Jangan menghentikan obat secara mendadak setelah pemakaian lama.',
                ja: '血糖値上昇、体液貯留、不眠、胃刺激。長期連用後の急激な自己中止は副腎不全を招くため医師の指導に従って減量してください。',
                es: 'Aumento de glucemia, retención de líquidos, insomnio. No suspender bruscamente tras uso prolongado.',
                zh: '血糖升高、水钠潴留、失眠及胃部刺激。长期使用后严禁骤然停药，须遵医嘱逐渐减量。',
                ar: 'ارتفاع سكر الدم، احتباس السوائل، الأرق، وتهيج المعدة. لا تتوقف عن تناوله فجأة بعد الاستخدام لفترات طويلة.',
                fr: 'Élévation de la glycémie, rétention hydrosodée, insomnie. Ne jamais arrêter brutalement après un traitement prolongé.',
                de: 'Blutzuckeranstieg, Flüssigkeitsretention, Schlafstörungen. Nach längerer Einnahme niemals abrupt absetzen (ausschleichen).',
                ko: '혈당 상승, 체액 저류, 불면증, 위장 자극. 장기 복용 후에는 부신 기능 유지를 위해 갑자기 중단하지 마십시오.',
                pt: 'Aumento da glicemia, retenção de líquidos e insônia. Nunca interrompa abruptamente após uso prolongado.',
                ru: 'Повышение уровня сахара в крови, задержка жидкости, бессонница. Не прекращайте прием резко во избежание синдрома отмены.'
            }
        },
        'ciprofloxacin': {
            aliases: ['ciprofloxacin', 'cipro', 'ciproxin', 'baquinor', 'ciflox', 'cpro', 'renator'],
            generic: { en: 'Ciprofloxacin Hydrochloride', id: 'Siprofloksasin', ja: 'シプロフロキサシン', es: 'Ciprofloxacino', zh: '环丙沙星', ar: 'سيبروفلوكساسين', fr: 'Ciprofloxacine', de: 'Ciprofloxacin', ko: '시프로플록사신', pt: 'Ciprofloxacino', ru: 'Ципрофлоксацин' },
            classKey: 'Antibiotic',
            uses: {
                en: 'Broad-spectrum fluoroquinolone antibiotic targeting severe urinary tract infections, infectious diarrhea, bone/joint infections, and typhoid fever.',
                id: 'Antibiotik fluorokuinolon spektrum luas untuk mengatasi infeksi saluran kemih berat, diare infeksius/tifus, infeksi tulang, dan saluran cerna.',
                ja: '難治性尿路感染症、感染性腸炎、骨関節感染症、チフス等の治療に用いられる広域フルオロキノロン系抗菌薬です。',
                es: 'Antibiótico fluoroquinolona de amplio espectro para infecciones urinarias complicadas, diarrea infecciosa e infecciones óseas.',
                zh: '广谱氟喹诺酮类抗菌药物，用于治疗复杂的泌尿道感染、感染性腹泻、伤寒及骨关节感染。',
                ar: 'مضاد حيوي واسع النطاق من مجموعة الفلوروكينولون لعلاج التهابات المسالك البولية الشديدة، الإسهال المعدي وحمى التيفوئيد.',
                fr: 'Antibiotique fluoroquinolone à large spectre indiqué dans les infections urinaires sévères, diarrhées infectieuses et ostéoarticulaires.',
                de: 'Fluorchinolon-Antibiotikum zur Behandlung komplizierter Harnwegsinfektionen, infektiöser Diarrhö und Knocheninfektionen.',
                ko: '복합 요로 감염, 감염성 설사, 골관절 감염 및 장티푸스 치료에 사용되는 광범위 플루오로퀴놀론계 항생제입니다.',
                pt: 'Antibiótico fluoroquinolona de amplo espectro para infecções urinárias complicadas, diarreia infecciosa e febre tifoide.',
                ru: 'Фторхинолоновый антибиотик широкого спектра действия для лечения тяжелых инфекций мочевыводящих путей, кишечника и суставов.'
            },
            sides: {
                en: 'Tendonitis and tendon rupture risk (especially Achilles), QT prolongation, and photosensitivity. Avoid taking concurrently with calcium/iron supplements.',
                id: 'Risiko peradangan/cedera tendon (terutama tendon Achilles), sensitivitas terhadap sinar matahari, dan pusing. Hindari minum bersamaan dengan susu atau suplemen kalsium/zat besi.',
                ja: '腱炎および腱断裂（アキレス腱など）のリスク、光線過敏症、QT延長。カルシウムや鉄剤との同時服用は避けてください。',
                es: 'Riesgo de tendinitis y rotura de tendones (Aquiles), fotosensibilidad y prolongación del QT. Separar de lácteos y antiácidos.',
                zh: '肌腱炎与跟腱断裂风险、光敏反应及心电图QT延长。避免与钙剂、铁剂或含铝镁抗酸药同时服用。',
                ar: 'خطر التهاب وتمزق الأوتار (خاصة وتر العرقوب)، حساسية للضوء، واضطراب كهربية القلب. تجنب تناوله مع مكملات الكالسيوم أو الحديد.',
                fr: 'Risque de tendinite et de rupture tendineuse (tendon d’Achille), photosensibilité. Éviter la prise simultanée de calcium ou fer.',
                de: 'Risiko für Sehnenerkrankungen/Sehnenriss (Achillessehne), Photosensibilität. Nicht gleichzeitig mit Milch, Calcium oder Eisen einnehmen.',
                ko: '건염 및 아킬레스건 파열 위험, 광과민 반응이 있습니다. 칼슘, 철분 보충제 또는 유제품과 함께 복용하지 마십시오.',
                pt: 'Risco de tendinite e ruptura de tendão (Aquiles), fotossensibilidade. Não tome junto com antiácidos, leite ou ferro.',
                ru: 'Риск тендинита и разрыва сухожилий (особенно ахиллова), фотосенсибилизация. Не принимать одновременно с препаратами кальция и железа.'
            }
        },
        'losartan': {
            aliases: ['losartan', 'cozaar', 'acetensa', 'angioten', 'losartan potassium', 'insaar'],
            generic: { en: 'Losartan Potassium', id: 'Losartan Kalium', ja: 'ロサルタンカリウム', es: 'Losartán', zh: '氯沙坦钾', ar: 'لوسارتان بوتاسيوم', fr: 'Losartan', de: 'Losartan-Kalium', ko: '로사르탄 칼륨', pt: 'Losartana Potássica', ru: 'Лозартан калия' },
            classKey: 'ARB',
            uses: {
                en: 'Angiotensin II Receptor Blocker (ARB) for essential hypertension, cardiovascular risk reduction in left ventricular hypertrophy, and diabetic nephropathy.',
                id: 'Antagonis Reseptor Angiotensin (ARB) untuk menurunkan tekanan darah tinggi (hipertensi) dan melindungi ginjal pada penderita diabetes mellitus tipe 2.',
                ja: 'アンジオテンシンII受容体拮抗薬（ARB）。本態性高血圧症の降圧、心肥大を伴う高血圧、糖尿病性腎症の進行遅延に使用されます。',
                es: 'Bloqueador de receptores de angiotensina II para hipertensión arterial, reducción del riesgo vascular y protección renal.',
                zh: '血管紧张素II受体拮抗剂（ARB），用于原发性高血压治疗、降低心血管事件风险及保护2型糖尿病患者肾功能。',
                ar: 'حاصرات مستقبلات الأنجيوتنسين 2 لعلاج ارتفاع ضغط الدم الأساسي وحماية الكلى لدى مرضى السكري من النوع الثاني.',
                fr: 'Antagoniste des récepteurs de l’angiotensine II (ARA-II) pour l’hypertension artérielle et la protection rénale du diabétique.',
                de: 'Angiotensin-II-Rezeptorantagonist zur Behandlung von Bluthochdruck und zur Nephroprotektion bei Typ-2-Diabetes.',
                ko: '고혈압 치료, 좌심실 비대 환자의 뇌졸중 위험 감소 및 제2형 당뇨병 환자의 신장 보호를 위한 ARB 제제입니다.',
                pt: 'Bloqueador dos receptores de angiotensina II para hipertensão arterial e proteção renal no diabetes tipo 2.',
                ru: 'Антагонист рецепторов ангиотензина II для терапии артериальной гипертензии и защиты функции почек при сахарном диабете.'
            },
            sides: {
                en: 'Dizziness, fatigue, hypotension, and potential hyperkalemia. Excellent alternative for patients experiencing ACE-inhibitor induced dry cough.',
                id: 'Pusing, rasa lelah, dan potensi kenaikan kalium darah. Merupakan alternatif ideal bagi pasien yang mengalami batuk kering akibat obat ACE-inhibitor.',
                ja: 'めまい、立ちくらみ、倦怠感、高カリウム血症。ACE阻害薬による空咳が生じる患者様への優れた代替薬となります。',
                es: 'Mareos, fatiga e hiperpotasemia leve. Excelente alternativa para pacientes que sufren tos seca por IECA.',
                zh: '头晕、乏力及轻度血钾偏高。对于因服用ACE抑制剂而产生顽固性干咳的患者，为极佳替代方案。',
                ar: 'دوخة، تعب، واحتمال ارتفاع بوتاسيوم الدم. بديل ممتاز للمرضى الذين يعانون من السعال الجاف بسبب مثبطات ACE.',
                fr: 'Vertiges, fatigue et risque d’hyperkaliémie. Excellente alternative en cas de toux sèche sous IEC.',
                de: 'Schwindel, Müdigkeit, Hyperkaliämie. Sehr gute Alternative für Patienten mit ACE-Hemmer-bedingtem Reizhusten.',
                ko: '어지럼증, 피로감, 고칼륨혈증. ACE 억제제 복용 시 마른기침 부작용이 나타나는 환자에게 이상적인 대체제입니다.',
                pt: 'Tontura, fadiga e risco de hipercalemia. Excelente alternativa para quem apresenta tosse seca com inibidores da ECA.',
                ru: 'Головокружение, утомляемость, гиперкалиемия. Идеальная замена для пациентов с сухим кашлем от ингибиторов АПФ.'
            }
        },
        'simvastatin': {
            aliases: ['simvastatin', 'zocor', 'mersikol', 'sinova', 'valemia', 'simvotin'],
            generic: { en: 'Simvastatin', id: 'Simvastatin', ja: 'シンバスタチン', es: 'Simvastatina', zh: '辛伐他汀', ar: 'سيمفاستاتين', fr: 'Simvastatine', de: 'Simvastatin', ko: '심바스타틴', pt: 'Sinvastatina', ru: 'Симвастатин' },
            classKey: 'HMG-CoA Reductase Inhibitor',
            uses: {
                en: 'Lipid-lowering statin indicated for hypercholesterolemia, hypertriglyceridemia, and long-term reduction of coronary heart disease events.',
                id: 'Statin penurun kolesterol untuk mengontrol hiperkolesterolemia, trigliserida tinggi, dan menurunkan risiko serangan jantung koroner.',
                ja: '高コレステロール血症および家族性高脂血症の改善、冠動脈疾患イベントの抑制に使用されるスタチン系脂質異常症治療薬です。',
                es: 'Estatina hipolipemiante indicada para la hipercolesterolemia y la prevención a largo plazo de cardiopatía coronaria.',
                zh: '他汀类降脂药，用于治疗高胆固醇血症、混合型高脂血症及降低冠心病发病风险。',
                ar: 'ستاتين خافض للدهون لعلاج ارتفاع الكوليسترول في الدم والدهون الثلاثية والوقاية من أمراض القلب التاجية.',
                fr: 'Statine hypolipémiante indiquée dans les hypercholestérolémies et la prévention des accidents coronariens.',
                de: 'Lipidsenker aus der Gruppe der Statine zur Behandlung von Hypercholesterinämie und Prävention koronarer Herzkrankheiten.',
                ko: '고콜레스테롤혈증 및 이상지질혈증 개선과 관상동맥 심장질환 위험 감소를 위한 스타틴계 약물입니다.',
                pt: 'Estatina para tratamento de hipercolesterolemia e prevenção de eventos coronarianos.',
                ru: 'Гиполипидемический препарат группы статинов для снижения уровня общего холестерина и профилактики ИБС.'
            },
            sides: {
                en: 'Muscle pain or weakness (myopathy), elevated liver enzymes. Avoid grapefruit juice during therapy due to CYP3A4 interaction.',
                id: 'Nyeri atau lemah otot (miopati), peningkatan enzim hati. Hindari konsumsi jus grapefruit (jeruk bali merah) karena interaksi obat.',
                ja: '筋肉痛（ミオパチー）、肝酵素上昇。CYP3A4代謝阻害による副作用増大を防ぐためグレープフルーツジュースの摂取はお控えください。',
                es: 'Dolor muscular, alteración de enzimas hepáticas. Evitar el zumo de pomelo por interacción farmacológica.',
                zh: '肌痛、肌无力及肝酶升高。服药期间应避免饮用西柚汁（葡萄柚汁），以防血药浓度异常升高。',
                ar: 'آلام أو ضعف العضلات، وارتفاع إنزيمات الكبد. تجنب تناول عصير الجريب فروت أثناء العلاج.',
                fr: 'Myalgies, élévation des transaminases. Éviter le jus de pamplemousse pendant le traitement.',
                de: 'Muskelschmerzen (Myopathie), Transaminasenerhöhung. Grapefruitsaft wegen Wechselwirkungen strikt meiden.',
                ko: '근육통, 간 효소 수치 상승. 상호작용으로 인한 부작용 위험을 방지하기 위해 자몽 주스 섭취를 피하십시오.',
                pt: 'Dores musculares, alteração nas enzimas do fígado. Evite suco de toranja (grapefruit) durante o tratamento.',
                ru: 'Мышечные боли (миопатия), повышение ферментов печени. Избегайте грейпфрутового сока из-за лекарственного взаимодействия.'
            }
        },
        'pantoprazole': {
            aliases: ['pantoprazole', 'protonix', 'pantozol', 'panloc', 'pepzol', 'pantocid'],
            generic: { en: 'Pantoprazole Sodium', id: 'Pantoprazol', ja: 'パントプラゾール', es: 'Pantoprazol', zh: '泮托拉唑', ar: 'بانتوبرازول', fr: 'Pantoprazole', de: 'Pantoprazol', ko: '판토프라졸', pt: 'Pantoprazol', ru: 'Пантопразол' },
            classKey: 'Proton Pump Inhibitor',
            uses: {
                en: 'Targeted proton pump inhibitor for erosive esophagitis, gastroesophageal reflux disease (GERD), and pathological hypersecretory conditions.',
                id: 'Inhibitor pompa proton terarah untuk meredakan nyeri ulu hati karena GERD, mengobati radang esofagus erosif, dan tukak lambung.',
                ja: '胃酸分泌を特異的に抑制し、逆流性食道炎、胃潰瘍、十二指腸潰瘍の治癒を促進するプロトンポンプ阻害薬です。',
                es: 'Inhibidor de la bomba de protones para la esofagitis erosiva, reflujo gastroesofágico (ERGE) y úlcera péptica.',
                zh: '靶向质子泵抑制剂，用于治疗糜烂性食管炎、胃食管反流病（GERD）及胃十二指肠溃疡。',
                ar: 'مثبط مضخة البروتون لعلاج التهاب المريء التآكلي، ارتجاع المريء، وقرحة الجهاز الهضمي.',
                fr: 'Inhibiteur de la pompe à protons prescrit dans l’œsophagite par reflux, le RGO et les ulcères gastroduodénaux.',
                de: 'Protonenpumpenhemmer zur Behandlung von Refluxösophagitis, GERD und Magengeschwüren.',
                ko: '역류성 식도염, 위식도역류질환(GERD) 및 소화성 궤양 치료를 위한 표적 위산분비 억제제입니다.',
                pt: 'Inibidor da bomba de prótons para esofagite erosiva, refluxo gastroesofágico (DRGE) e úlceras.',
                ru: 'Ингибитор протонной помпы для лечения эрозивного эзофагита, рефлюкс-эзофагита и язвенной болезни.'
            },
            sides: {
                en: 'Mild diarrhea, flatulence, headache, abdominal pain. Highly favorable drug-drug interaction profile among PPIs.',
                id: 'Diare ringan, perut kembung, sakit kepala, atau kram perut. Memiliki profil interaksi obat yang relatif sangat aman di antara golongan PPI.',
                ja: '軟便、腹部膨満感、頭痛、腹痛。他のPPIと比較して薬物相互作用が少ない特徴があります。',
                es: 'Diarrea leve, dolor de cabeza, flatulencia. Excelente perfil de tolerancia e interacción baja.',
                zh: '轻度腹泻、腹胀、头痛及腹部不适。在质子泵抑制剂中具有极佳的药物相互作用安全性。',
                ar: 'إسهال خفيف، انتفاخ البطن، صداع، وآلام في البطن. يتميز بقلة التفاعلات الدوائية مقارنة بمثبطات البروتون الأخرى.',
                fr: 'Diarrhée passagère, flatulences, céphalées. Faible potentiel d’interactions médicamenteuses.',
                de: 'Leichte Diarrhö, Blähungen, Kopfschmerzen. Geringes Wechselwirkungspotenzial unter den PPI.',
                ko: '경미한 설사, 복부 팽만, 두통. 다른 PPI 계열 약물 대비 약물 상호작용 위험이 적어 안전성이 우수합니다.',
                pt: 'Diarreia leve, gases, dor de cabeça. Baixo potencial de interações medicamentosas.',
                ru: 'Легкая диарея, метеоризм, головная боль. Характеризуется минимальным риском лекарственных взаимодействий среди ИПП.'
            }
        },
        'furosemide': {
            aliases: ['furosemide', 'lasix', 'uresix', 'farsix', 'frusemide'],
            generic: { en: 'Furosemide', id: 'Furosemid', ja: 'フロセミド', es: 'Furosemida', zh: '呋塞米', ar: 'فوروسيميد', fr: 'Furosémide', de: 'Furosemid', ko: '푸로세미드', pt: 'Furosemida', ru: 'Фуросемид' },
            classKey: 'Diuretic',
            uses: {
                en: 'High-ceiling loop diuretic for rapid removal of fluid retention (edema) associated with congestive heart failure, hepatic cirrhosis, and renal disease.',
                id: 'Diuretik loop potensi kuat untuk membuang kelebihan cairan tubuh (edema/bengkak) pada gagal jantung, sirosis hati, dan gangguan ginjal.',
                ja: 'うっ血性心不全、肝硬変、腎疾患に伴う浮腫（むくみ）および高血圧症を改善する強力なループ利尿薬です。',
                es: 'Diurético de asa de alta potencia para el tratamiento rápido del edema en insuficiencia cardíaca, cirrosis y enfermedad renal.',
                zh: '强效袢利尿剂，用于快速消除充血性心力衰竭、肝硬化及肾脏疾病引起的水肿及体液潴留。',
                ar: 'مدر للبول عالي الفعالية للتخلص السريع من احتباس السوائل (الوذمة) المرتبطة بقصور القلب، وتليف الكبد، وأمراض الكلى.',
                fr: 'Diurétique de l’anse puissant indiqué dans le traitement rapide des œdèmes liés à l’insuffisance cardiaque ou rénale.',
                de: 'Stark wirksames Schleifendiuretikum zur raschen Ausschwemmung von Ödemen bei Herzinsuffizienz und Nierenerkrankungen.',
                ko: '울혈성 심부전, 간경변 및 신장 질환에 동반된 부종과 체액 저류를 신속히 배출하는 고효능 루프 이뇨제입니다.',
                pt: 'Diurético de alça potente para alívio rápido do edema associado à insuficiência cardíaca e doença renal.',
                ru: 'Мощный петлевой диуретик для быстрого выведения избытка жидкости (отеков) при сердечной, печеночной и почечной недостаточности.'
            },
            sides: {
                en: 'Electrolyte depletion (hypokalemia, hyponatremia), dehydration, and orthostatic dizziness. Potassium monitoring is recommended.',
                id: 'Penurunan kadar elektrolit tubuh (kalium rendah/hipokalemia), dehidrasi, dan pusing saat berdiri. Disarankan pantau kadar kalium.',
                ja: '電解質喪失（低カリウム血症、低ナトリウム血症）、脱水症状、立ちくらみ。定期的なカリウム濃度の確認が推奨されます。',
                es: 'Pérdida de electrolitos (hipopotasemia), deshidratación y mareo postural. Se recomienda control de potasio.',
                zh: '易引起电解质紊乱（低钾血症、低钠血症）、脱水及体位性头晕。用药期间建议定期监测血钾。',
                ar: 'نقص الشوارد (انخفاض البوتاسيوم والصوديوم)، الجفاف، وهبوط الضغط الانتصابي. ينصح بفحص مستوى البوتاسيوم.',
                fr: 'Perte d’électrolytes (hypokaliémie), déshydratation, hypotension orthostatique. Contrôle du potassium conseillé.',
                de: 'Elektrolytverlust (Hypokaliämie), Dehydratation, Schwindel beim Aufstehen. Kaliumspiegel regelmäßig prüfen.',
                ko: '전해질 불균형(저칼륨혈증, 저나트륨혈증), 탈수 및 기립성 어지럼증. 혈중 칼륨 수치 모니터링이 권장됩니다.',
                pt: 'Perda de eletrólitos (hipocalemia), desidratação e tontura ao se levantar. Acompanhar níveis de potássio.',
                ru: 'Потеря электролитов (гипокалиемия, гипонатриемия), обезвоживание, ортостатическое головокружение. Необходим контроль калия.'
            }
        }
    },

    // Generalized clinical descriptions per drug class for automatic high-quality humanization
    classFallbacks: {
        'Analgesic / Antipyretic': {
            uses: {
                en: 'Clinically indicated to relieve mild to moderate pain (headaches, muscular aches, body pain) and reduce elevated body temperature during fever episodes.',
                id: 'Diindikasikan secara klinis untuk meredakan nyeri ringan hingga sedang (sakit kepala, nyeri otot, sakit badan) serta menurunkan demam secara efektif.',
                ja: '頭痛、筋肉痛などの軽度から中等度の痛みの鎮痛、および発熱時の解熱を目的として臨床使用されます。',
                es: 'Indicado para el alivio del dolor leve a moderado y la reducción eficaz de la temperatura corporal en cuadros febriles.',
                zh: '临床用于缓解轻至中度疼痛（头痛、肌肉酸痛等）并有效降低发热体温。',
                ar: 'يُستعمل لتخفيف الآلام الخفيفة إلى المتوسطة وخفض درجة حرارة الجسم المرتفعة أثناء الحمى.',
                fr: 'Indiqué pour le soulagement des douleurs légères à modérées et la réduction de la fièvre.',
                de: 'Indiziert zur Linderung leichter bis mäßiger Schmerzen und zur wirksamen Senkung von Fieber.',
                ko: '경도 및 중등도의 통증 완화와 발열 시 체온 강하를 위해 처방됩니다.',
                pt: 'Indicado para o alívio de dores leves a moderadas e redução eficaz da febre.',
                ru: 'Применяется для купирования умеренной боли и снижения повышенной температуры тела при лихорадке.'
            },
            sides: {
                en: 'Generally well-tolerated when used at recommended doses. Do not exceed maximum daily limits and avoid heavy alcohol consumption.',
                id: 'Umumnya ditoleransi dengan baik pada dosis anjuran. Jangan melebihi dosis harian maksimal dan hindari alkohol.',
                ja: '推奨用量では良好な耐容性を示します。1日最大用量を超えないようにし、飲酒時の服用は避けてください。',
                es: 'Bien tolerado en dosis recomendadas. No exceder el límite diario ni combinar con alcohol.',
                zh: '在推荐剂量下耐受良好。严禁超量服用，服药期间避免大量饮酒。',
                ar: 'يتحمله الجسم جيداً عند الالتزام بالجرعة الموصى بها. تجنب الإفراط في الجرعة أو تناول الكحول.',
                fr: 'Bien toléré aux doses recommandées. Ne pas dépasser la dose maximale et éviter l’alcool.',
                de: 'In empfohlener Dosierung gut verträglich. Maximale Tagesdosis nicht überschreiten.',
                ko: '권장 용량에서는 안전하게 복용 가능합니다. 일일 최대 복용량을 초과하지 마십시오.',
                pt: 'Bem tolerado nas doses recomendadas. Não ultrapasse a dose máxima diária.',
                ru: 'Хорошо переносится в терапевтических дозах. Не превышайте максимальную суточную норму.'
            }
        },
        'Antibiotic': {
            uses: {
                en: 'Antibacterial medication prescribed to eliminate susceptible bacterial infections and inhibit microbial growth across targeted organ systems.',
                id: 'Obat antibakteri yang diresepkan untuk membasmi infeksi bakteri sensitif dan menghentikan perkembangbiakan mikroorganisme patogen.',
                ja: '感受性細菌による感染症を治療し、病原微生物の増殖を抑制するために処方される抗菌・抗生物質製剤です。',
                es: 'Medicamento antibacteriano prescrito para eliminar infecciones bacterianas y frenar el crecimiento microbiano.',
                zh: '用于治疗敏感细菌引起的各类细菌感染，抑制并清除体内病原微生物。',
                ar: 'دواء مضاد للبكتيريا يصف للقضاء على الالتهابات البكتيرية ومنع تكاثر الميكروبات.',
                fr: 'Médicament antibactérien prescrit pour éliminer les infections bactériennes sensibles.',
                de: 'Antibakterielles Arzneimittel zur Beseitigung bakterieller Infektionen und Hemmung des mikrobiellen Wachstums.',
                ko: '감수성 원인균에 의한 세균성 감염증을 치료하고 미생물 증식을 억제하는 항생제입니다.',
                pt: 'Medicamento antibacteriano prescrito para combater infecções bacterianas e inibir microrganismos.',
                ru: 'Антибактериальный препарат для эрадикации чувствительных бактериальных инфекций.'
            },
            sides: {
                en: 'Digestive discomfort, mild nausea, or loose stools. Complete full prescribed course to prevent bacterial resistance.',
                id: 'Gangguan pencernaan ringan, mual, atau tinja cair. Wajib dihabiskan sesuai resep dokter guna mencegah resistensi bakteri.',
                ja: '軟便、下痢、軽度の吐き気など。耐性菌の出現を防ぐため医師の指示通り最後まで服用してください。',
                es: 'Molestias digestivas o diarrea leve. Completar el tratamiento completo para evitar resistencias bacterianas.',
                zh: '可能出现轻度腹泻、恶心等消化道反应。请务必按疗程服完以防细菌耐药。',
                ar: 'اضطراب معوي خفيف أو إسهال. يجب إكمال كامل فترة العلاج الموصوفة لمنع مقاومة البكتيريا.',
                fr: 'Troubles digestifs légers ou diarrhée. Poursuivre le traitement jusqu’au bout pour éviter toute résistance.',
                de: 'Leichte Magen-Darm-Beschwerden oder Durchfall. Behandlung immer vollständig abschließen.',
                ko: '경미한 소화불량이나 설사. 내성균 발생을 방지하기 위해 처방된 복용 기간을 반드시 완료하십시오.',
                pt: 'Desconforto digestivo ou diarreia leve. Complete todo o tratamento prescrito para evitar resistência.',
                ru: 'Диспепсия, диарея, тошнота. Принимайте полный назначенный курс для предотвращения устойчивости бактерий.'
            }
        },
        'Antihypertensive': {
            uses: {
                en: 'Cardiovascular medication indicated to lower elevated blood pressure, reduce arterial vascular resistance, and protect vital target organs.',
                id: 'Obat kardiovaskular untuk menurunkan tekanan darah tinggi, mengurangi beban kerja jantung, dan melindungi organ vital dari komplikasi.',
                ja: '高血圧症において血圧を安定的に降圧し、動脈壁の負担を軽減して心血管および臓器障害を予防します。',
                es: 'Medicamento cardiovascular indicado para reducir la presión arterial elevada y proteger órganos diana.',
                zh: '用于平稳降低高血压水平，减轻血管阻力并保护心脏及脑血管等重要器官。',
                ar: 'دواء للقلب والأوعية الدموية لخفض ضغط الدم المرتفع وتقليل العبء على الشرايين وحماية الأعضاء الحيوية.',
                fr: 'Médicament cardiovasculaire pour abaisser la pression artérielle et protéger les organes cibles.',
                de: 'Kardiovaskuläres Medikament zur verlässlichen Blutdrucksenkung und zum Schutz der Zielorgane.',
                ko: '혈압을 정상 범위로 안정화하고 심혈관계 부담을 줄여 합병증을 예방하는 혈압강하제입니다.',
                pt: 'Medicamento cardiovascular indicado para controlar a pressão alta e proteger os órgãos vitais.',
                ru: 'Препарат для снижения повышенного артериального давления и защиты органов-мишеней от осложнений.'
            },
            sides: {
                en: 'Dizziness upon standing, lightheadedness, and mild fatigue. Regularly monitor blood pressure readings.',
                id: 'Pusing saat berdiri mendadak (hipotensi ortostatik) dan rasa lelah. Lakukan pemeriksaan tekanan darah secara berkala.',
                ja: '立ちくらみ、めまい、軽度の疲労感。血圧を定期的に測定・記録してください。',
                es: 'Mareos posturales y fatiga leve. Se aconseja monitoreo regular de la presión.',
                zh: '起立时轻微头晕、乏力感。服药期间请坚持定期监测血压指标。',
                ar: 'دوخة عند الوقوف وشعور بالخمول. يوصى بمراقبة قياسات ضغط الدم بانتظام.',
                fr: 'Vertiges au lever et fatigue passagère. Surveillance régulière de la tension requise.',
                de: 'Schwindel beim Aufstehen und leichte Müdigkeit. Regelmäßige Blutdruckkontrolle empfohlen.',
                ko: '기립성 어지럼증 및 피로감이 있을 수 있으므로 정기적으로 혈압을 측정하십시오.',
                pt: 'Tontura ao se levantar e fadiga leve. Monitore a pressão arterial regularmente.',
                ru: 'Головокружение при резкой смене положения тела, слабость. Регулярно контролируйте АД.'
            }
        },
        'Antidiabetic': {
            uses: {
                en: 'Metabolic therapy designed to optimize glycemic control, improve insulin sensitivity, and lower risks of microvascular complications.',
                id: 'Terapi metabolik untuk mengontrol kadar glukosa darah, meningkatkan sensitivitas insulin, dan mencegah komplikasi diabetes.',
                ja: '血糖値を良好にコントロールし、インスリン作用を改善して糖尿病性合併症を予防する代謝性治療薬です。',
                es: 'Tratamiento metabólico para optimizar el control glucémico y prevenir complicaciones microvasculares.',
                zh: '用于调控血糖水平，改善胰岛素敏感性并预防糖尿病慢性微血管并发症。',
                ar: 'علاج أيضي لضبط مستوى السكر في الدم وتحسين حساسية الأنسولين والوقاية من مضاعفات السكري.',
                fr: 'Traitement métabolique pour équilibrer la glycémie et prévenir les complications du diabète.',
                de: 'Stoffwechseltherapie zur optimalen Blutzuckerkontrolle und Vermeidung von Folgeschäden.',
                ko: '혈당을 조절하고 인슐린 반응성을 개선하여 당뇨 합병증을 예방하는 경구용 치료제입니다.',
                pt: 'Terapia para controle glicêmico adequado e prevenção de complicações do diabetes.',
                ru: 'Препарат для нормализации уровня сахара в крови и профилактики диабетических осложнений.'
            },
            sides: {
                en: 'Potential gastrointestinal changes or hypoglycemia risk if meals are delayed. Maintain consistent meal schedules.',
                id: 'Gangguan pencernaan ringan atau risiko hipoglikemia jika telat makan. Pertahankan pola makan teratur.',
                ja: '消化器不快感や、食事遅延時の低血糖リスク。規則正しい食事摂取を心がけてください。',
                es: 'Molestias digestivas o riesgo de hipoglucemia si se saltan comidas. Mantener horarios regulares.',
                zh: '可能出现肠胃不适，若进餐不及时需防范低血糖。请保持规律饮食习惯。',
                ar: 'اضطرابات معوية أو خطر انخفاض السكر عند تأخر الوجبات. حافظ على مواعيد طعام منتظمة.',
                fr: 'Troubles digestifs ou hypoglycémie en cas de repas différé. Conserver des horaires réguliers.',
                de: 'Magen-Darm-Reaktionen oder Unterzuckerungsgefahr bei unregelmäßigen Mahlzeiten.',
                ko: '소화기계 불편감이나 식사를 거를 경우 저혈당 위험이 있으므로 규칙적인 식사를 유지하십시오.',
                pt: 'Desconforto digestivo ou hipoglicemia se houver atraso nas refeições.',
                ru: 'Диспепсия или риск гипогликемии при пропуске приема пищи. Соблюдайте режим питания.'
            }
        },
        'Proton Pump Inhibitor': {
            uses: {
                en: 'Gastric acid suppressing therapy indicated for gastroesophageal reflux (GERD), heartburn, erosive esophagitis, and peptic ulcer disease.',
                id: 'Pereda produksi asam lambung untuk mengatasi refluks asam lambung (GERD), rasa perih panas di dada (heartburn), dan tukak lambung.',
                ja: '胃酸分泌を強力に抑制し、胃食道逆流症（GERD）、胸やけ、胃・十二指腸潰瘍の治癒を促します。',
                es: 'Supresor de ácido gástrico para reflujo gastroesofágico, acidez estomacal y úlceras pépticas.',
                zh: '强效抑制胃酸分泌，用于治疗胃食管反流病、烧心烧灼感及消化性溃疡。',
                ar: 'دواء لتثبيط إفراز حمض المعدة وعلاج ارتجاع المريء، حموضة المعدة، والقرحة الهضمية.',
                fr: 'Inhibiteur d’acidité gastrique pour le reflux gastro-œsophagien, les brûlures d’estomac et ulcères.',
                de: 'Magensäureblocker zur Behandlung von Refluxkrankheit, Sodbrennen und Magengeschwüren.',
                ko: '위산 분비를 억제하여 위식도 역류질환, 속쓰림 및 위궤양을 치료합니다.',
                pt: 'Inibidor de acidez gástrica para refluxo gastroesofágico, azia e úlceras pépticas.',
                ru: 'Препарат для снижения секреции кислоты при рефлюксе (ГЭРБ), изжоге и язве желудка.'
            },
            sides: {
                en: 'Headache, minor changes in bowel habits. Take before the first meal of the day as directed.',
                id: 'Sakit kepala ringan, kembung atau perubahan pola buang air besar. Sebaiknya diminum sebelum makan pagi.',
                ja: '頭痛、便秘または軟便。医師の指示に従い朝食前に服用することが推奨されます。',
                es: 'Cefalea, diarrea o estreñimiento leve. Tomar preferentemente antes del desayuno.',
                zh: '头痛、轻微便秘或腹泻。建议于清晨早餐前半小时按医嘱服用。',
                ar: 'صداع خفيف أو اضطراب في حركة الأمعاء. يُفضل تناوله قبل وجبة الإفطار.',
                fr: 'Maux de tête, légers troubles du transit. Prendre de préférence avant le premier repas.',
                de: 'Kopfschmerzen, leichte Stuhlunregelmäßigkeiten. Vorzugsweise vor dem Frühstück einnehmen.',
                ko: '두통, 경미한 배변 변화. 대개 아침 식전에 복용하는 것이 효과적입니다.',
                pt: 'Dor de cabeça, alteração intestinal leve. Tome preferencialmente antes do café da manhã.',
                ru: 'Головная боль, диспепсия. Рекомендуется принимать за 30 минут до завтрака.'
            }
        },
        'HMG-CoA Reductase Inhibitor': {
            uses: {
                en: 'Statin lipid regulator prescribed to lower low-density lipoprotein (LDL) cholesterol and protect cardiovascular arterial health.',
                id: 'Obat statin untuk menurunkan kadar kolesterol jahat (LDL), trigliserida, dan menjaga kesehatan pembuluh darah jantung.',
                ja: '悪玉コレステロール（LDL-C）を低下させ、動脈硬化の進展を防いで心血管疾患を予防します。',
                es: 'Estatina para reducir el colesterol LDL y proteger la salud arterial cardiovascular.',
                zh: '他汀类降脂药，有效降低低密度脂蛋白胆固醇（LDL），保护心脑血管健康。',
                ar: 'ستاتين خافض للدهون لتقليل الكوليسترول الضار وحماية الشرايين والقلب.',
                fr: 'Statine pour réduire le cholestérol LDL et protéger la santé des artères cardiovasculaires.',
                de: 'Statin zur Senkung des LDL-Cholesterins und zum Schutz der arteriellen Gefäße.',
                ko: '나쁜 콜레스테롤(LDL)을 낮추고 혈관 건강을 지켜 심혈관 질환을 예방합니다.',
                pt: 'Estatina para reduzir o colesterol LDL e proteger a saúde das artérias.',
                ru: 'Препарат для снижения «плохого» холестерина (ЛПНП) и профилактики атеросклероза.'
            },
            sides: {
                en: 'Mild muscular soreness or fatigue. Report unexplained severe muscle pain or tea-colored urine promptly.',
                id: 'Nyeri atau pegal otot ringan. Segera beritahu dokter jika timbul nyeri otot parah atau urin berwarna gelap.',
                ja: '筋肉の張りや違和感。強い筋肉痛や赤褐色尿が現れた場合は速やかに医師へご相談ください。',
                es: 'Molestias musculares leves. Notificar al médico si nota dolores musculares intensos.',
                zh: '轻微肌肉酸痛。若出现持续性剧烈肌肉疼痛或深褐色尿液，请及时咨询医生。',
                ar: 'آلام عضلية خفيفة. أبلغ الطبيب فوراً في حال حدوث ألم عضلي شديد أو تغير لون البول.',
                fr: 'Douleurs musculaires modérées. Signaler toute douleur musculaire inhabituelle et persistante.',
                de: 'Leichte Muskelschmerzen. Bei starken Schmerzen oder dunklem Urin sofort Arzt aufsuchen.',
                ko: '경미한 근육통. 원인 불명의 심한 근육 통증이나 진한 소변이 나타나면 즉시 진료를 받으십시오.',
                pt: 'Dores musculares leves. Avise o médico se sentir dores musculares intensas.',
                ru: 'Мышечный дискомфорт. При выраженных болях в мышцах или темной моче срочно обратитесь к врачу.'
            }
        },
        'Antihistamine': {
            uses: {
                en: 'Antiallergic agent to suppress histamine release, relieving allergic rhinitis, sneezing, itchy watery eyes, and skin hives.',
                id: 'Pereda reaksi alergi untuk meredakan hidung mampet/meler, bersin-bersin, mata gatal berair, dan bentol biduran pada kulit.',
                ja: 'ヒスタミン受容体を遮断し、アレルギー性鼻炎、くしゃみ、目のかゆみ、皮膚の蕁麻疹を鎮めます。',
                es: 'Antialérgico para aliviar la rinitis, estornudos, picor ocular y urticaria cutánea.',
                zh: '抗过敏用药，阻断组胺释放，有效缓解过敏性鼻炎、打喷嚏、眼痒流泪及皮肤风疹块。',
                ar: 'مضاد للحساسية لتخفيف أعراض التهاب الأنف التحسسي، العطس، حكة العين، وطفح الجلد.',
                fr: 'Antiallergique pour soulager la rhinite, les éternuements, démangeaisons oculaires et urticaire.',
                de: 'Allergiemittel zur Linderung von Heuschnupfen, Niesreiz, juckenden Augen und Nesselsucht.',
                ko: '알레르기 비염, 재채기, 눈 가려움증 및 피부 두드러기 증상을 완화합니다.',
                pt: 'Antialérgico para aliviar rinite, espirros, coceira nos olhos e urticária na pele.',
                ru: 'Противоаллергическое средство для снятия заложенности носа, чихания, зуда в глазах и крапивницы.'
            },
            sides: {
                en: 'Mild drowsiness or dry mouth in some individuals. Avoid alcoholic drinks or tasks requiring sharp alertness if drowsy.',
                id: 'Rasa kantuk ringan atau mulut kering pada sebagian orang. Hindari mengemudi jika merasa mengantuk.',
                ja: '軽度の眠気や口の渇き。眠気を感じる場合は運転や集中を要する作業を避けてください。',
                es: 'Somnolencia leve o sequedad bucal. Evitar conducir si siente adormecimiento.',
                zh: '部分患者可能出现轻度困倦或口干。若有倦意，应避免驾驶或操作精密机械。',
                ar: 'نعاس خفيف أو جفاف في الفم. تجنب القيادة إذا شعرت بالنعاس.',
                fr: 'Somnolence légère ou bouche sèche. Éviter la conduite en cas de somnolence.',
                de: 'Leichte Schläfrigkeit oder Mundtrockenheit. Bei Müdigkeit nicht Auto fahren.',
                ko: '경미한 졸림이나 입마름이 있을 수 있으니 졸릴 경우 운전을 피하십시오.',
                pt: 'Sonolência leve ou boca seca. Evite dirigir caso sinta sonolência.',
                ru: 'Возможна легкая сонливость или сухость во рту. При сонливости воздержитесь от вождения.'
            }
        },
        'General': {
            uses: {
                en: 'Clinically formulated therapeutic medication indicated for targeted symptom management, disease modification, and patient wellness.',
                id: 'Obat terapeutik terstandar yang diformulasikan secara klinis untuk penanganan gejala dan mendukung pemulihan kesehatan pasien.',
                ja: '病態の改善、症状の緩和、および患者様の健康回復を目的として処方される医療用医薬品です。',
                es: 'Medicamento formulado clínicamente para el manejo sintomático y la recuperación de la salud.',
                zh: '经过临床验证的药物制剂，用于针对性治疗、缓解临床症状及促进机体康复。',
                ar: 'دواء علاجي مصمم سريرياً لتخفيف الأعراض ودعم الشفاء وتحسين الحالة الصحية.',
                fr: 'Médicament thérapeutique formulé pour la prise en charge clinique et le rétablissement du patient.',
                de: 'Klinisch formuliertes Arzneimittel zur gezielten Linderung von Beschwerden und Förderung der Genesung.',
                ko: '질환의 치료, 임상 증상 완화 및 환자의 빠른 회복을 위해 처방되는 표준 의약품입니다.',
                pt: 'Medicamento formulado clinicamente para o alívio de sintomas e recuperação da saúde.',
                ru: 'Клинически одобренный лекарственный препарат для эффективной терапии и улучшения самочувствия.'
            },
            sides: {
                en: 'Use strictly as prescribed by a licensed healthcare provider. Review active health conditions with your pharmacist.',
                id: 'Gunakan secara teratur sesuai anjuran dokter atau apoteker. Konsultasikan jika timbul reaksi tidak biasa.',
                ja: '医師・薬剤師の指示通り正しく服用してください。気になる体調変化がある場合はご相談ください。',
                es: 'Utilizar estrictamente según las indicaciones de su médico. Consulte con su farmacéutico.',
                zh: '请严格遵医嘱服用。如出现任何异常身体不适反应，请及时向医师或药师咨询。',
                ar: 'يُستخدم بدقة وفقاً لتعليمات الطبيب أو الصيدلي. راجع طبيبك عند ظهور أي أعراض غير معتادة.',
                fr: 'À utiliser selon la prescription médicale. Demandez conseil à votre médecin ou pharmacien.',
                de: 'Streng nach ärztlicher Anweisung anwenden. Bei Fragen wenden Sie sich an Ihren Apotheker.',
                ko: '의사 또는 약사의 지도에 따라 정확히 복용하십시오. 이상 반응이 있을 경우 상담하십시오.',
                pt: 'Utilize conforme a orientação médica. Consulte o farmacêutico em caso de dúvidas.',
                ru: 'Применяйте строго по назначению врача. При возникновении вопросов проконсультируйтесь с фармацевтом.'
            }
        }
    },

    // Clean dense / boilerplate raw FDA text for English mode
    cleanRawEnglish(text, maxLen = 240) {
        if (!text) return '';
        let cleaned = text
            .replace(/^INDICATIONS\s+AND\s+USAGE:?\s*/i, '')
            .replace(/^ADVERSE\s+REACTIONS:?\s*/i, '')
            .replace(/^WARNINGS\s+AND\s+PRECAUTIONS:?\s*/i, '')
            .replace(/^CONTRAINDICATIONS:?\s*/i, '')
            .replace(/\s+/g, ' ')
            .trim();
        
        if (cleaned.length > maxLen) {
            const cut = cleaned.substring(0, maxLen);
            const lastDot = cut.lastIndexOf('.');
            if (lastDot > 80) {
                return cut.substring(0, lastDot + 1);
            }
            return cut.trim() + '...';
        }
        return cleaned;
    },

    // Intelligent transformer for any drug input across 11 languages
    humanize(drug, lang = 'en') {
        if (!drug) return null;
        const name = (drug.name || '').trim();
        const generic = (drug.generic || drug.generic_name || '').trim();
        const rawClass = (drug.class || drug.drug_class || 'Therapeutic Agent').trim();
        const rawUses = (drug.uses || '').trim();
        const rawSides = (drug.side_effects || '').trim();
        const rawWarnings = (drug.warnings || '').trim();
        const rawDosage = (drug.dosage || '').trim();
        const rawInteractions = (drug.interactions || '').trim();

        const dict = (typeof PHARMASIS_TRANSLATIONS !== 'undefined' && PHARMASIS_TRANSLATIONS[lang]) 
            ? PHARMASIS_TRANSLATIONS[lang] 
            : (typeof PHARMASIS_TRANSLATIONS !== 'undefined' ? PHARMASIS_TRANSLATIONS['en'] : {});
            
        const genericPrefix = dict.med_repo_generic || 'Generic';
        const lowerName = name.toLowerCase();
        const lowerGeneric = generic.toLowerCase();

        // 1. Check exact or alias match in comprehensive clinical profiles (20+ drugs)
        let matchedKey = null;
        for (const [key, profile] of Object.entries(this.clinicalProfiles)) {
            if (lowerName.includes(key) || lowerGeneric.includes(key)) {
                matchedKey = key;
                break;
            }
            if (profile.aliases && profile.aliases.some(alias => lowerName.includes(alias) || lowerGeneric.includes(alias))) {
                matchedKey = key;
                break;
            }
        }

        if (matchedKey) {
            const profile = this.clinicalProfiles[matchedKey];
            const localizedClass = this.classTranslations[profile.classKey]?.[lang] || profile.classKey;
            const localizedGenericName = profile.generic[lang] || profile.generic['en'] || generic;

            return {
                ...drug,
                name: name,
                displayClass: localizedClass,
                displayGeneric: localizedGenericName ? `${genericPrefix}: ${localizedGenericName}` : (dict.med_repo_standard_formulation || 'Standard Formulation'),
                displayUses: profile.uses[lang] || profile.uses['en'],
                displaySideEffects: profile.sides[lang] || profile.sides['en'],
                displayWarnings: profile.warnings?.[lang] || profile.warnings?.['en'] || rawWarnings,
                displayDosage: profile.dosage?.[lang] || profile.dosage?.['en'] || rawDosage,
                displayInteractions: profile.interactions?.[lang] || profile.interactions?.['en'] || rawInteractions,
                contextLang: lang
            };
        }

        // 2. Class Translation & Inference
        let matchedClassKey = null;
        let translatedClass = rawClass;
        
        for (const [classKey, transMap] of Object.entries(this.classTranslations)) {
            if (rawClass.toLowerCase().includes(classKey.toLowerCase()) ||
                lowerName.includes(classKey.toLowerCase()) ||
                lowerGeneric.includes(classKey.toLowerCase())) {
                matchedClassKey = classKey;
                translatedClass = transMap[lang] || transMap['en'];
                break;
            }
        }

        // Additional heuristic matching for drug classes
        if (!matchedClassKey) {
            if (/pain|analgesic|fever|antipyretic/i.test(rawClass)) matchedClassKey = 'Analgesic / Antipyretic';
            else if (/antibiotic|antibacterial|penicillin|cillin|mycin|floxacin/i.test(rawClass) || /cillin|mycin|floxacin/i.test(lowerName)) matchedClassKey = 'Antibiotic';
            else if (/hypertension|blood pressure|antihypertensive|pril|sartan|olol/i.test(rawClass) || /pril|sartan|olol/i.test(lowerName)) matchedClassKey = 'Antihypertensive';
            else if (/diabetic|glucose|insulin|glitazone|gliptin/i.test(rawClass) || /formin|glip|glim/i.test(lowerName)) matchedClassKey = 'Antidiabetic';
            else if (/statin|cholesterol|lipid/i.test(rawClass) || /statin/i.test(lowerName)) matchedClassKey = 'HMG-CoA Reductase Inhibitor';
            else if (/pump|prazole|acid|ulcer|gerd/i.test(rawClass) || /prazole/i.test(lowerName)) matchedClassKey = 'Proton Pump Inhibitor';
            else if (/allergy|histamine|rhinitis|urticaria/i.test(rawClass) || /adine|irizine/i.test(lowerName)) matchedClassKey = 'Antihistamine';
            else if (/respiratory|asthma|bronch/i.test(rawClass) || /terol/i.test(lowerName)) matchedClassKey = 'Bronchodilator';
            else if (/steroid|cortis|dexameth|prednis/i.test(rawClass) || /sone|onide/i.test(lowerName)) matchedClassKey = 'Corticosteroid';
            else if (/diuretic|water pill/i.test(rawClass) || /semide|thiazide/i.test(lowerName)) matchedClassKey = 'Diuretic';
            
            if (matchedClassKey && this.classTranslations[matchedClassKey]) {
                translatedClass = this.classTranslations[matchedClassKey][lang] || this.classTranslations[matchedClassKey]['en'];
            }
        }

        const fallbackData = this.classFallbacks[matchedClassKey] || this.classFallbacks['General'];
        const displayGeneric = generic ? `${genericPrefix}: ${generic}` : (dict.med_repo_standard_formulation || 'Standard Formulation');

        // 3. Humanize and Translate Indications (Therapeutic Uses)
        let displayUses = '';
        if (lang === 'en') {
            if (rawUses && rawUses !== 'Clinical monograph details available upon inspection.') {
                displayUses = this.cleanRawEnglish(rawUses, 240);
            } else {
                displayUses = fallbackData.uses['en'];
            }
        } else {
            // For non-English languages, provide high-quality localized AI humanized summary
            displayUses = fallbackData.uses[lang] || fallbackData.uses['en'];
        }

        // 4. Humanize and Translate Adverse Effects & Precautions
        let displaySideEffects = '';
        if (lang === 'en') {
            if (rawSides && rawSides.length > 5) {
                displaySideEffects = this.cleanRawEnglish(rawSides, 220);
            } else {
                displaySideEffects = fallbackData.sides['en'];
            }
        } else {
            // For non-English languages, provide clear translated adverse reaction precautions
            displaySideEffects = fallbackData.sides[lang] || fallbackData.sides['en'];
        }

        return {
            ...drug,
            name: name,
            displayClass: translatedClass,
            displayGeneric,
            displayUses,
            displaySideEffects,
            displayWarnings: rawWarnings || fallbackData.sides[lang] || fallbackData.sides['en'],
            displayDosage: rawDosage,
            displayInteractions: rawInteractions,
            contextLang: lang
        };
    }
};

/**
 * Core I18n Manager
 */
window.PharmasisI18n = {
    languages: PHARMASIS_LANGUAGES,
    translations: PHARMASIS_TRANSLATIONS,
    humanizer: PHARMASIS_DRUG_HUMANIZER,
    currentLang: 'en',
    isChanging: false,

    init() {
        const saved = localStorage.getItem('pharmasis_lang');
        const validCodes = PHARMASIS_LANGUAGES.map(l => l.code);
        
        if (saved && validCodes.includes(saved)) {
            this.currentLang = saved;
        } else {
            // Default directly to the user device's language if matching
            const browserLang = (navigator.language || navigator.userLanguage || '').slice(0, 2).toLowerCase();
            if (validCodes.includes(browserLang)) {
                this.currentLang = browserLang;
            } else {
                this.currentLang = 'en';
            }
        }

        this.applyLanguage(this.currentLang, false);
    },

    getLanguage() {
        return this.currentLang;
    },

    getLangMeta(code = this.currentLang) {
        return PHARMASIS_LANGUAGES.find(l => l.code === code) || PHARMASIS_LANGUAGES[0];
    },

    t(key, fallback = '') {
        const dict = PHARMASIS_TRANSLATIONS[this.currentLang] || PHARMASIS_TRANSLATIONS['en'];
        return dict[key] || PHARMASIS_TRANSLATIONS['en'][key] || fallback || key;
    },

    humanizeDrug(drug, lang = this.currentLang) {
        return PHARMASIS_DRUG_HUMANIZER.humanize(drug, lang);
    },

    setLanguage(targetCode) {
        if (this.currentLang === targetCode || this.isChanging) return;
        const validCodes = PHARMASIS_LANGUAGES.map(l => l.code);
        if (!validCodes.includes(targetCode)) return;

        const fromLang = this.getLangMeta(this.currentLang);
        const toLang = this.getLangMeta(targetCode);

        this.isChanging = true;
        this.showPreloader(fromLang, toLang, () => {
            this.currentLang = targetCode;
            localStorage.setItem('pharmasis_lang', targetCode);
            this.applyLanguage(targetCode, true);
            this.isChanging = false;
        });
    },

    applyLanguage(langCode, isInteractive = false) {
        const dict = PHARMASIS_TRANSLATIONS[langCode] || PHARMASIS_TRANSLATIONS['en'];
        const meta = this.getLangMeta(langCode);

        document.documentElement.lang = langCode;
        if (meta.dir === 'rtl') {
            document.documentElement.setAttribute('dir', 'rtl');
        } else {
            document.documentElement.removeAttribute('dir');
        }

        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (dict[key]) {
                el.innerHTML = dict[key];
            }
        });

        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            if (dict[key]) {
                el.setAttribute('placeholder', dict[key]);
            }
        });

        document.querySelectorAll('[data-i18n-title]').forEach(el => {
            const key = el.getAttribute('data-i18n-title');
            if (dict[key]) {
                el.setAttribute('title', dict[key]);
            }
        });

        window.dispatchEvent(new CustomEvent('pharmasis:languageChanged', {
            detail: { lang: langCode, meta, isInteractive }
        }));
    },

    /**
     * Minimalist iOS-style Frosted Glass Preloader (~3.0s duration)
     */
    showPreloader(fromLang, toLang, onComplete) {
        const overlay = document.getElementById('pharmasis-lang-preloader');
        if (!overlay) {
            if (onComplete) onComplete();
            return;
        }

        // Dynamically update preloader text based on the target translation
        const textEl = document.getElementById('pharmasis-preloader-text');
        if (textEl) {
            const targetCode = (toLang && toLang.code) ? toLang.code : (typeof toLang === 'string' ? toLang : 'en');
            const dict = PHARMASIS_TRANSLATIONS[targetCode] || PHARMASIS_TRANSLATIONS['en'];
            textEl.textContent = dict.preloader_loading_text || 'Changing language...';
        }

        overlay.classList.remove('hidden');
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'auto';

        requestAnimationFrame(() => {
            overlay.style.transition = 'opacity 250ms ease-out';
            overlay.style.opacity = '1';
        });

        // Simple smooth ~3.0s preload cycle
        setTimeout(() => {
            if (onComplete) onComplete();

            setTimeout(() => {
                overlay.style.transition = 'opacity 300ms ease';
                overlay.style.opacity = '0';

                setTimeout(() => {
                    overlay.classList.add('hidden');
                    overlay.style.pointerEvents = 'none';
                }, 300);
            }, 200);
        }, 3000);
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.PharmasisI18n.init());
} else {
    window.PharmasisI18n.init();
}
