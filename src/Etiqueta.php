<?php

namespace Welin\PhpEtiquetaGenerator;

use Mpdf\Mpdf;

class Etiqueta
{
    private HtmlRender $htmlRender;
    private string $templateJson;

    /**
    * @var Field[]
    **/
    private array $fields;
    private array $data;

    public function __construct(array $fields, array $data, string $templateJson)
    {
        $this->templateJson = $templateJson;
        $this->htmlRender = new HtmlRender();
        $this->fields = $fields;
        $this->data = $data;
    }

    public function getHtml(): string
    {
        return $this->htmlRender->render('etiqueta.html.twig', [
            'template' => $this->templateJson,
            'data' => json_encode($this->getDataWithFields())
        ]);
    }

    public function getDataWithFields()
    {
        $newData = [];

        foreach ($this->fields as $field) {
            $newData[$field->getLabel()] = $this->data[$field->getDataKey()];
        }

        return $newData;
    }
}