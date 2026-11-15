<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\Tests\Unit;

use ksfraser\RelatedTo\DTO\ContactResolvedDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../hooks.php';

/**
 * Tests for \hooks_ksf_FA_Lead responders (RELATED_TO_FIELDS, LEAD_RESOLVE,
 * LEAD_FIELDS). FA's hook_invoke_all() is stubbed by the bootstrap; tests can
 * install a handler via $GLOBALS['KSF_HOOK_HANDLER'].
 *
 * @package Ksfraser\FrontAccounting\Lead\Tests\Unit
 * @since   1.0.0
 */
class LeadHooksTest extends TestCase
{
    /** @var \hooks_ksf_FA_Lead */
    private $hooks;

    protected function setUp(): void
    {
        $this->hooks = new \hooks_ksf_FA_Lead();
        $GLOBALS['KSF_HOOK_HANDLER'] = null;
        $GLOBALS['KSF_DB_RESULT']   = false;
        $GLOBALS['KSF_DB_ROWS']     = array();
    }

    private function data(array $overrides = array()): array
    {
        return array_merge(array(
            'module' => 'ksf_FA_Opportunity',
            'type'   => 'lead',
            'fields' => array(),
        ), $overrides);
    }

    public function testRespondersDeclared(): void
    {
        $this->assertContains('RELATED_TO_FIELDS', \hooks_ksf_FA_Lead::RESPONDERS);
        $this->assertContains('LEAD_RESOLVE', \hooks_ksf_FA_Lead::RESPONDERS);
        $this->assertContains('LEAD_FIELDS', \hooks_ksf_FA_Lead::RESPONDERS);
    }

    public function testRelatedToFieldsSetsLeadFields(): void
    {
        $data = $this->data(array('type' => 'lead'));
        $this->hooks->RELATED_TO_FIELDS($data);

        $this->assertArrayHasKey('leadSource', $data['fields']);
        $this->assertArrayHasKey('leadStatus', $data['fields']);
        $this->assertArrayHasKey('rating', $data['fields']);
        $this->assertSame('Lead Source', $data['fields']['leadSource']);
        $this->assertSame('Rating', $data['fields']['rating']);
    }

    public function testRelatedToFieldsIgnoresNonLeadType(): void
    {
        $data = $this->data(array('type' => 'opportunity'));
        $this->hooks->RELATED_TO_FIELDS($data);

        $this->assertSame(array(), $data['fields']);
    }

    public function testRelatedToFieldsRequiresModuleAndType(): void
    {
        $data = array('fields' => array());
        $this->hooks->RELATED_TO_FIELDS($data);
        $this->assertArrayHasKey('error', $data);

        $data = array('module' => 'x', 'fields' => array());
        $this->hooks->RELATED_TO_FIELDS($data);
        $this->assertArrayHasKey('error', $data);
    }

    public function testLeadFieldsReturnsFieldMap(): void
    {
        $data = array();
        $this->hooks->LEAD_FIELDS($data);

        $this->assertSame(array(
            'leadSource' => 'Lead Source',
            'leadStatus' => 'Lead Status',
            'rating'     => 'Rating',
            'company'    => 'Company',
            'contact'    => 'Contact',
        ), $data['fields']);
    }

    public function testLeadResolveNotFoundSetsResult(): void
    {
        $dto = new RelatedRecordDto('lead', '3', 'lead', '3', null);
        $data = array('lead_id' => 3, 'dto' => $dto);

        $this->hooks->LEAD_RESOLVE($data);

        $this->assertArrayHasKey('result', $data);
        $result = $data['result'];
        $this->assertIsArray($result);
        $this->assertArrayHasKey('dto', $result);
        $this->assertArrayHasKey('fields', $result);
        // Not-found wraps the empty RelatedRecordDto in a ContactResolvedDto.
        $this->assertInstanceOf(ContactResolvedDto::class, $result['dto']);
        $this->assertSame('lead', $result['dto']->dto->lhsType);
        $this->assertSame('3', $result['dto']->dto->lhsId);
        $this->assertSame(array(
            'leadSource' => 'Lead Source',
            'leadStatus' => 'Lead Status',
            'rating'     => 'Rating',
            'company'    => 'Company / Industry',
            'contact'    => 'Assigned To',
        ), $result['fields']);
    }

    public function testLeadResolveFoundSetsPopulatedResult(): void
    {
        $GLOBALS['KSF_DB_RESULT'] = true;
        $GLOBALS['KSF_DB_ROWS']   = array(array(
            'id'            => 8,
            'lead_source'   => 'Web',
            'lead_status'   => 'qualified',
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
        ));

        $dto = new RelatedRecordDto('lead', '8', 'lead', '8', null);
        $data = array('lead_id' => 8, 'dto' => $dto);

        $this->hooks->LEAD_RESOLVE($data);

        $result = $data['result'];
        $this->assertSame('8', $result['dto']->lhsId);
        $this->assertSame('Web', $result['fields']['leadSource']);
        $this->assertSame('qualified', $result['fields']['leadStatus']);
        $this->assertSame('hot', $result['fields']['rating']);
        $this->assertSame('Acme', $result['fields']['company']);
        $this->assertSame('jsmith', $result['fields']['contact']);
        $this->assertStringContainsString('lead_id=8', $result['url']);
    }

    public function testLeadResolveRequiresLeadIdAndDto(): void
    {
        $data = array('dto' => new RelatedRecordDto('lead', '1', 'lead', '1', null));
        $this->hooks->LEAD_RESOLVE($data);
        $this->assertArrayHasKey('error', $data);

        $data = array('lead_id' => 1);
        $this->hooks->LEAD_RESOLVE($data);
        $this->assertArrayHasKey('error', $data);
    }
}
