-- =====================================================================
-- Pharmasis · Traffic & Feature-Usage Analytics  (PostgreSQL 12+)
--
-- HOW TO RUN
--   psql:    psql -U USER -d DATABASE -f database/analytics_schema.sql
--   pgAdmin: open this file in the Query Tool, then press F5 (Execute Script).
--            Do NOT select all text + run "Execute query" — pgAdmin will
--            wrap it in SELECT COUNT(*) FROM (...) and you'll get a
--            "syntax error at or near ;" error.
--
-- Purpose: row-level visibility into HOW users use MediCheck, AI Simplifier,
-- and AI Interaction — without storing prompt/response bodies.
--
-- All objects are created in the `masterdata` schema. Change the line
-- below if your schema is named differently.
-- =====================================================================

CREATE SCHEMA IF NOT EXISTS masterdata;
SET search_path TO masterdata;


-- ---------------------------------------------------------------------
-- 1. traffic_visits — one row per route hit.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS traffic_visits CASCADE;
CREATE TABLE traffic_visits (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT       NULL,            -- soft link to users.id (no FK so this is portable)
    session_id      CHAR(40)     NULL,
    visitor_uid     UUID         NULL,
    method          VARCHAR(8)   NOT NULL,
    route_name      VARCHAR(120) NULL,
    path            VARCHAR(255) NOT NULL,
    feature_area    VARCHAR(20)  NOT NULL DEFAULT 'other'
        CHECK (feature_area IN ('medicheck','ai_simplifier','ai_interaction','drugs','auth','marketing','other')),
    status_code     SMALLINT     NULL,
    duration_ms     INTEGER      NULL,
    referrer_host   VARCHAR(120) NULL,
    device_type     VARCHAR(10)  NOT NULL DEFAULT 'unknown'
        CHECK (device_type IN ('desktop','mobile','tablet','bot','unknown')),
    locale          VARCHAR(8)   NULL,
    ip_hash         CHAR(64)     NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_visits_user_time    ON traffic_visits (user_id, created_at);
CREATE INDEX idx_visits_session      ON traffic_visits (session_id);
CREATE INDEX idx_visits_feature_time ON traffic_visits (feature_area, created_at);
CREATE INDEX idx_visits_path         ON traffic_visits (path);


-- ---------------------------------------------------------------------
-- 2. medicheck_events — one row per MediCheck submission.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS medicheck_events CASCADE;
CREATE TABLE medicheck_events (
    id                       BIGSERIAL PRIMARY KEY,
    visit_id                 BIGINT      NULL REFERENCES traffic_visits(id) ON DELETE SET NULL,
    user_id                  BIGINT      NULL,
    action                   VARCHAR(20) NOT NULL
        CHECK (action IN ('analyze','nearby','history_list','history_view')),
    lang                     VARCHAR(8)  NULL,
    input_mode               VARCHAR(10) NOT NULL DEFAULT 'unknown'
        CHECK (input_mode IN ('text','audio','mixed','unknown')),
    symptoms_length          INTEGER     NULL,
    has_audio                BOOLEAN     NOT NULL DEFAULT FALSE,
    has_location             BOOLEAN     NOT NULL DEFAULT FALSE,
    age_provided             BOOLEAN     NOT NULL DEFAULT FALSE,
    weight_provided          BOOLEAN     NOT NULL DEFAULT FALSE,
    gender_provided          BOOLEAN     NOT NULL DEFAULT FALSE,
    allergies_count          SMALLINT    NOT NULL DEFAULT 0,
    conditions_count         SMALLINT    NOT NULL DEFAULT 0,
    medications_count        SMALLINT    NOT NULL DEFAULT 0,
    pipeline_steps_completed SMALLINT    NULL,
    providers_returned       SMALLINT    NULL,
    success                  BOOLEAN     NOT NULL DEFAULT TRUE,
    error_code               VARCHAR(64) NULL,
    duration_ms              INTEGER     NULL,
    created_at               TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_mc_user_time   ON medicheck_events (user_id, created_at);
CREATE INDEX idx_mc_action_time ON medicheck_events (action, created_at);
CREATE INDEX idx_mc_visit       ON medicheck_events (visit_id);


-- ---------------------------------------------------------------------
-- 3. ai_simplifier_events — one row per /api/v1/ai/simplify call.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS ai_simplifier_events CASCADE;
CREATE TABLE ai_simplifier_events (
    id            BIGSERIAL PRIMARY KEY,
    visit_id      BIGINT      NULL REFERENCES traffic_visits(id) ON DELETE SET NULL,
    user_id       BIGINT      NULL,
    drug_id       BIGINT      NULL,
    field         VARCHAR(20) NOT NULL
        CHECK (field IN ('uses','warnings','before_taking','dosage','side_effects','interactions')),
    language      VARCHAR(8)  NOT NULL DEFAULT 'en',
    input_length  INTEGER     NULL,
    output_length INTEGER     NULL,
    cache_hit     BOOLEAN     NOT NULL DEFAULT FALSE,
    success       BOOLEAN     NOT NULL DEFAULT TRUE,
    error_code    VARCHAR(64) NULL,
    duration_ms   INTEGER     NULL,
    created_at    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_simp_user_time  ON ai_simplifier_events (user_id, created_at);
CREATE INDEX idx_simp_field_time ON ai_simplifier_events (field, created_at);
CREATE INDEX idx_simp_drug       ON ai_simplifier_events (drug_id);
CREATE INDEX idx_simp_visit      ON ai_simplifier_events (visit_id);


-- ---------------------------------------------------------------------
-- 4. ai_interaction_events — one row per /api/v1/interactions/check call.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS ai_interaction_events CASCADE;
CREATE TABLE ai_interaction_events (
    id                 BIGSERIAL PRIMARY KEY,
    visit_id           BIGINT      NULL REFERENCES traffic_visits(id) ON DELETE SET NULL,
    user_id            BIGINT      NULL,
    drug_count         SMALLINT    NOT NULL,
    drug_ids_hash      CHAR(64)    NULL,
    language           VARCHAR(8)  NOT NULL DEFAULT 'en',
    severity_max       VARCHAR(20) NOT NULL DEFAULT 'unknown'
        CHECK (severity_max IN ('none','minor','moderate','major','contraindicated','unknown')),
    interactions_found SMALLINT    NULL,
    cache_hit          BOOLEAN     NOT NULL DEFAULT FALSE,
    success            BOOLEAN     NOT NULL DEFAULT TRUE,
    error_code         VARCHAR(64) NULL,
    duration_ms        INTEGER     NULL,
    created_at         TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_inter_user_time ON ai_interaction_events (user_id, created_at);
CREATE INDEX idx_inter_severity  ON ai_interaction_events (severity_max, created_at);
CREATE INDEX idx_inter_hash      ON ai_interaction_events (drug_ids_hash);
CREATE INDEX idx_inter_visit     ON ai_interaction_events (visit_id);


-- =====================================================================
-- Helper queries (uncomment to use):
--
-- 1) Top 20 most active users last 7 days:
--    SELECT user_id, COUNT(*) AS hits
--    FROM traffic_visits
--    WHERE created_at >= NOW() - INTERVAL '7 days' AND user_id IS NOT NULL
--    GROUP BY user_id ORDER BY hits DESC LIMIT 20;
--
-- 2) Funnel — visits → medicheck analyze → success (last 7d):
--    SELECT
--      (SELECT COUNT(*) FROM traffic_visits
--         WHERE feature_area='medicheck' AND created_at >= NOW() - INTERVAL '7 days') AS visits_mc,
--      (SELECT COUNT(*) FROM medicheck_events
--         WHERE action='analyze'           AND created_at >= NOW() - INTERVAL '7 days') AS analyses,
--      (SELECT COUNT(*) FROM medicheck_events
--         WHERE action='analyze' AND success AND created_at >= NOW() - INTERVAL '7 days') AS analyses_ok;
--
-- 3) Most simplified field per language:
--    SELECT language, field, COUNT(*) AS c
--    FROM ai_simplifier_events
--    GROUP BY language, field ORDER BY c DESC;
--
-- 4) Repeat interaction queries (same drug set):
--    SELECT drug_ids_hash, COUNT(*) AS c
--    FROM ai_interaction_events
--    GROUP BY drug_ids_hash HAVING COUNT(*) > 1 ORDER BY c DESC;
-- =====================================================================
