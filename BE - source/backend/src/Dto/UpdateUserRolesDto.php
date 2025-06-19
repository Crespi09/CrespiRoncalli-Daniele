<?php

namespace App\Dto;

readonly class UpdateUserRolesDto
{
    public function __construct(
        public readonly array $roles
    ) {}
}
