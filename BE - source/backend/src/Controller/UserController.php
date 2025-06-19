<?php

namespace App\Controller;

use App\Dto\UpdateUserDto;
use App\Dto\UpdateUserRolesDto;
use App\Dto\UserRoleDto;
use App\Entity\RefreshToken;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user' )]
final class UserController extends AbstractController
{
    #[Route('/all', name: 'all_user', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_ADMIN')]
    public function allUsers(
        EntityManagerInterface $entityManager,
        ): JsonResponse
    {
        
        $users = $entityManager->getRepository(User::class)->findAll();
    
        $usersData = array_map(function(User $u) {

            return [
                'id'    => $u->getId(),
                'email' => $u->getEmail(),
                'roles' => $u->getUserRoles()->map(fn(Role $r) => $r->getName())->toArray()
            ];

        }, $users);
                
        return $this->json([
            'message' => 'Users data',
            'path' => 'src/Controller/UserController.php',
            'data' => $usersData
        ]);
    }

    #[Route('', name: 'app_user', methods: [Request::METHOD_GET])]
    public function userInfo(
        EntityManagerInterface $em,
        #[CurrentUser()] User $currentUser,
        Request $request
        ): JsonResponse
    {

        if($currentUser === null) {
            return $this->json([
                'message' => 'User - Not Found',
                'path' => 'src/Controller/UserController.php',
            ]);        
        }
        
        $device = $request->getClientIp() . ":" . $request->headers->get('User-Agent');
        
        $rt = $em->getRepository(RefreshToken::class)->findBy(['user' => $currentUser->getId()]);

        $currentDevice = [];
        $devices = [];

        foreach ($rt as $r) {
            $devices[] = [
                'id' => $r->getId(),
                'device' => $r->getDevice()
            ];

            if($r->getDevice() == $device) {
                $currentDevice = [
                    'id' => $r->getId(),
                    'device' => $r->getDevice()
                ];
            }

        }

        return $this->json([
            'message' => 'User data',
            'path' => 'src/Controller/UserController.php',
            'data' => [
                'email' => $currentUser->getEmail(),
                'name' => $currentUser->getName(),
                'surname' => $currentUser->getSurname(),
                'currentDevice' => $currentDevice,
                'devices' => $devices
            ]
        ]);
    }
    

    #[Route('', name: 'update_user', methods: [Request::METHOD_PUT])]
    public function updateUser(
        #[MapRequestPayload()] UpdateUserDto $dto,
        EntityManagerInterface $em,
        #[CurrentUser()] User $currentUser,
    ): JsonResponse {

        if(!empty($dto->name)) {
            $currentUser->setName($dto->name);
        }
        if(!empty($dto->surname)) {
            $currentUser->setSurname($dto->surname);
        }

        $em->persist($currentUser);
        $em->flush();

        return $this->json([
            'message' => 'User Updated',
            'path' => 'src/Controller/UserController.php'
        ]);

    }


    #[Route('/device/{device_id}', name: 'delete_device', methods: [Request::METHOD_DELETE])]
    public function deleteDevice(
        EntityManagerInterface $em,
        $device_id,
        #[CurrentUser()] User $currentUser
    ): JsonResponse {

        try {
            
            $device = $em->getRepository(RefreshToken::class)->findOneBy(['id' => $device_id, 'user' => $currentUser->getId()]);
    
            if ($device === null) {
                return $this->json([
                    'message' => 'Device not found',
                    'path' => 'src/Controller/UserController.php',
                ], 404);
            }
    
            $em->remove($device);
            $em->flush();
    
            return $this->json([
                'message' => 'Device - ' .  $device_id . ' deleted',
                'path' => 'src/Controller/UserController.php',
            ], 200);
    
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Error deleting Device: ' . $e->getMessage(),
                'path' => 'src/Controller/UserController.php',
            ], 500);
        }

    }

    // -------------------- USER - ROLES -------------------------------

    
    #[Route('/roles', name: 'get_user_roles', methods: [Request::METHOD_GET])]
    public function getRole(#[CurrentUser()] User $user): JsonResponse {

        return $this->json([
            'message' => 'User Roles',
            'path' => 'src/Controller/UserController.php',
            'roles' => $user->getUserRoles()->toArray()
        ]);

    }

    
    #[Route('/role', name: 'add_user_role', methods: [Request::METHOD_POST])]
    public function addRole(
        #[MapRequestPayload()] UserRoleDto $dto,
        EntityManagerInterface $em,
        #[CurrentUser()] User $currentUser
    ): JsonResponse {

        $roleName = trim($dto->role_name);
        $role = $em->getRepository(Role::class)->findOneBy(['name' => $roleName]);

        if($roleName === null) {
            return $this->json([
                'message' => 'Role Name is required',
                'path' => 'src/Controller/UserController.php'
            ]);
        }

        $currentUser->addUserRole($role);

        $em->persist($currentUser);
        $em->flush();

        return $this->json([
            'message' => 'Role ' . $roleName . '- added to User',
            'path' => 'src/Controller/UserController.php'
        ]);
    }

    #[Route('/roles/{id}', name: 'update_user_roles', methods: [Request::METHOD_PUT])]
    // #[IsGranted('ROLE_ADMIN')]
    public function updateUserRoles(
        #[MapRequestPayload()] UpdateUserRolesDto $dto,
        EntityManagerInterface $em,
        #[MapEntity(mapping: [ 'id' => 'id' ])]
        User $user
    ): JsonResponse {

        // rimuovo i ruoli precedenti
        foreach ($user->getUserRoles() as $role) {

            $user->removeUserRole($role);
        }

        $expr = $em->getExpressionBuilder();

        $roles = $em->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r')
            ->where($expr->in('r.name', ':roles'))
            ->setParameter('roles', $dto->roles)
            ->getQuery()
            ->getResult()
        ;

        // aggiungo quelli nuovi
        foreach ($roles as $role) {

            $user->addUserRole($role);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User Roles Updated',
            'path' => 'src/Controller/UserController.php'
        ]);
    }


    #[Route('/users/{id}', name: 'api_delete_user', methods: [Request::METHOD_DELETE])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(
        EntityManagerInterface $em,
        #[MapEntity(mapping: [ 'id' => 'id' ])] 
        User $user,
        #[CurrentUser()] User $currentUser
    ): JsonResponse {

        if($currentUser->getId() === $user->getId()) {
            throw new UnprocessableEntityHttpException("Cannot delete yourself!");
        }

        $em->remove($user);
        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);

    }

}