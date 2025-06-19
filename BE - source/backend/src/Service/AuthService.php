<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SebastianBergmann\Environment\Console;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AuthService {

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) { }

    // funzioni generazione e controllo token  -----------------------------------------------

    public function generateAccessToken($data, $key)
    {
        $payload = [
            'iss'   => 'http://example.org',
            'aud'   => 'http://example.com',
            'iat'   => time(), // quando il token inizia a essere valido
            'exp'   =>  strtotime('+30 minute'), // quando il token non è più valido
            'data'  => $data
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function generateRefreshToken($data, $key)
    {
        $payload = [
            'iss'   => '', // mittente token
            'aud'   => '', // destinatario del token
            'iat'   => time(), // quando il token inizia a essere valido
            'exp'   =>  strtotime('+7 days'), // quando il token non è più valido
            'data'  => $data
        ];

        return JWT::encode($payload, $key, 'HS256');
    }


    public function isTokenExpired($token, $key) {
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $now = new DateTimeImmutable();
            return $decoded->exp < $now->getTimestamp();
        } catch (Exception $e) {
            return true;
        }
    }

    // --------------------------------


    public function signupValidator($data): array{

        $errors = [];

        if(!is_array($data)){
            array_push($errors, "Dati mandati nel formato sbagliato");
            return $errors;
        }

        $email=$data['email'];
        $password=$data['password'];
        $name = $data['name'];
        $surname = $data['surname'];

        $user = $this->em->getRepository(User::class);

        if($email === "" || $password === "" || $name === "" || $surname === ""){
            array_push($errors, "Tutti i campi sono obbligatori");
            return $errors;
        }

        if($user->findOneBy(['email' => $email]) !== null){
            array_push($errors, "Utente già registrato");
        }

        if(strlen($password) < 8){
            array_push($errors, "La password deve essere di almeno 8 caratteri");
        }

        return $errors;
    }

    
    public function signInValidator($data, $user): array{
        $errors = [];

        if(!is_array($data)){
            array_push($errors, "Dati mandati nel formato sbagliato");
            return $errors;
        }

        if($data['email'] === "" || $data['password'] === ""){
            array_push($errors, "Tutti i campi sono obbligatori");
            return $errors;
        }

        if ($user === null) {
            array_push($errors, 'Utente non trovato');
            return $errors;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            array_push($errors, 'Password non valida');
        }

        return $errors;

    }

}