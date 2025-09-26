<?php

namespace Welin\PhpEtiquetaGenerator;

use mikehaertl\wkhtmlto\Pdf;

class EtiquetasSheetOld
{
    private string $templateJson;
    private array $fields;
    private array $data;
    
    /**
    * @var Etiqueta[]
    **/
    private array $etiquetas;
    
    // Propriedades para controlar o layout
    private int $colunas = 2;
    private int $espacamento = 10; // em mm
    private int $larguraEtiqueta = 200; // largura de cada etiqueta em mm
    private int $alturaEtiqueta = 100; // altura de cada etiqueta em mm

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

    public function setEspacamento(int $espacamento): void
    {
        $this->espacamento = $espacamento;
    }

    public function setLarguraEtiqueta(int $largura): void
    {
        $this->larguraEtiqueta = $largura;
    }

    public function setAlturaEtiqueta(int $altura): void
    {
        $this->alturaEtiqueta = $altura;
    }

    public function render(): void
    {
        $this->generateEtiquetas();
    }

    public function getPdf()
    {
        $etiquetas = $this->etiquetas ?? [];
        
        if (empty($etiquetas)) {
            throw new \Exception('Nenhuma etiqueta para gerar');
        }

        // Calcula o tamanho da página baseado no número de colunas
        $larguraPagina = ($this->larguraEtiqueta * $this->colunas) + ($this->espacamento * ($this->colunas + 1));
        $alturaPagina = $this->alturaEtiqueta + ($this->espacamento * 2);

        $pdf = new Pdf([
            'page-width' => $larguraPagina,
            'page-height' => $alturaPagina,
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'encoding' => 'utf-8',
            'disable-smart-shrinking',
            'print-media-type',
            'javascript-delay' => 3000,
            'keep-relative-links',
            'enable-local-file-access',
            'enable-plugins',
            'debug-javascript',
            //'run-script', // Permite execução de scripts
            'window-status' => 'ready', // Aguarda o sinal "ready" do JavaScript
            'load-error-handling' => 'ignore',
            //'load-media-error-handling' => 'ignore',
            // Configurações específicas para Canvas/JavaScript
            'enable-external-links',
            'images',
            'enable-forms',
           // 'minimum-font-size' => 0

        ]);

        // Organiza as etiquetas em grupos (uma linha por página)
        $totalEtiquetas = count($etiquetas);
        
        for ($i = 0; $i < $totalEtiquetas; $i += $this->colunas) {
            $htmlPagina = $this->generatePageHtml($etiquetas, $i);
            $pdf->addPage($htmlPagina);
        }
        
        return $pdf;
    }

    private function generatePageHtml(array $etiquetas, int $startIndex): string 
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body { 
                    font-family: Arial, sans-serif;
                    width: 100%;
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: white;
                }
                .row-container {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: ' . $this->espacamento . 'mm;
                    width: 100%;
                    height: 100%;
                }
                .etiqueta-wrapper {
                    width: ' . $this->larguraEtiqueta . 'mm;
                    height: ' . $this->alturaEtiqueta . 'mm;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 1px dashed #ccc;
                    background: white;
                    overflow: hidden;
                }
                .etiqueta-content {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>
        </head>
        <body>
            <div class="row-container">';

        // Adiciona as etiquetas da linha atual
        for ($j = 0; $j < $this->colunas; $j++) {
            $etiquetaIndex = $startIndex + $j;
            
            $html .= '<div class="etiqueta-wrapper">';
            
            if ($etiquetaIndex < count($etiquetas)) {
                $etiquetaHtml = $etiquetas[$etiquetaIndex]->getHtml();
                $etiquetaHtml = $this->cleanEtiquetaHtml($etiquetaHtml);
                $html .= '<div class="etiqueta-content">' . $etiquetaHtml . '</div>';
            } else {
                // Espaço vazio se não houver mais etiquetas
//                $html .= '<div class="etiqueta-content"></div>';
            }
            
            $html .= '</div>';
        }

        $html .= '</div></body></html>';

        return $html;
    }

    private function cleanEtiquetaHtml(string $html): string
    {
        // Remove tags HTML e BODY para evitar conflitos
        $html = preg_replace('/<\/?html[^>]*>/i', '', $html);
        $html = preg_replace('/<\/?body[^>]*>/i', '', $html);
        $html = preg_replace('/<\/?head[^>]*>/i', '', $html);
        $html = preg_replace('/<meta[^>]*>/i', '', $html);
        
        return trim($html);
    }

    private function generateEtiquetas()
    {
        foreach ($this->data as $item) {
            $this->etiquetas[] = new Etiqueta($this->fields, $item, $this->templateJson);
        }
    }
}