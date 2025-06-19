<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class BookItemDto {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $id,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $quantity,
    ) {
    }
}