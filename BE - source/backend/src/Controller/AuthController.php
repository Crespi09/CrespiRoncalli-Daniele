<?php

namespace App\Controller;

use App\Dto\SigninDto;
use App\Dto\SignupDto;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthService;
// use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
// use Doctrine\ORM\Query\Expr\Func;
// use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth' )]
final class AuthController extends AbstractController
{
    #[Route('/signup', name: 'app_signup', methods: [Request::METHOD_POST])]
    public function signup(
        #[MapRequestPayload()] SignupDto $dto, 
        UserRepository $userRepository,
        EntityManagerInterface $em,
        AuthService $auth,
        Request $request
    ): JsonResponse {

        $dtoEmail       = trim($dto->email);
        $dtoPassword    = trim($dto->password);
        $dtoName        = trim($dto->name);
        $dtoSurname     = trim($dto->surname);

        $errors =  $auth->signupValidator(["email" => $dtoEmail, "password" => $dtoPassword, "name" => $dtoName, "surname" => $dtoSurname]);    

        if(count($errors) > 0){
            return $this->json([
                'message'   =>      'signup-error',
                'path'      =>      'src/Controller/AuthController.php',
                "errors"    =>      $errors
                ], 400);

        }

        $user = $userRepository->create(
            $dtoEmail, 
            $dtoPassword, 
            $dtoName, 
            $dtoSurname
        );

        $em->persist($user);
        $em->flush();

        $data = [
            "email"     =>      $user->getEmail(),
            "id"        =>      $user->getId(),
            "roles"     =>      $user->getRoles(),
        ];

        $access_token = $auth->generateAccessToken( $data, $_ENV["ACCESS_SECRET_KEY"]);
        $refresh_token = $auth->generateRefreshToken( $data, $_ENV["REFRESH_SECRET_KEY"] );

        $device = $request->getClientIp() . ":" . $request->headers->get('User-Agent');

        $tokenParams = [
            "device"        =>      $device,
            "refreshToken"  =>      $refresh_token,
            "user"          =>      $user,
        ];
        
        $refreshTokenEntity = $em->getRepository(RefreshToken::class)->create(...$tokenParams);

        $em->persist($user);
        $em->persist($refreshTokenEntity);
        $em->flush();

        // --------------------------------

        $decoded = JWT::decode($refresh_token, new Key($_ENV['REFRESH_SECRET_KEY'], 'HS256'));

        $refreshCookie = new Cookie('refresh_token', $refresh_token, $decoded->exp , httpOnly: true);
        $response = new JsonResponse([
            'message'       =>      'Signup',
            'path'          =>      'src/Controller/AuthController.php',
            "email"         =>      $dtoEmail,
            "access_token"  =>      $access_token
        ], 200);

        $response->headers->setCookie($refreshCookie);

        return $response;

    }

    #[Route('/signin', name: 'app_signin', methods: [Request::METHOD_POST])]
    public function signin(
        #[MapRequestPayload()] SigninDto $dto,
        EntityManagerInterface $em, 
        AuthService $auth,
        Request $request,
    ): JsonResponse {

        $dtoEmail       = trim($dto->email);
        $dtoPassword    = trim($dto->password);

        $currentUser = $em->getRepository(User::class)->findOneBy(['email' => $dto->email ]);

        $errors = $auth->signinValidator(["email" =>$dtoEmail, "password" => $dtoPassword], $currentUser);

        if(count($errors) > 0){
            return $this->json([
            'message'       =>      'signin-error',
            'path'          =>      'src/Controller/AuthController.php',
            "errors"        =>      $errors
            ], 400);
        }

        $device = $request->getClientIp() . ":" . $request->headers->get('User-Agent');
        $data = [
            "email"     =>      $dtoEmail,
            "id"        =>      $currentUser->getId(),
            "roles"     =>      $currentUser->getRoles(),
        ];


        //  controllo ->
        //        se il device inserito non è presente nel db, vado a creare un nuovo campo in refresh_token
        //        dove vado ad aggiungere lo stesso user_id ma mac_address e refresh_token differenti

        $rfToken = $em->getRepository(RefreshToken::class)->findOneBy(['user' => $currentUser->getId(), 'device' => $device]);

        if($rfToken === null) {

            $refreshTokenEntity = new RefreshToken();

            $refreshTokenEntity->setDevice($device);
            $refreshTokenEntity->setUser($currentUser);
            
            $rt = $auth->generateRefreshToken($data, $_ENV["REFRESH_SECRET_KEY"]);

            $refreshTokenEntity->setRefreshToken($rt);

            $em->persist($refreshTokenEntity);
            $em->flush();
        }

        // controllo se il refresh token è valido
        $rfToken = $em->getRepository(RefreshToken::class)->findOneBy(['user' => $currentUser->getId(), 'device' => $device])->getRefreshToken();
        $refreshTokenExpired = $auth->isTokenExpired($rfToken, $_ENV["REFRESH_SECRET_KEY"]);

        if($refreshTokenExpired) {
            $newRefreshToken = $auth->generateRefreshToken($data, $_ENV["REFRESH_SECRET_KEY"]);

            $refreshTokenEntity = new RefreshToken();

            $refreshTokenEntity->setRefreshToken($newRefreshToken);
            $refreshTokenEntity->setDevice($device);
            $refreshTokenEntity->setUser($currentUser);

            $rfToken = $newRefreshToken;

            $em->persist($currentUser);
            $em->persist($refreshTokenEntity);
            $em->flush();
        }

        // prendo il refresh token e lo decodifico
        $decoded = JWT::decode($rfToken, new Key($_ENV['REFRESH_SECRET_KEY'], 'HS256'));
        $refreshCookie = new Cookie('refresh_token', $rfToken, $decoded->exp, httpOnly: true);

        // genero access token
        $access_token = $auth->generateAccessToken( $data, $_ENV["ACCESS_SECRET_KEY"]);

        $response = new JsonResponse([
            'message'       =>      'Signin',
            'path'          =>      'src/Controller/AuthController.php',
            "email"         =>      $dtoEmail,
            "access_token"  =>      $access_token
        ], 200);

        // inserisco il refresh token nel cookie
        $response->headers->setCookie($refreshCookie);

        return $response;
    }

    #[Route('/refreshToken', name: 'app_refresh', methods: [Request::METHOD_POST])]
    public function refreshToken(
        // #[MapRequestPayload()] TokenDto $dto, 
        AuthService $auth,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        
        // vado a prendere il refresh token, lo decodifico , 
        // prendo la mail e la utilizzo insieme alla chiave 
        // per generare un nuovo access token

        $refreshToken = $request->cookies->get('refresh_token');
        $decoded = JWT::decode($refreshToken, new Key($_ENV['REFRESH_SECRET_KEY'], 'HS256'));

        $user = $em->getRepository(User::class)->findOneBy(['email' => $decoded->data->email]);

        $data = [
            "email"     =>      $decoded->data->email,
            "id"        =>      $decoded->data->id,
            "roles"     =>      $user->getRoles(),
        ];

        $newAccessToken = $auth->generateAccessToken( $data,  $_ENV["ACCESS_SECRET_KEY"]);

        return $this->json([
            'message'       =>      'Nuovo access token generato',
            'path'          =>      'src/Controller/AuthController.php',
            "access_token"  =>      $newAccessToken,
        ]);

    }

    #[Route('/device', name: 'delete_device', methods: [Request::METHOD_DELETE])]
    public function deleteDevice(
        EntityManagerInterface $em,
        Request $request
    ): JsonResponse {

        // prendo il device, vedo a quale riga della tabella corrisponde e lo cancello

        $device = $request->getClientIp() . ":" . $request->headers->get('User-Agent');

        $device = $em->getRepository(RefreshToken::class)->findOneBy(['device' => $device]);

        if ($device !== null) {
            $em->remove($device);
        }

        $em->persist($device);
        $em->flush();

        return $this->json([
            'message'   =>      'Device Deleted',
            'path'      =>      'src/Controller/AuthController.php',
            "device"    =>      $device
        ]);

    }

}
