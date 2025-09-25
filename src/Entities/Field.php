<?php

namespace Welin\PhpEtiquetaGenerator\Entities;

class Field
{
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
}