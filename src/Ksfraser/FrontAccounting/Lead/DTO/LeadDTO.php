<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\DTO;

/**
 * Lead — immutable value object wrapping 0_ksf_crm_leads data.
 *
 * @package Ksfraser\FrontAccounting\Lead\DTO
 * @since   1.0.0
 */
final class LeadDTO
{
    public $id;
    public $leadSource;
    public $leadStatus;
    public $rating;
    public $annualRevenue;
    public $employeeCount;
    public $industry;
    public $website;
    public $phone;
    public $email;
    public $address;
    public $assignedTo;
    public $campaignId;
    public $notes;
    public $createdAt;

    public function __construct(
        int $id,
        string $leadSource = '',
        string $leadStatus = 'new',
        string $rating = '',
        string $annualRevenue = '0',
        string $employeeCount = '0',
        string $industry = '',
        string $website = '',
        string $phone = '',
        string $email = '',
        string $address = '',
        string $assignedTo = '',
        int $campaignId = 0,
        string $notes = '',
        string $createdAt = ''
    ) {
        $this->id           = $id;
        $this->leadSource   = $leadSource;
        $this->leadStatus   = $leadStatus;
        $this->rating       = $rating;
        $this->annualRevenue= $annualRevenue;
        $this->employeeCount= $employeeCount;
        $this->industry     = $industry;
        $this->website      = $website;
        $this->phone        = $phone;
        $this->email        = $email;
        $this->address      = $address;
        $this->assignedTo   = $assignedTo;
        $this->campaignId   = $campaignId;
        $this->notes        = $notes;
        $this->createdAt    = $createdAt;
    }

    public function getDisplayLabel(): string
    {
        $parts = array();
        if ($this->leadSource !== '') {
            $parts[] = $this->leadSource;
        }
        if ($this->rating !== '') {
            $parts[] = $this->rating;
        }
        if ($this->leadStatus !== '') {
            $parts[] = $this->leadStatus;
        }
        if ($parts === []) {
            return _('Lead #') . (string) $this->id;
        }
        return implode(' — ', $parts);
    }

    public function toArray(): array
    {
        return array(
            'id'            => $this->id,
            'lead_source'   => $this->leadSource,
            'lead_status'   => $this->leadStatus,
            'rating'        => $this->rating,
            'annual_revenue'=> $this->annualRevenue,
            'employee_count'=> $this->employeeCount,
            'industry'      => $this->industry,
            'website'       => $this->website,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'address'       => $this->address,
            'assigned_to'   => $this->assignedTo,
            'campaign_id'   => $this->campaignId,
            'notes'         => $this->notes,
            'created_at'    => $this->createdAt,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['lead_source'] ?? ''),
            (string) ($data['lead_status'] ?? 'new'),
            (string) ($data['rating'] ?? ''),
            (string) ($data['annual_revenue'] ?? '0'),
            (string) ($data['employee_count'] ?? '0'),
            (string) ($data['industry'] ?? ''),
            (string) ($data['website'] ?? ''),
            (string) ($data['phone'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['address'] ?? ''),
            (string) ($data['assigned_to'] ?? ''),
            (int) ($data['campaign_id'] ?? 0),
            (string) ($data['notes'] ?? ''),
            (string) ($data['created_at'] ?? '')
        );
    }
}
