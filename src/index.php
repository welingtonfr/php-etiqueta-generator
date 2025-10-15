<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;
use Welin\PhpEtiquetaGenerator\EtiquetasSheet;
use Welin\PhpEtiquetaGenerator\EtiquetaTemplate;

$template = '{"attrs":{"width":302.362204728,"height":302.362204728,"className":"stage"},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"name":"imagem_produto","width":120,"height":120,"fill":"#a0a0a0","x":10,"y":10,"fontSize":20,"draggable":true},"className":"Image"},{"attrs":{"text":"Clique duas vezes para editar","x":11.997632338194698,"y":151.83189601182514,"height":137.86030795348856,"fontSize":20,"fill":"black","name":"text","draggable":true,"width":275.5865872350709},"className":"Text"},{"attrs":{"keepRatio":false,"rotateEnabled":false},"className":"Transformer"}]}]}';

$products = [
    [
        'price' => 28.84,
        'name' => 'Produto 1 asd makf ndwj ngjdnfj dnjfdngbjf nikjfnbfjgbn fjbnfoj nvjfng jfnjvbf njvb nrjvnfrjng jfnbjfnj vnrjn ',
        'barcode' => '34343434343434',
        'image_url' => 'https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png'
    ],
  ];


try {

    $priceField = new Field('preco', 'price');
    $nameField = new Field('nome', 'name');
    $nameField = new Field('imagem_produto', 'image_url');
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