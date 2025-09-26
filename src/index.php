<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\EtiquetasSheet;

$template = '{"attrs":{"width":500,"height":150},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"name":"barcode","width":120,"height":60,"draggable":true,"x":8,"y":323.00000000000006,"scaleX":3.1833333333333327,"scaleY":1.0499999999999998},"className":"Image"},{"attrs":{"text":"{nome}","x":7.999997436523273,"y":10,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"{preco}","x":8.99999749755844,"y":39,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"name":"barcode","width":120,"height":60,"draggable":true,"x":274.00001672363373,"y":85.99999999999994,"scaleX":1.8416667180379243,"scaleY":1.0166666666666675},"className":"Image"},{"attrs":{"keepRatio":false,"enabledAnchors":["top-left","top-right","bottom-left","bottom-right"],"x":11.999997680663924,"y":36},"className":"Transformer"}]}]}';

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

    $etiquetasSheet->setSideMargin(0);
    $etiquetasSheet->setTopMargin(0);
    $etiquetasSheet->setCentralMargin(0);

    $etiquetasSheet->render();
//

//    echo json_encode($etiquetasSheet->getDebugInfo());
    $etiquetasSheet->getPdf();
//    file_put_contents('test.pdf', $etiquetasSheet->getPdf());

    echo "PDF gerado com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}