<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RoleDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 15)]
        public string $name,
        
        public ?bool $create,

        public ?bool $read,

        public ?bool $update,

        public ?bool $delete,
    ) {
    }
}