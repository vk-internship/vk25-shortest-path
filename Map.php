<?php

class Map {
    protected array $map;

    public function __construct(array $map) {
        $this->map = $map;
    }

    public function getCellValue(int $i, int $j): int {
        return $this->isCellAvailable($i, $j) ? $this->map[$i][$j] : 0;
    }

    public function isCellAvailable(int $i, int $j): bool {
        return isset($this->map[$i][$j]) && $this->map[$i][$j] > 0;
    }

    public function canGoTo(int $i, int $j): array {
        $directions = [
            [$i, $j + 1],
            [$i + 1, $j],
            [$i, $j - 1],
            [$i - 1, $j],
        ];

        return array_filter($directions, fn($cell) => $this->isCellAvailable($cell[0], $cell[1]));
    }

    public function initializeMatrix(int $value): array {
        return array_fill(0, $this->rowsCount(), array_fill(0, $this->colsCount(), $value));
    }

    public function rowsCount(): int {
        return count($this->map);
    }

    public function colsCount(): int {
        return count($this->map[0]);
    }
}
