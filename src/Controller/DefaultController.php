<?php

namespace App\Controller;

use App\Factory\Meeting\MeetingFactory;
use App\Repository\MeetingRepository;
use App\Service\Meeting\RateService;
use App\ValueObject\Meeting\MeetingRate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController
{
    private MeetingRepository $meetingRepository;
    private RateService $rateService;

    public function __construct(MeetingRepository $meetingRepository, RateService $rateService)
    {
        $this->meetingRepository = $meetingRepository;
        $this->rateService = $rateService;
    }

    #[Route('/meetings/{meetingId}', name: 'meeting')]
    public function meeting(string $meetingId): Response
    {
        $meeting = $this->meetingRepository->get($meetingId);

        return new JsonResponse(MeetingFactory::createWithStatus($meeting));
    }

    #[Route('/meetings/{meetingId}/rate', name: 'meeting-rate', methods: [Request::METHOD_POST])]
    public function rate(string $meetingId, Request $request): Response
    {
        $meeting = $this->meetingRepository->getWithParticipant($meetingId);
        $userId = $request->query->get('userId');
        $rate = $request->query->get('rate');

        $meetingRate = new MeetingRate($meeting, $userId, $rate);
        $meetingRate->validate();

        $response = $this->rateService->setRate($meetingRate);

        $status = $response->isAdded ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $response->message], $status);

    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return new Response('<h1>Hello</h1>');
    }
}
