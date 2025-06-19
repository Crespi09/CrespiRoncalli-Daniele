<?php

namespace App\Controller;

use App\Dto\UpdateUserRaceDto;
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

#[Route('/api/userRace')]
final class UserRaceController extends AbstractController
{
    #[Route('', name: 'add_user_race', methods: [Request::METHOD_POST])]
    public function addUserRace(
        EntityManagerInterface $em,
        #[CurrentUser()] User $user,
        #[MapRequestPayload()] UserRaceDto $dto,
        UserRaceRepository $repo,
    ): JsonResponse {
        $race = $em->getRepository(Race::class)->find($dto->race_id);

        if (!$dto->name || !$dto->size || !$dto->total || !$race || !$user) {
            return $this->json([
                'message' => 'UserRace error',
                'path' => 'src/Controller/UserRaceController.php',
            ], 400);
        }

        $userRace = $repo->create(
            $dto->name,
            $dto->size,
            $dto->total,
            $dto->km,

            $race,
            $user
        );

        $em->persist($userRace);
        $em->flush();


        $userRaceData = [
            'id'            => $userRace->getId(),
            'user_id'       => $userRace->getUser()->getId(),
            'race_id'      => $userRace->getRace()->getId(),
            'name'         => $userRace->getName(),
            'size'        => $userRace->getSize(),
            'total'      => $userRace->getTotal(),
            'km'         => $userRace->getKm(),
        ];

        return $this->json([
            'message' => 'User Race Created',
            'path' => 'src/Controller/UserRaceController.php',
            'races' => $userRaceData
        ], Response::HTTP_CREATED);
    }


    #[Route('', name: 'get_user_race', methods: [Request::METHOD_GET])]
    public function getUserRace(
        EntityManagerInterface $em,
        #[CurrentUser()] User $user,
    ): JsonResponse {
        $races = $em->getRepository(UserRace::class)->findBy(['user' => $user]);


        $racesData = array_map(function (UserRace $ur) {

            $race = $ur->getRace();

            return [
                'user_id'       => $ur->getUser()->getId(),
                'race_id'       => $race ? $race->getId() : null,
                'userRace_id'   => $ur->getId(),
                'name'          => $ur->getName(),
                'size'          => $ur->getSize(),
                'total'         => $ur->getTotal(),
                'km'            => $ur->getKm(),
                'race_place'    => $race ? $race->getPlace() : null,
                'race_date'     => $race ? $race->getDate() : null,
                'COEFF'         => $race ? $race->getCOEFF() : null 
            ];
        }, $races);


        return $this->json([
            'message' => 'User Race',
            'path' => 'src/Controller/UserRaceController.php',
            'races' => $racesData
        ], 200);
    }


    #[Route('/{id}', name: 'delete_user_race', methods: [Request::METHOD_DELETE])]
    public function deleteUserRace(
        EntityManagerInterface $em,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserRace $ur
    ): JsonResponse {

        if (!$ur) {
            return $this->json([
                'message' => 'Delete Id not found',
                'path' => 'src/Controller/UserRaceController.php'
            ], 404);
        }

        $em->remove($ur);
        $em->flush();

        return $this->json([
            'message' => 'User Race deleted',
            'path' => 'src/Controller/UserRaceController.php'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'update_user_race', methods: [Request::METHOD_PUT])]
    public function updateUserRace(
        EntityManagerInterface $em,
        #[CurrentUser()] User $user,
        #[MapRequestPayload()] UpdateUserRaceDto $dto,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserRace $ur,
        UserRaceRepository $repo
    ): JsonResponse {
    
        if (!$ur) {
            return $this->json([
                'message' => 'Update Id not found',
                'path' => 'src/Controller/UserRaceController.php'
            ], 404);
        }
    
        $race = null;
        if ($dto->race_id !== null) {
            $race = $em->getRepository(Race::class)->find($dto->race_id);
            if (!$race) {
                return $this->json([
                    'message' => 'Race not found',
                    'path' => 'src/Controller/UserRaceController.php'
                ], 404);
            }
        }
    
        $updatedUserRace = $repo->update(
            $ur,
            $dto->name,
            $dto->size,
            $dto->total,
            $dto->km,
            $race
        );
    
        $em->persist($updatedUserRace);
        $em->flush();

    
        return $this->json([
            'message' => 'User Race updated',
            'path' => 'src/Controller/UserRaceController.php',
        ], 200);
    }
}
