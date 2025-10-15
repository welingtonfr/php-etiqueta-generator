<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;
use Welin\PhpEtiquetaGenerator\EtiquetasSheet;
use Welin\PhpEtiquetaGenerator\EtiquetaTemplate;

$template = '{"attrs":{"width":793.700787411,"height":211.6535433096,"className":"stage"},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"text":"{nome}","height":205.204781046692,"align":"center","fontSize":20,"fill":"black","name":"text","draggable":true,"width":588.7028872755756},"className":"Text"},{"attrs":{"name":"EAN13","width":120,"height":60,"x":595.1487185685031,"y":105.0626767846433,"scaleX":1.5918953915433425,"scaleY":1.6510819005309476,"fontSize":20,"draggable":true},"className":"Image"},{"attrs":{"keepRatio":false,"rotateEnabled":false,"x":-3.001159731769036,"y":-3.004993387065902},"className":"Transformer"}]}]}';

$products = [
    ['price' => 28.84, 'name' => 'Produto 1 asd makf ndwj ngjdnfj dnjfdngbjf nikjfnbfjgbn fjbnfoj nvjfng jfnjvbf njvb nrjvnfrjng jfnbjfnj vnrjn ', 'barcode' => '34343434343434'],
  ];


try {

    $priceField = new Field('preco', 'price');
    $nameField = new Field('nome', 'name');
    $barcodeField = Field::barcode('barcode');

    $pageMargin = new PageMargin(rightMargin: 0, leftMargin: 0, topMargin: 0, centralMargin: 9);
    $etiquetaTemplate = new EtiquetaTemplate($template);

    $etiquetasSheet = new EtiquetasSheet($pageMargin, $etiquetaTemplate);

    $etiquetasSheet->setFields([$priceField, $nameField, $barcodeField]);

    $etiquetasSheet->setData($products);

    $etiquetasSheet->setColunas(1);

    $etiquetasSheet->render();

    $pdf = $etiquetasSheet->getPdf();

    file_put_contents('test.txt', $pdf);

    file_put_contents('test.pdf', $pdf);

    echo "PDF gerado com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e;
}