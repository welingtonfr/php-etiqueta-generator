<?php

namespace Welin\PhpEtiquetaGenerator\Attributes;

class Field
{
    const BARCODE_LABEL = 'barcode';
    const BARCODE_EAN13 = 'EAN13';
    const BARCODE_CODE39 = 'CODE39';

    private string $label;
    private string $dataKey;

    public function __construct(
        string $label,
        string $dataKey,
    )
    {
        $this->label = $label;
        $this->dataKey = $dataKey;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDataKey(): string
    {
        return $this->dataKey;
    }

    static function barcode(string $dataKey): Field
    {
        return new Field('barcode', $dataKey);
    }
}