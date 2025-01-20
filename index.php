<?php

include 'Map.php';

function readInput(): array {
    $maze = [];
    $start = $end = null;

    [$rows, ] = array_map('intval', explode(' ', trim(fgets(STDIN))));

    for ($i = 0; $i < $rows; $i++) {
        $maze[] = array_map('intval', explode(' ', trim(fgets(STDIN))));
    }

    [$startRow, $startCol, $endRow, $endCol] = array_map('intval', explode(' ', trim(fgets(STDIN))));

    $start = [$startRow, $startCol];
    $end = [$endRow, $endCol];

    return [$maze, $start, $end];
}

function reconstructPath(array $mapPrices, Map $map, array $start, array $end): array {
    $path = [];
    [$i, $j] = $end;

    while ([$i, $j] !== $start) {
        $path[] = [$i, $j];
        $canGoCells = $map->canGoTo($i, $j);
        $currentPrice = $mapPrices[$i][$j] - $map->getCellValue($i, $j);

        foreach ($canGoCells as $cell) {
            if ($mapPrices[$cell[0]][$cell[1]] === $currentPrice) {
                [$i, $j] = $cell;
                break;
            }
        }
    }

    $path[] = $start;

    return array_reverse($path);
}

try {
    [$maze, $start, $end] = readInput();
    $map = new Map($maze);

    if ($map->getCellValue($start[0], $start[1]) === 0 || $map->getCellValue($end[0], $end[1]) === 0) {
        fwrite(STDERR, "Начало или конец заблокированы стеной.\n");
        exit(1);
    }

    $queue = [$start];
    $mapPrices = $map->initializeMatrix(-1);
    $mapPrices[$start[0]][$start[1]] = 0;

    while (!empty($queue)) {
        [$x, $y] = array_shift($queue);
        $neighbors = $map->canGoTo($x, $y);

        foreach ($neighbors as [$nx, $ny]) {
            $newPrice = $mapPrices[$x][$y] + $map->getCellValue($nx, $ny);

            if ($mapPrices[$nx][$ny] === -1 || $newPrice < $mapPrices[$nx][$ny]) {
                $mapPrices[$nx][$ny] = $newPrice;
                $queue[] = [$nx, $ny];
            }
        }
    }

    if ($mapPrices[$end[0]][$end[1]] !== -1) {
        $path = reconstructPath($mapPrices, $map, $start, $end);
        foreach ($path as [$i, $j]) {
            echo "$i $j\n";
        }
        echo ".\n";
    } else {
        fwrite(STDERR, "Путь не найден.\n");
        exit(2);
    }
} catch (Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(3);
}
