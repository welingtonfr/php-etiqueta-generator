<?php

namespace Welin\PhpEtiquetaGenerator;

use Dompdf\Dompdf;
use Dompdf\Options;
use Spatie\Browsershot\Browsershot;
use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;

class EtiquetasSheet
{
    private array $data = [];

    /**
     * @var Field[]
     */
    private array $fields = [];

    /**
     * @var Etiqueta[]
     **/
    private array $etiquetas = [];
    private PageMargin $pageMargin;
    private EtiquetaTemplate $etiquetaTemplate;
    private int $colunas = 3;


    public function __construct(
         PageMargin $pageMargin,
         EtiquetaTemplate $etiquetaTemplate
    )
    {
        $this->pageMargin = $pageMargin;
        $this->etiquetaTemplate = $etiquetaTemplate;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setColunas(int $colunas): void
    {
        $this->colunas = $colunas;
    }

    public function render(): void
    {
        $this->generateEtiquetas();
    }

    public function getPdf(): string
    {
        if (empty($this->etiquetas)) {
            throw new \Exception('Nenhuma etiqueta para gerar. Certifique-se de chamar render() primeiro.');
        }
        return $this->generatePdfWithDomPdf();
    }

    public function generatePdfWithBrowserShot(): string
    {
        $htmlPagina = $this->generatePageHtml();

        return Browsershot::html($htmlPagina)
            ->setChromePath(getenv('CHROME_PATH') ?? "/usr/bin/chromium")
            ->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox',
                'disable-gpu',
                'disable-dev-shm-usage',
                'single-process',
                'no-zygote',
                'disable-extensions',
                'disable-background-networking',
                'disable-sync',
                'disable-translate',
                'disable-background-timer-throttling',
                'memory-pressure-off',
                'max_old_space_size=4096',

            ])
            ->timeout(120)
            ->setDelay(0)
            ->showBackground()
            ->scale(1)
            ->margins(0, 0, 0, 0)
            ->paperSize($this->getPageWidth(), $this->getPageHeight())
            ->pdf();
    }

    private function generatePdfWithDomPdf(): string
    {
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('debugKeepTemp', true);
        $options->set('debugLayout', true);
        $options->set('chroot', '/');
        $options->set('tempDir', sys_get_temp_dir());

        $dompdf = new Dompdf($options);

        $htmlPagina = $this->generatePageHtml();

        file_put_contents('debug_full.html', $htmlPagina);

        $dompdf->loadHtml($htmlPagina);

        $widthInPoints = $pageWidth * 2.834645669;
        $heightInPoints = $pageHeight * 2.834645669;

        $dompdf->setPaper([0.0, 0.0, $widthInPoints, $heightInPoints]);

        $dompdf->render();

        return $dompdf->output();
    }

    private function generatePageHtml(): string
    {
        $html = '<!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    html, body {
                        width: 100%;
                        height: 100%;
                        font-family: sans-serif;
                    }
                    
                    .page-container {
                        width: 100%;
                        height: 100%;
                        padding: '.$this->pageMargin->getTopMargin().'mm '.$this->pageMargin->getRightMargin().'mm 0 '.$this->pageMargin->getLeftMargin().'mm;
                        page-break-after: always;
                        position: relative;
                    }
                    
                    .page-container:last-child {
                        page-break-after: auto;
                    }
                    
                    .row-container {
                        display: block;
                        width: 100%;
                        position: relative;
                    }
                    
                    .etiqueta-wrapper {
                        display: inline-block;
                        vertical-align: top;
                        margin-right: '.$this->pageMargin->getCentralMargin().'mm;
                        margin-bottom: '.$this->pageMargin->getTopMargin().'mm;
                        overflow: visible;
                        position: relative;
                    }
                    
                    .etiqueta-wrapper:nth-child('.$this->colunas.'n) {
                        margin-right: 0;
                    }
                    
                    .etiqueta-content {
                        display: block;
                        position: relative;
                        z-index: 1;
                    }

                    @media print {
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        
                        .page-container {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            </head>
        <body>';

        $totalEtiquetas = count($this->etiquetas);
        $etiquetasPorPagina = $this->colunas;
        $totalPaginas = ceil($totalEtiquetas / $etiquetasPorPagina);

        for ($pagina = 0; $pagina < $totalPaginas; $pagina++) {
            $inicioEtiqueta = $pagina * $etiquetasPorPagina;
            $fimEtiqueta = min($inicioEtiqueta + $etiquetasPorPagina, $totalEtiquetas);

            $html .= '<div class="page-container">
                        <div class="row-container">';

            for ($i = $inicioEtiqueta; $i < $fimEtiqueta; $i++) {
                $etiqueta = $this->etiquetas[$i];
                $html .= '<div class="etiqueta-wrapper">';
                $html .= '<div class="etiqueta-content">' . $etiqueta->getHtml() . '</div>';
                $html .= '</div>';
            }

            $html .= '</div>
                    </div>';
        }

        $html .= '</body>
        </html>';

        return $html;
    }

    private function getPageWidth(): int
    {
        return ($this->etiquetaTemplate->getWidth() * $this->colunas) + $this->pageMargin->getTotalInlineMargins($this->colunas);
    }

    private function getPageHeight(): int
    {
        return $this->etiquetaTemplate->getHeight() + $this->pageMargin->getTopMargin();

    }

    private function processEtiquetasHtml()
    {
        for ($index = 0; $index < count($this->etiquetas); $index++) {
            yield $this->etiquetas[$index]->getHtml();
        }
    }

    private function generateEtiquetas(): void
    {
        $this->etiquetas = [];

        foreach ($this->data as $item) {
            $this->etiquetas[] = new Etiqueta($this->fields, $item, $this->etiquetaTemplate);
        }

        if (empty($this->etiquetas)) {
            throw new \Exception('Nenhuma etiqueta foi gerada. Verifique os dados fornecidos.');
        }
    }
}