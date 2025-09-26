<?php

namespace Welin\PhpEtiquetaGenerator;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\CommunicationException;
use HeadlessChromium\Exception\NoResponseAvailable;
use mikehaertl\wkhtmlto\Pdf;
use Mpdf\Mpdf;

class EtiquetasSheet
{
    private string $templateJson;
    private array $fields;
    private array $data;

    /**
     * @var Etiqueta[]
     **/
    private array $etiquetas = [];

    private int $sideMargin = 0;
    private int $topMargin = 0;
    private int $centralMargin = 0;
    private int $colunas = 1;
    private float $etiquetaWidth = 100;
    private float $etiquetaHeight = 30;


    public function __construct(string $templateJson, array $fields)
    {
        $this->templateJson = $templateJson;
        $this->fields = $fields;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setColunas(int $colunas): void
    {
        $this->colunas = $colunas;
    }

    public function setSideMargin(int $sideMargin): void
    {
        $this->sideMargin = $sideMargin;
    }


    public function setTopMargin(int $topMargin): void
    {
        $this->topMargin = $topMargin;
    }


    public function setCentralMargin(int $centralMargin): void
    {
        $this->centralMargin = $centralMargin;
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

        return $this->generatePdfWithChrome();
    }

    private function generatePdfWithChrome(): string
    {
        $browserFactory = new BrowserFactory();

        // Configurações do Chrome mais robustas
        $browser = $browserFactory->createBrowser([
            'headless' => true,
            'noSandbox' => true,
            'args' => [
                '--disable-web-security',
                '--allow-running-insecure-content',
                '--disable-features=VizDisplayCompositor',
                '--enable-local-file-accesses',
                '--disable-dev-shm-usage',
                '--no-zygote',
                '--single-process',
                '--disable-extensions',
                '--disable-plugins',
                '--disable-background-networking',
                '--disable-sync',
                '--disable-translate',
                '--hide-scrollbars'
            ]
        ]);

        try {
            $page = $browser->createPage();
           $pageWidth = ($this->etiquetaWidth * $this->colunas) + ($this->centralMargin * ($this->colunas - 1)) + ($this->sideMargin * 2);
           $pageHeight = $this->etiquetaHeight + ($this->topMargin * 2);

            $pdfPages = [];
            $totalEtiquetas = count($this->etiquetas);

            for ($i = 0; $i < $totalEtiquetas; $i += $this->colunas) {
                $htmlPagina = $this->generatePageHtml($i);

                if ($i == 0) {
                 file_put_contents('debug.html', $htmlPagina);
                }

                $page->setHtml($htmlPagina);

                $pdf = $page->pdf([
                    'printBackground' => false,
                    'marginTop' => 0,
                    'marginBottom' => 0,
                    'marginLeft' => 0,
                    'marginRight' => 0,
                    'paperWidth' => $pageWidth * 0.0393700787,
                    'paperHeight' => $pageHeight * 0.0393700787,
                    'preferCSSPageSize' => false,
                     'displayHeaderFooter' => false
                ]);

                $pdfPages[] = $pdf->getBase64();
            }


            file_put_contents('test.pdf', base64_decode($pdfPages[0]));

        return '';
//            $pdf = new Pdf();
//
//            foreach ($pdfPages as $pdfPage) {
//                $pdf->addPage($pdfPage);
//
//            }
//
//            return $pdf->saveAs('test.pdf');

        } catch (CommunicationException | NoResponseAvailable $e) {
            throw new \Exception('Erro de comunicação com o Chrome: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Erro ao gerar PDF: ' . $e->getMessage());
        } finally {
            $browser->close();
        }
    }

    private function generatePageHtml(int $startIndex): string
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
                }
                
                html, body {
                    width: 100%;
                    height: 100%;
                    overflow: hidden;
                }
                
                .page-container {
                  margin-left: '.$this->sideMargin.'mm;
                  margin-right: '.$this->sideMargin.'mm;
                  margin-top: '.$this->topMargin.'mm;
                }
                
                .row-container {
                    display: flex;
                    flex-direction: row;
                    align-items: flex-start;
                    justify-content: start;
                    gap: '.$this->centralMargin.'mm;
                }
                
                .etiqueta-wrapper {
                    overflow: hidden;
                    position: relative;
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

                // Adiciona as etiquetas da linha atual
                for ($j = 0; $j < $this->colunas; $j++) {
                    $etiquetaIndex = $startIndex + $j;

                    $html .= '<div class="etiqueta-wrapper">';

                    if ($etiquetaIndex < count($this->etiquetas)) {
                        $etiquetaHtml = $this->etiquetas[$etiquetaIndex]->getHtml();
                        echo $etiquetaHtml;
                        $etiquetaHtml = $this->processEtiquetaHtml($etiquetaHtml);
                        $html .= '<div class="etiqueta-content">' . $etiquetaHtml . '</div>';
                    }

                    $html .= '</div>';
                }

                $html .= '</div>
            </div>
            
           
        </body>
        </html>';

        return $html;
    }

    private function processEtiquetaHtml(string $html): string
    {
        return trim($html);
    }

    private function generateEtiquetas(): void
    {
        $this->etiquetas = [];

        foreach ($this->data as $item) {
            $this->etiquetas[] = new Etiqueta($this->fields, $item, $this->templateJson);
        }

        if (empty($this->etiquetas)) {
            throw new \Exception('Nenhuma etiqueta foi gerada. Verifique os dados fornecidos.');
        }
    }

    /**
     * Gera uma prévia HTML de todas as etiquetas para debug
     */
    public function getPreviewHtml(): string
    {
        if (empty($this->etiquetas)) {
            $this->generateEtiquetas();
        }

        return $this->generatePageHtml(0);
    }

    /**
     * Retorna informações de debug sobre as etiquetas
     */
    public function getDebugInfo(): array
    {
        return [
            'total_etiquetas' => count($this->etiquetas),
            'colunas' => $this->colunas,
            'largura_pagina_mm' => ($this->larguraEtiqueta * $this->colunas) + ($this->espacamento * ($this->colunas + 1)),
            'altura_pagina_mm' => $this->alturaEtiqueta + ($this->espacamento * 2),
            'largura_etiqueta' => $this->larguraEtiqueta,
            'altura_etiqueta' => $this->alturaEtiqueta,
            'espacamento' => $this->espacamento,
            'campos' => array_map(fn($field) => [
                'label' => $field->getLabel(),
                'dataKey' => $field->getDataKey()
            ], $this->fields)
        ];
    }
}