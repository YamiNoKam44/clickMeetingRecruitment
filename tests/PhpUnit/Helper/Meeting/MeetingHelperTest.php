<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\Helper\Meeting;


use App\Helper\Meeting\MeetingHelper;
use PHPUnit\Framework\TestCase;

class MeetingHelperTest extends TestCase
{
    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(MeetingHelper::class, new MeetingHelper());
    }

    public function testResultEmptyFilterCollection(): void
    {
        $filterList = [MeetingHelper::ORDER_BY_PARAMETER_NAME => 'test', MeetingHelper::ORDER_DIRECTION_PARAMETER_NAME => 'test'];
        $filterCollection = MeetingHelper::createFilterArrayCollection($filterList);

        $this->assertTrue($filterCollection->isEmpty());
    }

    public function testResultNotEmptyFilterCollection(): void
    {
        $filterList = ['test1' => 'test', 'test2' => 'test'];
        $filterCollection = MeetingHelper::createFilterArrayCollection($filterList);

        $this->assertFalse($filterCollection->isEmpty());
        $this->assertEquals(2, $filterCollection->count());

        $currentFilter = $filterCollection->get('test1');
        $this->assertEquals('test', $currentFilter);

        $currentFilter = $filterCollection->get('test2');
        $this->assertEquals('test', $currentFilter);
    }
}
