<?php

namespace App\Dto;

use App\Dto\UserDto;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SignupDto extends UserDto
{
    public function __construct(
        string $email,
        string $password,
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        public string $surname
    ) {
        parent::__construct($email, $password);
    }
}