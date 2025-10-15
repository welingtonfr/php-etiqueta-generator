<?php

namespace Welin\PhpEtiquetaGenerator;

use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCode39;
use Picqer\Barcode\Types\TypeEan13;
use Welin\PhpEtiquetaGenerator\Attributes\Field;

class Etiqueta
{
    /** @var Field[] */
    private array $fields;
    private array $data;

    private EtiquetaTemplate $template;

    public function __construct(array $fields, array $data, EtiquetaTemplate $template)
    {
        $this->fields = $fields;
        $this->data = $data;
        $this->template = $template;
    }

    public function getHtml(): string
    {
        $width = $this->template->getWidth();
        $height = $this->template->getHeight();
        $children = $this->template->getObjects();

        $html = "<div style=\"position:relative;width:{$width}mm;height:{$height}mm;overflow:hidden;\">";

        foreach ($children as $item) {
            $class = $item['className'] ?? '';

            switch ($class) {
                case 'Text':
                    $html .= $this->buildTextDiv($item['attrs'] ?? []);
                    break;
                case 'Image':
                    $html .= $this->buildImageDiv($item['attrs'] ?? []);
                    break;
            }
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Monta o div de texto com estilos e substituição de campos.
     * @param array $attrs
     * @return string
     */
    private function buildTextDiv(array $attrs): string
    {
        $text = $attrs['text'] ?? '';
        $text = $this->parseTextFields($text);

        $style = $this->getTextStyles($attrs);

        return "<div style=\"$style\">$text</div>";
    }

    /**
     * Substitui os campos no texto pelos valores do $this->data
     * @param string $text
     * @return string
     */
    private function parseTextFields(string $text): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($m) {
            $key = strtolower($m[1]);
            foreach ($this->fields as $f) {
                if ($f->getLabel() === $key || $f->getDataKey() === $key) {
                    return $this->data[$f->getDataKey()] ?? '';
                }
            }
            return '';
        }, $text);
    }

    /**
     * Retorna a string de estilos CSS para o div de texto.
     * @param array $attrs
     * @return string
     */
    private function getTextStyles(array $attrs): string
    {
        $style = [];

        $x = $this->template->pxToMm($attrs['x'] ?? 0);
        $y = $this->template->pxToMm($attrs['y'] ?? 0);
        $width = $this->template->pxToMm($attrs['width'] ?? 0);
        $height = $this->template->pxToMm($attrs['height'] ?? 0);
        $textAlign = $attrs['align'] ?? 'left';

        if ($x > $this->template->getWidth() || $y > $this->template->getHeight()) {
            return '';
        }

        $style[] = "left:{$x}mm";
        $style[] = "top:{$y}mm";

        $style[] = "width:{$width}mm";
        $style[] = "height:{$height}mm";

        $style[] = "text-align:{$textAlign}";

        if (isset($attrs['fontStyle'])) $style[] = "font-weight:{$attrs['fontStyle']}";
        if (isset($attrs['fontSize'])) $style[] = "font-size:{$this->template->pxToMm($attrs['fontSize'])}mm";
        if (isset($attrs['fill'])) $style[] = "color:{$attrs['fill']}";
        if (isset($attrs['rotation'])) $style[] = "transform:rotate({$attrs['rotation']}deg)";

        if (isset($attrs['scaleX']) || isset($attrs['scaleY'])) {
            $sx = $attrs['scaleX'] ?? 1;
            $sy = $attrs['scaleY'] ?? 1;
            $style[] = "transform:scale({$sx},{$sy})";
            $style[] = "transform-origin: top left";
        }

        $style[] = "position:absolute;overflow:hidden;white-space:wrap;overflow-wrap:anywhere;text-wrap:wrap;word-break:break-all;font-family:sans-serif;line-height:100%;";

        return implode(';', $style);
    }

    /**
     * Monta o placeholder de imagem para códigos de barras (pode evoluir para SVG/base64)
     * @param array $attrs
     * @return string
     */
    private function buildImageDiv(array $attrs): string
    {
        $widthImg = $this->template->pxToMm($attrs['width'] ?? 120);
        $heightImg = $this->template->pxToMm($attrs['height'] ?? 60);
        $x = $this->template->pxToMm($attrs['x'] ?? 0);
        $y = $this->template->pxToMm($attrs['y'] ?? 0);
        $scaleX = $attrs['scaleX'] ?? 1;
        $scaleY = $attrs['scaleY'] ?? 1;
        $scaleStyle = "transform:scale({$scaleX},{$scaleY});transform-origin: top left";

        if ($x > $this->template->getWidth() || $y > $this->template->getHeight()) {
            return '';
        }

        $style = "position:absolute;left:{$x}mm;top:{$y}mm;width:{$widthImg}mm;height:{$heightImg}mm;text-align:center;line-height:{$heightImg}mm;color:#444;{$scaleStyle};";

        if ($attrs['name'] == Field::BARCODE_EAN13 || $attrs['name'] == Field::BARCODE_CODE39 ) {
            $barCodeBase64 = $this->buildBarCodeBase64($attrs['name']);

            return '<img style="'.$style.'" src="data:image/png;base64,' . $barCodeBase64 . '">';
        }

        return "<div style=\"$style\">[barcode]</div>";
    }

    private function buildBarCodeBase64(string $name): string|null
    {
        $barcodeValue = $this->getBarcodeValue();

        if (!$barcodeValue) return '';

        match ($name) {
            Field::BARCODE_EAN13 => $barcodeInstance = new TypeEan13(),
            Field::BARCODE_CODE39 => $barcodeInstance = new TypeCode39(),
        };

        $barcode = $barcodeInstance->getBarcode($barcodeValue);

        $renderer = new PngRenderer();

        return base64_encode($renderer->render($barcode, $barcode->getWidth() * 2));
    }
    
    private function getBarcodeValue(): string|null
    {
        $barcodeFields = array_values(array_filter($this->fields, function ($field) {
            return $field->getLabel() === Field::BARCODE_LABEL;
        }));

        if (empty($barcodeFields)) {
            return null;
        }

        return $this->data[$barcodeFields[0]->getDataKey()] ?? null;
    }
}