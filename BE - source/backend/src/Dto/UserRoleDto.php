<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRoleDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $role_name,
    ) {
    }
}