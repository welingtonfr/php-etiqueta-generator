<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;
use Welin\PhpEtiquetaGenerator\EtiquetasSheet;
use Welin\PhpEtiquetaGenerator\EtiquetaTemplate;

$template = '{"attrs":{"width":793.700787411,"height":211.6535433096,"className":"stage"},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"name":"barcode","width":120,"x":550.2126151665487,"y":82.12225913621263,"scaleX":1.9003543586109106,"scaleY":1.5342389565645342,"fontSize":20,"draggable":true},"className":"Image"},{"attrs":{"text":"{nome}","x":6.99881880463026,"y":8.013584348468065,"width":535.6385777881951,"height":141.1715023994093,"fontSize":48,"fontStyle":"bold","fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"{preco}","x":4.001574927159595,"y":159.63316678689768,"width":527.5960547548851,"height":48.01358434846837,"fontSize":42,"fontStyle":"bold","fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"{barcode}","x":589.1925348452631,"y":176.2818752307127,"width":154.48856597624118,"height":30.000000000000014,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"keepRatio":false,"rotateEnabled":false,"x":1.0003937317898703,"y":147.61279026419558},"className":"Transformer"}]}]}';

$products = [
    ['price' => 28.84, 'name' => 'Produto 1 asd makf ndwj ngjdnfj dnjfdngbjf nikjfnbfjgbn fjbnfoj nvjfng jfnjvbf njvb nrjvnfrjng jfnbjfnj vnrjn ', 'barcode' => '100000001'],
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
    echo "Erro: " . $e->getMessage();
}