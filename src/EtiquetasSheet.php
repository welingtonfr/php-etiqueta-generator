<?php

namespace Welin\PhpEtiquetaGenerator;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\CommunicationException;
use HeadlessChromium\Exception\NoResponseAvailable;
use HeadlessChromium\Page;
use setasign\Fpdi\Fpdi;
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

        return $this->generatePdfWithBrowserShot();
    }

    public function generatePdfWithBrowserShot(): string
    {
        $pageWidth = ($this->etiquetaTemplate->getWidth() * $this->colunas) + $this->pageMargin->getTotalInlineMargins($this->colunas);
        $pageHeight = $this->etiquetaTemplate->getHeight() + $this->pageMargin->getTopMargin();

        $htmlPagina = $this->generatePageHtml();

        return Browsershot::html($htmlPagina)
            ->setChromePath(getenv('CHROME_PATH') ?? "/usr/bin/chromium")
            ->margins(0, 0, 0, 0)
            ->paperSize($pageWidth, $pageHeight)
            ->pdf();
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
                    font-family: sans-serif;
                
                
                html, body {
                    width: 100%;
                    height: 100%;
                    overflow: hidden;
                }
                
                .page-container {
               
                }
                
                .row-container {
                    display: flex;
                    flex-wrap: wrap;
                    flex-direction: row;
                    align-items: flex-start;
                    justify-content: start;
                    gap: '.$this->pageMargin->getCentralMargin().'mm;
                    margin-left: '.$this->pageMargin->getLeftMargin().'mm;
                    margin-right: '.$this->pageMargin->getRightMargin().'mm;
                }
                
                .etiqueta-wrapper {
                    overflow: hidden;
                    position: relative;
                }
                
                .etiqueta-content {
                     margin-top: '.$this->pageMargin->getTopMargin().'mm;}
                }

                @media print {
                    * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="page-container">
                <div class="row-container">';
                    foreach ($this->processEtiquetasHtml() as $etiquetaHtml) {
                        $html .= '<div class="etiqueta-wrapper">';
                        $html .= '<div class="etiqueta-content">' . $etiquetaHtml . '</div>';
                        $html .= '</div>';
                    }
                $html .= '</div>
            </div>
        </body>
        </html>';

        return $html;
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