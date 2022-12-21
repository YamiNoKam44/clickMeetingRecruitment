<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\ValueObject\Meeting;


use App\Enum\Meeting\Filters;
use App\Enum\Meeting\OrderBy;
use App\Enum\Meeting\Status;
use App\Exception\Meeting\UnsupportedFilterFieldException;
use App\Exception\Meeting\UnsupportedOrderByFieldException;
use App\Exception\Meeting\UnsupportedOrderDirectionFieldException;
use App\Helper\Meeting\MeetingHelper;
use App\ValueObject\Meeting\ListOrderAndFilters;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class ListOrderAndFiltersTest extends TestCase
{
    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(ListOrderAndFilters::class, new ListOrderAndFilters(new ArrayCollection(), 'test', 'test'));
    }

    public function testValidateSuccessful() {
        $filters = new ArrayCollection();
        $filters->set(Filters::Status->value, Status::FULL->name);

        $listToValidate = new ListOrderAndFilters($filters, MeetingHelper::DEFAULT_ORDER_BY_PARAMETER, Criteria::DESC);

        $this->assertNull($listToValidate->validate());
    }

    public function testValidateThrowExceptionOnWrongFilter()
    {
        $filters = new ArrayCollection();
        $filters->set('key', 'value');

        $listToValidate = new ListOrderAndFilters($filters, 'test', 'test');

        $this->expectException(UnsupportedFilterFieldException::class);
        $this->expectExceptionMessage(sprintf('Unsupported filter, supported only: %s',
            implode(', ', Filters::allStatus())));
        $listToValidate->validate();
    }

    public function testValidateThrowExceptionOnWrongOrderBy()
    {
        $listToValidate = new ListOrderAndFilters(new ArrayCollection(), 'test', 'test');

        $this->expectException(UnsupportedOrderByFieldException::class);
        $this->expectExceptionMessage(sprintf('Unsupported OrderBy, supported only: %s',
            implode(', ', OrderBy::allStatus())));
        $listToValidate->validate();
    }

    public function testValidateThrowExceptionOnWrongOrderDirection()
    {
        $listToValidate = new ListOrderAndFilters(new ArrayCollection(), MeetingHelper::DEFAULT_ORDER_BY_PARAMETER, 'test');

        $this->expectException(UnsupportedOrderDirectionFieldException::class);
        $this->expectExceptionMessage(sprintf('Unsupported OrderDirection, supported only 
                "%s" and "%s"', Criteria::DESC, Criteria::ASC));
        $listToValidate->validate();
    }
}
