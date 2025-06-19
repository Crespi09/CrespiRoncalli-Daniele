<?php

namespace App\Dto;

use App\Dto\UserDto;

readonly class SigninDto extends UserDto
{
    public function __construct(
        string $email,
        string $password,
    ) {
        parent::__construct($email, $password);
    }
}