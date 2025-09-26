<?php

namespace Welin\PhpEtiquetaGenerator;

use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCode128;
use Welin\PhpEtiquetaGenerator\Entities\Field;

class Etiqueta
{
    private string $templateJson;
    /** @var Field[] */
    private array $fields;
    private array $data;

    public function __construct(array $fields, array $data, string $templateJson)
    {
        $this->templateJson = $templateJson;
        $this->fields = $fields;
        $this->data = $data;
    }

    public function getHtml(): string
    {
        $tpl = $this->getTemplateDecoded();
        $width = $tpl['attrs']['width'] ?? 500;
        $height = $tpl['attrs']['height'] ?? 150;
        $children = $tpl['children'][0]['children'] ?? [];

        $html = "<div style=\"position:relative;width:{$this->pxToMm($width)}mm;height:{$this->pxToMm($height)}mm;overflow:hidden;\">";

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

        file_put_contents('etiqueta.html', $html);

        return $html;
    }

    /**
     * Decodifica e retorna o template JSON como array.
     */
    private function getTemplateDecoded(): array
    {
        $tpl = json_decode($this->templateJson, true);
        return is_array($tpl) ? $tpl : [];
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

        if (isset($attrs['x'])) $style[] = "left:{$this->pxToMm($attrs['x'])}mm";
        if (isset($attrs['y'])) $style[] = "top:{$this->pxToMm($attrs['y'])}mm";
        if (isset($attrs['fontSize'])) $style[] = "font-size:{$this->pxToMm($attrs['fontSize'])}mm";
        if (isset($attrs['fill'])) $style[] = "color:{$attrs['fill']}";
        if (isset($attrs['rotation'])) $style[] = "transform:rotate({$attrs['rotation']}deg)";
        // scaleX/scaleY: implementa via transform
        if (isset($attrs['scaleX']) || isset($attrs['scaleY'])) {
            $sx = isset($attrs['scaleX']) ? $attrs['scaleX'] : 1;
            $sy = isset($attrs['scaleY']) ? $attrs['scaleY'] : 1;
            $style[] = "transform:scale({$sx},{$sy})";
            $style[] = "transform-origin: top left";
        }
        $style[] = "position:absolute;white-space:nowrap;font-family:sans-serif;text-align:center;line-height:100%;";

        return implode(';', $style);
    }

    /**
     * Monta o placeholder de imagem para códigos de barras (pode evoluir para SVG/base64)
     * @param array $attrs
     * @return string
     */
    private function buildImageDiv(array $attrs): string
    {
        $widthImg = $attrs['width'] ?? 120;
        $heightImg = $attrs['height'] ?? 60;
        $x = $attrs['x'] ?? 0;
        $y = $attrs['y'] ?? 0;
        $scaleX = $attrs['scaleX'] ?? 1;
        $scaleY = $attrs['scaleY'] ?? 1;
        $scaleStyle = "transform:scale({$scaleX},{$scaleY});transform-origin: top left";

        $style = "position:absolute;left:{$this->pxToMm($x)}mm;top:{$this->pxToMm($y)}mm;width:{$this->pxToMm($widthImg)}mm;height:{$this->pxToMm($heightImg)}mm;text-align:center;line-height:{$this->pxToMm($heightImg)}mm;color:#444;{$scaleStyle};";

        echo json_encode($attrs);

        if (strtolower($attrs['name']) == 'barcode') {
            $barcode = (new TypeCode128())->getBarcode('081231723897');
            $renderer = new PngRenderer();
            return '<img style="'.$style.'" src="data:image/png;base64,' . base64_encode($renderer->render($barcode, $barcode->getWidth() * 2)) . '">';
        }

        return "<div style=\"$style\">[barcode]</div>";
    }

    private function pxToMm(float $px): float
    {
        return $px * 0.2645833333;
    }
}