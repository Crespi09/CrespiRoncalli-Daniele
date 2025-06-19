<?php

namespace App\Controller;

use App\Controller\RoleDto as ControllerRoleDto;
use App\Entity\Role;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/role' )]
final class RoleController extends AbstractController
{

    #[Route('', name: 'api_get_roles', methods: [Request::METHOD_GET])]
    public function getRoles(
        EntityManagerInterface $em,
        ): JsonResponse
    {

        $roles = $em->getRepository(Role::class)->findAll();

        if($roles === null) {
            return $this->json([
                'message' => 'Roles not found',
                'path' => 'src/Controller/RoleController.php',
            ]);        
        }
                

        // Converti le entità in array semplici
        $rolesToReturn = array_map(function(Role $role) {
            return [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'can_create' => $role->getCan_create(),
                'can_read' => $role->getCan_read(),
                'can_update' => $role->getCan_update(),
                'can_delete' => $role->getCan_delete(),
            ];
        }, $roles);

        return $this->json([
            'message' => 'Roles',
            'path' => 'src/Controller/RoleController.php',
            'roles' => $rolesToReturn
        ]);
    }



    #[Route('', name: 'api_add_role', methods: [Request::METHOD_POST])]
    public function addRole(
        EntityManagerInterface $em,
        #[MapRequestPayload()] ControllerRoleDto $dto
        ): JsonResponse
    {

        $dtoName = trim($dto->name);

        $role = new Role();

        $role->setName($dtoName);

        if(!empty($dto->create)) {
            $role->setCan_create($dto->create);
        }
        if(!empty($dto->read)) {
            $role->setCan_read($dto->read);
        }
        if(!empty($dto->update)) {
            $role->setcan_update($dto->update);
        }
        if(!empty($dto->delete)) {
            $role->setCan_delete($dto->delete);
        }

        $em->persist($role);
        $em->flush();

        return $this->json([
            'message' => 'User Role added',
            'path' => 'src/Controller/RoleController.php',
            'role' => $role
        ]);
    }
}
