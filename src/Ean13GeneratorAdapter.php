<?php

namespace Welin\PhpEtiquetaGenerator;

use Picqer\Barcode\Types\TypeEanUpcBase;

class Ean13GeneratorAdapter extends TypeEanUpcBase
{
    protected int $length = 13;
    protected bool $upca = false;
    protected bool $upce = false;

    protected function calculateChecksumDigit(string $code): int
    {
        $length = $this->length;

        $dataLength = $length - 1;

        $code = str_pad($code, $dataLength, '0', STR_PAD_LEFT);

        return intval($code[$dataLength]);
    }
}