<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

abstract readonly class UserDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email()]
        public string $email,
        
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 15)]
        public string $password,
    ) {
    }
}