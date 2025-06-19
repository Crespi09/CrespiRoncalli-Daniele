<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class BookDto {
    /**
     * @param BookItemDto[] $books
     */
    public function __construct(
        #[Assert\Valid]
        #[Assert\Count(min: 1)]
        public readonly array $books = [],
    ) {
    }
}