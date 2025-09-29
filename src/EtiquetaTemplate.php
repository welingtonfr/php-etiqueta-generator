<?php

namespace Welin\PhpEtiquetaGenerator;

class EtiquetaTemplate
{
    private array $template;

    public function __construct(string $templateJson)
    {
        $tpl = json_decode($templateJson, true);
        $this->template = is_array($tpl) ? $tpl : [];
    }

    public function getWidth(): float
    {
        return $this->pxToMm($this->template['attrs']['width'] ?? 300);
    }

    public function getHeight(): float
    {
        return $this->pxToMm($this->template['attrs']['height'] ?? 180);
    }

    public function pxToMm(float $px): float
    {
        return round($px * 0.2645833333) / 2;
    }

    public function getObjects(): array
    {
        return $this->template['children'][0]['children'] ?? [];
    }
}