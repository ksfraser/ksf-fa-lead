<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\Service;

use Ksfraser\FrontAccounting\Lead\DTO\LeadDTO;
use Ksfraser\FrontAccounting\Lead\Repository\LeadRepository;
use ksfraser\RelatedTo\DTO\ContactResolvedDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * LeadService — use-case layer for lead resolution and display.
 *
 * Composes LeadRepository and builds resolved DTOs for RelatedTo display.
 * Lead is the owning module for lead data; consuming modules override by
 * registering their own LEAD_RESOLVE listener.
 *
 * @package Ksfraser\FrontAccounting\Lead\Service
 * @since   1.0.0
 * @BABOK Related: BR-LD-001
 */
final class LeadService
{
    /** @var LeadRepository */
    private $repo;

    /**
     * @BABOK Related: BR-LD-001
     */
    public function __construct(?LeadRepository $repo = null)
    {
        $this->repo = $repo ?? new LeadRepository();
    }

    /**
     * @BABOK Related: BR-LD-001
     */
    public function resolveById(int $leadId, ?RelatedRecordDto $dto = null): array
    {
        $row = $this->repo->findById($leadId);
        if ($row === null) {
            return array(
                'dto' => new ContactResolvedDto(
                    $dto ?? new RelatedRecordDto('lead', '0', 'lead', '0', null),
                    null, null, null
                ),
                'fields' => $this->fieldMap(),
            );
        }

        $lead = LeadDTO::fromArray($row);
        $dto = $dto ?? new RelatedRecordDto(
            'lead',
            (string) $lead->id,
            'lead',
            (string) $lead->id,
            null
        );

        // Build a resolved display array with field name => value
        $fields = array(
            'leadSource' => $lead->leadSource,
            'leadStatus' => $lead->leadStatus,
            'rating'     => $lead->rating,
            'company'    => $lead->industry, // industry as company context
            'contact'    => $lead->assignedTo,
        );

        $labels = array(
            'leadSource' => _('Lead Source'),
            'leadStatus' => _('Lead Status'),
            'rating'     => _('Rating'),
            'company'    => _('Company / Industry'),
            'contact'    => _('Assigned To'),
        );

        return array(
            'dto'    => $dto,
            'fields' => $fields,
            'labels' => $labels,
            'url'    => '/modules/ksf_FA_Lead/pages/lead_form.php?action=edit&lead_id=' . (int) $lead->id,
            'label'  => $lead->getDisplayLabel(),
        );
    }

    /**
     * @BABOK Related: BR-LD-003
     */
    public function listAll(): array
    {
        $rows = $this->repo->findAll();
        $results = array();
        foreach ($rows as $row) {
            $lead = LeadDTO::fromArray($row);
            $results[] = array(
                'id'    => $lead->id,
                'dto'   => new RelatedRecordDto(
                    'lead',
                    (string) $lead->id,
                    'lead',
                    (string) $lead->id,
                    null
                ),
                'fields' => array(
                    'leadSource' => $lead->leadSource,
                    'leadStatus' => $lead->leadStatus,
                    'rating'     => $lead->rating,
                    'company'    => $lead->industry,
                    'contact'    => $lead->assignedTo,
                ),
                'labels' => array(
                    'leadSource' => _('Lead Source'),
                    'leadStatus' => _('Lead Status'),
                    'rating'     => _('Rating'),
                    'company'    => _('Company / Industry'),
                    'contact'    => _('Assigned To'),
                ),
                'url'    => '/modules/ksf_FA_Lead/pages/lead_form.php?action=edit&lead_id=' . $lead->id,
                'label'  => $lead->getDisplayLabel(),
            );
        }
        return $results;
    }

    /**
     * @BABOK Related: BR-LD-003
     */
    public function fieldMap(): array
    {
        return array(
            'leadSource' => _('Lead Source'),
            'leadStatus' => _('Lead Status'),
            'rating'     => _('Rating'),
            'company'    => _('Company / Industry'),
            'contact'    => _('Assigned To'),
        );
    }
}
