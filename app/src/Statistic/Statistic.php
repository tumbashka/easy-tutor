<?php

namespace App\src\Statistic;

abstract class Statistic
{
    protected array $inputData;

    protected string $type;

    protected array $labels;

    protected array $numbers;

    public function __construct(array $inputData, string $type)
    {
        $this->inputData = $inputData;
        $this->type = $type;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getNumbers(): array
    {
        return $this->numbers;
    }

    abstract public function calculate(): void;
}
