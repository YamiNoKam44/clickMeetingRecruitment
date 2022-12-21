<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\Service\Meeting;


use App\DTO\Meeting\ListDTO;
use App\Repository\MeetingRepository;
use App\Service\Meeting\MeetingService;
use App\ValueObject\Meeting\ListOrderAndFilters;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class MeetingServiceTest extends TestCase
{
    private MeetingRepository $meetingRepository;

    protected function setUp(): void
    {
        $this->meetingRepository = $this->createMock(MeetingRepository::class);
        parent::setUp();
    }

    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(MeetingService::class, new MeetingService($this->meetingRepository));
    }

    public function testFetchingListWithDtoObject()
    {
        $dateTimeNow = new \DateTimeImmutable();
        $this->meetingRepository->method('findAllWithFilers')->willReturn([['name' => 'test',
            'startTime' => $dateTimeNow, 'averageRate' => 4.567]]);

        $list = (new MeetingService($this->meetingRepository))->fetchList(
            new ListOrderAndFilters(new ArrayCollection(), 'test', 'test'));

        $this->assertFalse($list->isEmpty());

        $dto = $list->first();
        $this->assertInstanceOf(ListDTO::class, $dto);
        $this->assertEquals('test', $dto->name);
        $this->assertEquals($dateTimeNow, $dto->startTime);
        $this->assertEquals(4.57, $dto->averageRate);
    }
    public function testFetchingListWithEmptyList()
    {
        $this->meetingRepository->method('findAllWithFilers')->willReturn([]);

        $list = (new MeetingService($this->meetingRepository))->fetchList(
            new ListOrderAndFilters(new ArrayCollection(), 'test', 'test'));

        $this->assertTrue($list->isEmpty());
    }
}
