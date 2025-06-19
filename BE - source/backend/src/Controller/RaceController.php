<?php

namespace App\Controller;

use App\Dto\UserRaceDto;
use App\Entity\Race;
use App\Entity\User;
use App\Entity\UserRace;
use App\Repository\UserRaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/race')]
final class RaceController extends AbstractController
{
    #[Route('/all', name: 'all_race', methods: [Request::METHOD_GET])]
    public function allRaces(
        EntityManagerInterface $em,
    ): JsonResponse {

        $races = $em->getRepository(Race::class)->findAll();

        $raceData = array_map(function (Race $r) {

            return [
                'id'                => $r->getId(),
                'place'             => $r->getPlace(),
                'date'              => $r->getDate(),
                'COEFF'             => $r->getCOEFF(),
                'maxPartecipanti'   => $r->getMaxPartecipanti()
            ];
        }, $races);

        return $this->json([
            'message' => 'Races data',
            'path' => 'src/Controller/RaceController.php',
            'races' => $raceData
        ]);
    }

    #[Route('/{id}', name: 'single_race', methods: [Request::METHOD_GET])]
    public function singleRace(
        EntityManagerInterface $em,
        #[MapEntity(mapping: ['id' => 'id'])]
        Race $race
    ): JsonResponse {

        $race = $em->getRepository(Race::class)->find($race->getId());

        if (!$race) {
            throw $this->createNotFoundException('Race not found');
        }

        return $this->json([
            'message' => 'Race data',
            'path' => 'src/Controller/RaceController.php',
            'data' => [
                'id'            => $race->getId(),
                'place'         => $race->getPlace(),
                'date'          => $race->getDate(),
                'COEFF'         => $race->getCOEFF(),
                'maxPartecipanti'   => $race->getMaxPartecipanti()
            ]
        ]);
    }
}
