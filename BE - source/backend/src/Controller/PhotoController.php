<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

#[Route('/api/photo' )]
final class PhotoController extends AbstractController
{
    #[Route('', name: 'upload_photo', methods:  [Request::METHOD_POST])]
    public function uploadPhoto(
        Request $request, 
        SluggerInterface $slugger,
        EntityManagerInterface $em): JsonResponse
    {
        $uploadedFile = $request->files->get('image');
        $authorization = $request->headers->get('Authorization');
        $token = explode(' ', $authorization)[1];
        $decodedToken = JWT::decode($token, new Key($_ENV['ACCESS_SECRET_KEY'], 'HS256'));

        $user = $em->getRepository(User::class)->findOneBy(['email' => $decodedToken->data->email]);

        if ($em->getRepository(Photo::class)->findOneBy(['user' => $user])) {
            return $this->json([
                'message' => 'error-userExists',
                'path' => 'src/Controller/PhotoController.php'
            ]);
        }


        if (!$uploadedFile) {
            return $this->json([
                'message' => 'error-uploadPhoto',
                'path' => 'src/Controller/PhotoController.php'
            ]);
        }

        $originalFilename = $uploadedFile->getClientOriginalName();
        $safeFilename = $slugger->slug($originalFilename);

        $extension = $uploadedFile->getClientOriginalExtension();
        $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;

        try{

            $uploadedFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );
            //----------------------------------------------

            $photo = new Photo();
            $photo->setPath($newFilename);
            $photo->setUser($user);            
            
            $em->persist($photo);
            $em->flush();

            return $this->json([
                'message' => 'Photo uploaded',
                'path' => 'src/Controller/PhotoController.php'
            ]);

        }catch(Throwable $e){
            return $this->json([
                'message' => 'error-uploadPhoto3',
                'path' => 'src/Controller/PhotoController.php',
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('', name: 'get_photo', methods: [Request::METHOD_GET])]
    public function getPhoto(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $em): BinaryFileResponse
    {

        $photo = $em->getRepository(Photo::class)->findOneBy(['user' => $user->getId()]);
        
        if (!$photo) {
            throw $this->createNotFoundException('Photo not found');
        }

        $filePath = $this->getParameter('uploads_directory').'/'.$photo->getPath();
        
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found  ' . $filePath);
        }

        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($filePath);

        return $this->file($filePath, null, ResponseHeaderBag::DISPOSITION_INLINE, $mimeType);
    }


    #[Route('', name: 'delete_photo', methods: [Request::METHOD_DELETE])]
    public function deletePhoto(
        EntityManagerInterface $em,
        #[CurrentUser()] User $user
        ): JsonResponse
    {

        $photo = $em->getRepository(Photo::class)->findOneBy(['user' => $user->getId()]);

        if ($photo == null) {
            return $this->json([
                'message' => 'error-deletePhoto',
                'path' => 'src/Controller/PhotoController.php'
            ]);
        }
        
        $filePath = $this->getParameter('uploads_directory').'/'.$photo->getPath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $em->remove($photo);
        $em->flush();

        return $this->json([
            'message' => 'photo deleted',
            'path' => 'src/Controller/PhotoController.php'
        ]);

    }

}
