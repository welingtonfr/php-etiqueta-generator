<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;
use Welin\PhpEtiquetaGenerator\EtiquetasSheet;
use Welin\PhpEtiquetaGenerator\EtiquetaTemplate;

$template = '{"attrs":{"width":793.700787411,"height":211.6535433096,"className":"stage"},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"text":"{nome}","x":140.01574927159618,"y":5.00849021779256,"width":641.6803133579244,"height":58.030564784052956,"align":"center","fontSize":32,"fontStyle":"bold","fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"{preco}","x":9.00354358610912,"y":151.61958243842906,"width":433.5590439666339,"height":48.01358434846827,"fontSize":42,"fontStyle":"bold","fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"name":"EAN13","width":120,"height":60,"x":463.17836050082667,"y":68.09848652639347,"scaleX":2.650649657453331,"scaleY":1.767968500061517,"fontSize":20,"draggable":true},"className":"Image"},{"attrs":{"name":"imagem_empresa","width":120,"height":120,"fill":"#a0a0a0","x":10,"y":10,"fontSize":20,"draggable":true},"className":"Image"},{"attrs":{"text":"TAM: P","x":140.05118513268764,"y":109.16810631229234,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"{barcode}","x":464.1787542326172,"y":185.2971576227393,"width":315.7253239366785,"align":"center","fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"keepRatio":false,"rotateEnabled":false},"className":"Transformer"}]}]}';

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