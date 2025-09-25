<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\EtiquetasSheet;

$template = '{"attrs":{"width":500,"height":150},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"text":"{nome}","x":19.999999999999943,"y":10.999999999999943,"fontSize":20,"fill":"black","name":"text","draggable":true,"scaleX":0.890956566774098,"scaleY":1.1000000000000023},"className":"Text"},{"attrs":{"name":"barcode","width":120,"height":60,"draggable":true,"x":276.99999999999994,"y":74,"scaleX":1.7750000000000095,"scaleY":1.166666666666662},"className":"Image"},{"attrs":{"text":"Preço {preco}","x":22,"y":48,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"Clique duas vezes para editar","x":216.80741923538932,"y":174.75438181840266,"fontSize":20,"fill":"black","name":"text","draggable":true,"rotation":165.68483117171718,"scaleX":0.608466506735559,"scaleY":1.100000000000001,"skewX":-2.9028120418473093e-16},"className":"Text"},{"attrs":{"keepRatio":false,"enabledAnchors":["top-left","top-right","bottom-left","bottom-right"],"x":256,"y":15},"className":"Transformer"}]}]}';

try {

    $priceField = new \Welin\PhpEtiquetaGenerator\Entities\Field('preco', 'price');
    $nameField = new \Welin\PhpEtiquetaGenerator\Entities\Field('nome', 'name');

    $etiquetasSheet = new \Welin\PhpEtiquetaGenerator\EtiquetasSheet($template, [$priceField, $nameField]);

    $etiquetasSheet->setData([
        [
            'price' => 10,
            'name' => 'Produto 1'
        ],
        [
            'price' => 10,
            'name' => 'Produto 2'
        ],
        [
            'price' => 10,
            'name' => 'Produto 3'
        ]
    ]);

    $etiquetasSheet->render();

    $etiquetasSheet->getPdf();

    echo "PDF gerado com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}