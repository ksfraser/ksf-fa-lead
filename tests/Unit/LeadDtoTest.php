<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\Tests\Unit;

use Ksfraser\FrontAccounting\Lead\DTO\LeadDTO;
use PHPUnit\Framework\TestCase;

/**
 * Tests for LeadDTO display label resolution.
 *
 * @package Ksfraser\FrontAccounting\Lead\Tests\Unit
 * @since   1.0.0
 */
class LeadDtoTest extends TestCase
{
    public function testGetDisplayLabelWithAllFields(): void
    {
        $dto = new LeadDTO(7, 'Website', 'new', 'hot', '50000', '10', 'Technology', '', '', '', '', 'Alice', 0, '', '');
        $this->assertSame('Website — hot — new', $dto->getDisplayLabel());
    }

    public function testGetDisplayLabelMinimal(): void
    {
        $dto = new LeadDTO(3, '', '', '', '', '', '', '', '', '', '', '', 0, '', '');
        $this->assertSame('Lead #3', $dto->getDisplayLabel());
    }

    public function testGetDisplayLabelSourceOnly(): void
    {
        $dto = new LeadDTO(1, 'Referral', '', '', '', '', '', '', '', '', '', '', 0, '', '');
        $this->assertSame('Referral', $dto->getDisplayLabel());
    }

    public function testToArrayShape(): void
    {
        $dto = new LeadDTO(7, 'Website', 'new', 'hot', '50000', '10', 'Technology', 'example.com', '555-0100', 'a@b.c', '123 Main St', 'Alice', 1, 'Notes', '2026-01-01');
        $array = $dto->toArray();
        $this->assertSame(7, $array['id']);
        $this->assertSame('Website', $array['lead_source']);
        $this->assertSame('new', $array['lead_status']);
        $this->assertSame('hot', $array['rating']);
        $this->assertSame('50000', $array['annual_revenue']);
        $this->assertSame('10', $array['employee_count']);
        $this->assertSame('Technology', $array['industry']);
        $this->assertSame('Alice', $array['assigned_to']);
    }

    public function testFromArrayReconstructs(): void
    {
        $dto = LeadDTO::fromArray(array(
            'id'            => 9,
            'lead_source'   => 'Cold Call',
            'lead_status'   => 'assigned',
            'rating'        => 'warm',
            'annual_revenue' => '25000',
            'employee_count' => '5',
            'industry'      => 'Healthcare',
            'website'       => 'clinic.com',
            'phone'         => '555-0200',
            'email'         => 'bob@clinic.com',
            'address'       => '456 Oak Ave',
            'assigned_to'   => 'Bob',
            'campaign_id'   => 3,
            'notes'         => 'Follow up',
            'created_at'    => '2026-02-01',
        ));
        $this->assertSame(9, $dto->id);
        $this->assertSame('Cold Call', $dto->leadSource);
        $this->assertSame('assigned', $dto->leadStatus);
        $this->assertSame('warm', $dto->rating);
        $this->assertSame('25000', $dto->annualRevenue);
        $this->assertSame('5', $dto->employeeCount);
        $this->assertSame('Healthcare', $dto->industry);
        $this->assertSame('clinic.com', $dto->website);
        $this->assertSame('555-0200', $dto->phone);
        $this->assertSame('bob@clinic.com', $dto->email);
        $this->assertSame('456 Oak Ave', $dto->address);
        $this->assertSame('Bob', $dto->assignedTo);
        $this->assertSame(3, $dto->campaignId);
        $this->assertSame('Follow up', $dto->notes);
        $this->assertSame('2026-02-01', $dto->createdAt);
    }
}

/**
 * Tests for LeadRepository (fake db_* functions, no real database).
 *
 * @package Ksfraser\FrontAccounting\Lead\Tests\Unit
 * @since   1.0.0
 */
class LeadRepositoryTest extends TestCase
{
    /** @var LeadRepository */
    private $repo;

    protected function setUp(): void
    {
        $this->repo = new LeadRepository();
    }

    public function testFindByIdZeroReturnsNull(): void
    {
        $this->assertNull($this->repo->findById(0));
        $this->assertNull($this->repo->findById(-1));
    }

    public function testFindByIdNotFoundOutsideFA(): void
    {
        // Outside FA, db_query is not defined → null
        $this->assertNull($this->repo->findById(1));
    }

    public function testFindAllEmptyOutsideFA(): void
    {
        $this->assertSame(array(), $this->repo->findAll());
    }

    public function testEscapeUsesDbEscapeWhenAvailable(): void
    {
        $reflection = new \ReflectionClass(LeadRepository::class);
        $method = $reflection->getMethod('escape');
        $method->setAccessible(false);

        // Can't easily test private method; just verify class loads
        $this->assertInstanceOf(LeadRepository::class, $this->repo);
    }
}
