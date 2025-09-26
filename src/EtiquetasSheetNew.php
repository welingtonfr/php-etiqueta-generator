<?php

namespace Welin\PhpEtiquetaGenerator;

use mikehaertl\wkhtmlto\Pdf;

class EtiquetasSheetNew
{
    private string $templateJson;
    private array $fields;
    private array $data;
    
    /**
    * @var Etiqueta[]
    **/
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
        if (empty($this->etiquetas)) {
            $this->generateEtiquetas();
        }

        // Decodifica o JSON do template para pegar largura e altura
        $template = json_decode($this->templateJson, true);
        $stage = $template['attrs'] ?? [];
        $width = $stage['width'] ?? 500;
        $height = $stage['height'] ?? 300;

        // Montagem do grid/layout de etiquetas
        $html = '<div style="display: flex; flex-wrap: wrap;">';
        foreach ($this->etiquetas as $etiqueta) {
            $html .= sprintf(
                '<div style="margin: 10px; width: %dpx; height: %dpx; box-sizing: border-box;">%s</div>',
                $width,
                $height,
                $etiqueta->getHtml()
            );
        }
        $html .= '</div>';

        // Geração do PDF
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [$width, $height]]);
        // Você pode ajustar o formato da página para múltiplas colunas/linhas, conforme sua necessidade

        $mpdf->WriteHTML($html);

        // Retorna o conteúdo do PDF como string (F para salvar em arquivo, D para download, I para inline)
        return $mpdf->Output('etiquetas.pdf', \Mpdf\Output\Destination::STRING_RETURN);
    }


    private function generateEtiquetas()
    {
        foreach ($this->data as $item) {
            $this->etiquetas[] = new Etiqueta($this->fields, $item, $this->templateJson);
        }
    }
}