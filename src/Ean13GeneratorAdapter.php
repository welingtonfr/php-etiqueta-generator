<?php

namespace Welin\PhpEtiquetaGenerator;

use Picqer\Barcode\Types\TypeEanUpcBase;

class Ean13GeneratorAdapter extends TypeEanUpcBase
{
    protected int $length = 13;
    protected bool $upca = false;
    protected bool $upce = false;

    public function getBarcode(string $code): \Picqer\Barcode\Barcode
    {
        $this->length = strlen($code);

        return parent::getBarcode($code);
    }

    protected function calculateChecksumDigit(string $code): int
    {
        $dataLength = $this->length - 1;

        $code = str_pad($code, $dataLength, '0', STR_PAD_LEFT);

        return intval($code[$dataLength]);
    }
}