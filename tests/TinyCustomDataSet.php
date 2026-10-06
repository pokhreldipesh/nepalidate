<?php

declare(strict_types=1);

namespace Tests;

use Dipesh\NepaliDate\DataSet;

/**
 * Minimal user-defined dataset: ships its own base dates and one year row.
 */
class TinyCustomDataSet extends DataSet
{
    public function __construct(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null)
    {
        parent::__construct(
            $rows ?: [2000 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30]],
            $baseEnglishDate ?? '2000/01/01',
            $equivalentNepaliDate ?? '2000/01/01',
        );
    }
}
