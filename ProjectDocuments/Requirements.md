# Business Requirements — ksf_FA_Lead

**Package:** `ksfraser/ksf-fa-lead`
**Version:** `1.0.0`
**Namespace:** `Ksfraser\FrontAccounting\Lead\*`
**Module sequence:** LD (Lead)

---

## BR-LD-001 — Lead resolution ownership

**Status:** IMPLEMENTED

The system SHALL provide `ksf_FA_Lead` as the owning module for lead/pipeline
data spun off from CRM Leads tab. CRM defers to Lead for lead display rather
than owning lead info itself.

1. Lead SHALL own the `lead` type in the RelatedTo registry.
2. Lead SHALL answer `RELATED_TO_FIELDS` for the `lead` type with the field map.
3. Lead SHALL answer `LEAD_RESOLVE` by building resolved DTOs from `0_ksf_crm_leads` data.
4. Consuming modules MAY override by registering their own `LEAD_RESOLVE` listener.

---

## BR-LD-002 — Lead display default

**Status:** IMPLEMENTED

The lead display default SHALL show Lead Source — Rating — Status.

1. `LeadDTO::getDisplayLabel()` SHALL return non-empty fields joined with ` — `.
2. When all display fields are empty, return `Lead #<id>`.
3. All values SHALL be HTML-escaped at render time.

---

## BR-LD-003 — Resolved DTO for RelatedTo

**Status:** IMPLEMENTED

Lead SHALL build resolved display data (DTO + fields + labels + url) for RelatedTo display.

1. Resolved data wraps a `RelatedRecordDto` with resolved fields,
   overridable labels, and a URL.
2. `fieldMap()` returns the field name => display label mapping.
3. `listAll()` returns resolved lead data for summary table display.

---

## BR-LD-004 — Lead pipeline fields

**Status:** IMPLEMENTED

Lead SHALL store pipeline fields: source, status, rating, annual revenue,
employee count, industry, website, phone, email, address, assigned to,
campaign, notes.

1. All fields stored in `0_ksf_crm_leads`.
2. Default status is `new`.
3. Default rating is empty (unrated).
