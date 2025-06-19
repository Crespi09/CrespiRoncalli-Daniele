<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api' )]
final class DefaultController extends AbstractController
{
    #[Route('/default', name: 'app_default', methods: [Request::METHOD_POST])]
    public function index(#[MapRequestPayload()] UserDto $dto): JsonResponse
    {
        return $this->json([
            'message' => 'test',
            'path' => 'src/Controller/DefaultController.php',
        ]);
    }
}
