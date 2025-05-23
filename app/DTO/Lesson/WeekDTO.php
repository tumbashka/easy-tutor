<?php

namespace App\DTO\Lesson;

use Illuminate\Support\Collection;

class WeekDTO
{
    public function __construct(
        public ?int $weekOffset = null,
        public ?array $weekDays = null,
        public ?array $previous = null,
        public ?array $next = null,
        public ?Collection $lessonsOnDays = null,
        public ?string $weekBorders = null,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
