<?php

namespace App\Dto;

class UpdateUserDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $surname = null
    ) {}
}