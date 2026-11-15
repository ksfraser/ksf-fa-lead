<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\Tests\Unit;

use Ksfraser\FrontAccounting\Lead\Repository\LeadRepository;
use Ksfraser\FrontAccounting\Lead\Service\LeadService;
use ksfraser\RelatedTo\DTO\ContactResolvedDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use PHPUnit\Framework\TestCase;

/**
 * Tests for LeadService. The repository is the real LeadRepository backed by
 * stubbed FA db_* functions, so resolveById/listAll can be driven deterministically.
 *
 * @package Ksfraser\FrontAccounting\Lead\Tests\Unit
 * @since   1.0.0
 */
class LeadServiceTest extends TestCase
{
    /** @var LeadService */
    private $service;

    protected function setUp(): void
    {
        $this->service = new LeadService(new LeadRepository());
        $GLOBALS['KSF_DB_RESULT']   = false;
        $GLOBALS['KSF_DB_ROWS']     = array();
        $GLOBALS['KSF_DB_INSERT_ID'] = 1;
    }

    private function leadRow(array $overrides = array()): array
    {
        return array_merge(array(
            'id'            => 1,
            'lead_source'   => 'Web',
            'lead_status'   => 'new',
            'rating'        => 'hot',
            'annual_revenue'=> '0',
            'employee_count'=> '0',
            'industry'      => 'Acme',
            'website'       => '',
            'phone'         => '',
            'email'         => '',
            'address'       => '',
            'assigned_to'   => 'jsmith',
            'campaign_id'   => '0',
            'notes'         => '',
            'created_at'    => '',
        ), $overrides);
    }

    public function testResolveByIdNotFoundReturnsEmptyStructure(): void
    {
        // db_query stubbed to return false -> repository finds nothing
        $result = $this->service->resolveById(999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('dto', $result);
        $this->assertArrayHasKey('fields', $result);
        // Not-found wraps the empty RelatedRecordDto in a ContactResolvedDto.
        $this->assertInstanceOf(ContactResolvedDto::class, $result['dto']);
        $this->assertSame('lead', $result['dto']->dto->lhsType);
        $this->assertSame('0', $result['dto']->dto->lhsId);
        $this->assertSame(array(
            'leadSource' => 'Lead Source',
            'leadStatus' => 'Lead Status',
            'rating'     => 'Rating',
            'company'    => 'Company / Industry',
            'contact'    => 'Assigned To',
        ), $result['fields']);
    }

    public function testResolveByIdFoundReturnsPopulatedStructure(): void
    {
        $GLOBALS['KSF_DB_RESULT'] = true;
        $GLOBALS['KSF_DB_ROWS']   = array($this->leadRow(array(
            'lead_source'  => 'Web',
            'lead_status'  => 'qualified',
            'rating'       => 'hot',
            'industry'     => 'Acme Inc',
            'assigned_to'  => 'jsmith',
        )));

        $result = $this->service->resolveById(1);

        $this->assertInstanceOf(RelatedRecordDto::class, $result['dto']);
        $this->assertSame('1', $result['dto']->lhsId);
        $this->assertSame('Web', $result['fields']['leadSource']);
        $this->assertSame('qualified', $result['fields']['leadStatus']);
        $this->assertSame('hot', $result['fields']['rating']);
        $this->assertSame('Acme Inc', $result['fields']['company']);
        $this->assertSame('jsmith', $result['fields']['contact']);
        $this->assertSame('lead', $result['dto']->lhsType);
        $this->assertStringContainsString('lead_id=1', $result['url']);
    }

    public function testListAllReturnsEmptyOutsideFA(): void
    {
        // db_query stubbed to return false -> repository finds nothing
        $this->assertSame(array(), $this->service->listAll());
    }

    public function testListAllReturnsRows(): void
    {
        $GLOBALS['KSF_DB_RESULT'] = true;
        $GLOBALS['KSF_DB_ROWS']   = array(
            $this->leadRow(array('id' => 1, 'lead_source' => 'Web')),
            $this->leadRow(array('id' => 2, 'lead_source' => 'Phone')),
        );

        $results = $this->service->listAll();

        $this->assertCount(2, $results);
        $this->assertSame('1', $results[0]['dto']->lhsId);
        $this->assertSame('Web', $results[0]['fields']['leadSource']);
        $this->assertSame('2', $results[1]['dto']->lhsId);
        $this->assertSame('Phone', $results[1]['fields']['leadSource']);
    }

    public function testFieldMapReturnsExpectedLabels(): void
    {
        $this->assertSame(array(
            'leadSource' => 'Lead Source',
            'leadStatus' => 'Lead Status',
            'rating'     => 'Rating',
            'company'    => 'Company / Industry',
            'contact'    => 'Assigned To',
        ), $this->service->fieldMap());
    }
}
