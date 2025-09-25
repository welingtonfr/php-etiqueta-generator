<?php

namespace Welin\PhpEtiquetaGenerator;


use mikehaertl\wkhtmlto\Pdf;

class EtiquetasSheet
{
    private string $templateJson;
    private array $fields;

    private array $data;

    private array $etiquetas;


    public function __construct(string $templateJson, array $fields)
    {
        $this->templateJson = $templateJson;
        $this->fields = $fields;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function render(): void
    {
        $this->generateEtiquetas();
    }

    public function getPdf()
    {
       // $pdf = new Pdf();
        $pdf = new Pdf([
            'no-pdf-compression',
            'page-size' => 'A4',
            'encoding' => 'utf-8',
            'javascript-delay' => 3000, // Aguarda 3s para JavaScript executar
            'enable-javascript',
            'debug-javascript',
            'enable-external-links',
            'enable-plugins',
            'run-script'

        ]);

        foreach ($this->etiquetas as $etiqueta) {
            file_put_contents('test.html', $etiqueta->getHtml());
            $pdf->addPage($etiqueta->getHtml());
        }

        if (!$pdf->saveAs('test.pdf')) {
            throw new \Exception($pdf->getError());
        }
    }

    private function generateEtiquetas()
    {
        foreach ($this->data as $item) {
            $this->etiquetas[] = new Etiqueta($this->fields, $item, $this->templateJson);
        }
    }

}