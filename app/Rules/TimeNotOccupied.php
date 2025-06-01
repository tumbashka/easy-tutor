<?php

namespace App\Rules;

use App\Services\ScheduleService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class TimeNotOccupied implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function __construct(public $occupiedSlots)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($this->data['start']);
        $end = Carbon::parse($this->data['end']);

        foreach ($this->occupiedSlots as $slot) {
            if (($start >= $slot->start && $start < $slot->end) ||
                ($end > $slot->start && $end <= $slot->end) ||
                ($start <= $slot->start && $end >= $slot->end)) {
                $fail("Время пересекается с занятием: {$slot->start->format('H:i')}-{$slot->end->format('H:i')} {$slot->student->name}");
            }
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
