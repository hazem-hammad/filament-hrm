<?php

namespace App\DTOs\V1\User\Expert;

use App\DTOs\Common\AbstractDTO;

class ViewScheduleDTO extends AbstractDTO
{
    protected int $duration;

    protected ?string $date;

    final public function getDuration(): ?int
    {
        return $this->duration;
    }

    final public function getDate(): string
    {
        return $this->date;
    }

    public function toArray(): array
    {
        return [
            'duration' => $this->duration,
            'date' => $this->date,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->date = $data['date'];
        $this->duration = $data['duration'];

        return true;
    }
}
