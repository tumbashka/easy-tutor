<?php

namespace App\DTO\Board;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BoardFilterDTO
{
    public function __construct(
        public ?string $name = null,
        public ?int $subject_id = null,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function create(array $data): self
    {
        return new self(...$data);
    }
}
