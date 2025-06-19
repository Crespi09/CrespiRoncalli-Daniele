<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRaceDto {

    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public string $size,
        
        #[Assert\NotBlank]
        public float $total,

        #[Assert\NotBlank]
        public int $race_id,

        #[Assert\NotBlank]
        public int $km

    ) {
    }
}