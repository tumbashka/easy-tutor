<?php

namespace App\DTO\Lesson;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LessonDTO
{
    public function __construct(
        public ?int $student_id = null,
        public ?int $user_id = null,
        public ?string $student_name = null,
        public ?Carbon $date = null,
        public ?Carbon $start = null,
        public ?Carbon $end = null,
        public ?bool $is_paid = null,
        public ?bool $is_canceled = null,
        public ?int $price = null,
        public ?int $lesson_time_id = null,
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
