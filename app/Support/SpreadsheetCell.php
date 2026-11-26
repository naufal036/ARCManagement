<?php

namespace App\Support;

namespace App\Support;

class SpreadsheetCell
{
    public static function safeText(?string $value): string
    {
        $value ??= '';

        return preg_match('/^\s*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }
}
