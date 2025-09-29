<?php

namespace Welin\PhpEtiquetaGenerator\Attributes;

class PageMargin
{
    public function __construct(
        private int $rightMargin = 0,
        private int $leftMargin = 0,
        private int $topMargin = 0,
        private int $centralMargin = 0,
    )
    {
    }

    public function getRightMargin(): int
    {
        return $this->rightMargin;
    }

    public function getLeftMargin(): int
    {
        return $this->leftMargin;
    }

    public function getTopMargin(): int
    {
        return $this->topMargin;
    }

    public function getCentralMargin(): int
    {
        return $this->centralMargin;
    }


    public function getTotalInlineMargins(int $columns = 1): int
    {
        return ($this->centralMargin * ($columns - 1)) + $this->rightMargin + $this->leftMargin;
    }
}