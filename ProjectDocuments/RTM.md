# RTM — ksf_FA_Lead

**Module:** `ksf_FA_Lead`
**Version:** `1.0.0`
**Requirement definitions:** `ProjectDocuments/Requirements.md`

| BR | Requirement | Implementation | Tests |
|----|-------------|----------------|-------|
| BR-LD-001 | Lead resolution ownership — owns `lead` type, answers RELATED_TO_FIELDS + LEAD_RESOLVE | `hooks.php` (`hooks_ksf_FA_Lead::RELATED_TO_FIELDS`, `::LEAD_RESOLVE`, `::LEAD_FIELDS`), `LeadService::resolveById` | (FA runtime only — hooks) |
| BR-LD-002 | Lead display default — Source — Rating — Status, fallback `Lead #<id>` | `LeadDTO::getDisplayLabel()` | `LeadDtoTest` |
| BR-LD-003 | Resolved DTO for RelatedTo — field map + listAll summary data | `LeadService::fieldMap()`, `LeadService::listAll()`, `hooks.php` `RELATED_TO_FIELDS` | (via related-to-core) |
| BR-LD-004 | Lead pipeline fields — source, status, rating, revenue, employees, industry, … stored in `0_ksf_crm_leads` | `LeadRepository` (`save`, `update`, `findAll`, `findById`), `LeadDTO` | `LeadRepositoryTest` |
