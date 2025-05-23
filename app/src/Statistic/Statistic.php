<?php

namespace App\src\Statistic;

abstract class Statistic
{
    protected array $input_data;

    protected string $type;

    protected array $labels;

    protected array $numbers;

    public function __construct(array $input_data, string $type)
    {
        $this->input_data = $input_data;
        $this->type = $type;
    }

    public function get_labels(): array
    {
        return $this->labels;
    }

    public function get_numbers(): array
    {
        return $this->numbers;
    }

    abstract public function calculate(): void;
}
