<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\Repository;

use Ksfraser\FrontAccounting\Lead\DTO\LeadDTO;

/**
 * LeadRepository — SRP access to 0_ksf_crm_leads.
 *
 * Uses FA's native db_* calls only (no PDO, no raw mysqli).
 *
 * @package Ksfraser\FrontAccounting\Lead\Repository
 * @since   1.0.0
 */
final class LeadRepository
{
    private string $table = 'ksf_crm_leads';

    public function findAll(): array
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return array();
        }

        $sql = "SELECT * FROM " . TB_PREF . $this->table
            . " ORDER BY created_at DESC, id DESC";
        $result = db_query($sql, 'Could not load leads');
        if ($result === false) {
            return array();
        }

        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function findById(int $id): ?array
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return null;
        }

        $sql = "SELECT * FROM " . TB_PREF . $this->table
            . " WHERE id = " . (int) $id;
        $result = db_query($sql, 'Could not load lead');
        if ($result === false) {
            return null;
        }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }

    public function save(array $data): int
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return 0;
        }

        $sql = "INSERT INTO " . TB_PREF . $this->table . " (
                lead_source, lead_status, rating, annual_revenue,
                employee_count, industry, website, phone, email, address,
                assigned_to, campaign_id, notes)
            VALUES ("
            . $this->escape($data['lead_source'] ?? '') . ", "
            . $this->escape($data['lead_status'] ?? 'new') . ", "
            . $this->escape($data['rating'] ?? '') . ", "
            . $this->escape($data['annual_revenue'] ?? '0') . ", "
            . $this->intVal($data['employee_count'] ?? 0) . ", "
            . $this->escape($data['industry'] ?? '') . ", "
            . $this->escape($data['website'] ?? '') . ", "
            . $this->escape($data['phone'] ?? '') . ", "
            . $this->escape($data['email'] ?? '') . ", "
            . $this->escape($data['address'] ?? '') . ", "
            . $this->escape($data['assigned_to'] ?? '') . ", "
            . $this->intVal($data['campaign_id'] ?? 0) . ", "
            . $this->escape($data['notes'] ?? '') . ")";
        db_query($sql, 'Could not save lead');
        return db_insert_id();
    }

    public function update(int $id, array $data): void
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return;
        }

        $sets = [];
        foreach (['lead_source', 'lead_status', 'rating', 'annual_revenue',
                  'employee_count', 'industry', 'website', 'phone', 'email', 'address',
                  'assigned_to', 'notes'] as $col) {
            if (array_key_exists($col, $data)) {
                $val = ($col === 'employee_count') ? $this->intVal($data[$col])
                    : $this->escape($data[$col]);
                $sets[] = "$col = $val";
            }
        }
        if (array_key_exists('campaign_id', $data)) {
            $sets[] = "campaign_id = " . $this->intVal($data['campaign_id']);
        }
        if (empty($sets)) {
            return;
        }
        $sql = "UPDATE " . TB_PREF . $this->table
            . " SET " . implode(', ', $sets)
            . " WHERE id = " . (int) $id;
        db_query($sql, 'Could not update lead');
    }

    public function delete(int $id): void
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return;
        }
        $sql = "DELETE FROM " . TB_PREF . $this->table
            . " WHERE id = " . (int) $id;
        db_query($sql, 'Could not delete lead');
    }

    private function escape($value): string
    {
        if (function_exists('db_escape')) {
            return db_escape($value);
        }
        return addslashes((string) $value);
    }

    private function intVal($value): int
    {
        return (int) $value;
    }
}
