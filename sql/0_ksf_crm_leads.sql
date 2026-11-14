-- ksf_FA_Lead: lead pipeline table
--
-- Spun off from CRM Leads tab (0_ksf_crm_leads).
-- Table name uses bare 0_ prefix; db_import() substitutes TB_PREF at install.

CREATE TABLE IF NOT EXISTS `0_ksf_crm_leads` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `lead_source`   VARCHAR(50)  NOT NULL DEFAULT '',
  `lead_status`   VARCHAR(30)  NOT NULL DEFAULT 'new',
  `rating`        VARCHAR(10)  NOT NULL DEFAULT '',
  `annual_revenue` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `employee_count` INT(11)     NOT NULL DEFAULT 0,
  `industry`      VARCHAR(50)  NOT NULL DEFAULT '',
  `website`       VARCHAR(255) NOT NULL DEFAULT '',
  `phone`         VARCHAR(20)  NOT NULL DEFAULT '',
  `email`         VARCHAR(100) NOT NULL DEFAULT '',
  `address`       TEXT,
  `assigned_to`   VARCHAR(100) NOT NULL DEFAULT '',
  `campaign_id`   INT(11)      NOT NULL DEFAULT 0,
  `notes`         TEXT,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lead_status` (`lead_status`),
  KEY `idx_rating` (`rating`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
