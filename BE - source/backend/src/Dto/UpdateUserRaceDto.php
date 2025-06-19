<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateUserRaceDto {

    public function __construct(
        public ?string $name,

        public ?string $size,
        
        public ?float $total,

        public ?int $race_id,

        public ?int $km

    ) {
    }
}