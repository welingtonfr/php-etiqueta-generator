<?php

// Incluir o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Welin\PhpEtiquetaGenerator\Attributes\Field;
use Welin\PhpEtiquetaGenerator\Attributes\PageMargin;
use Welin\PhpEtiquetaGenerator\EtiquetasSheet;
use Welin\PhpEtiquetaGenerator\EtiquetaTemplate;

$template = '{"attrs":{"width":226.77165354599998,"height":188.976377955,"className":"stage"},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"text":"teste","x":10,"y":10,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"text":"teste2","x":172.28923167099998,"y":168.976377955,"fontSize":20,"fill":"black","name":"text","draggable":true},"className":"Text"},{"attrs":{"keepRatio":false,"rotateEnabled":false,"enabledAnchors":["top-left","top-right","bottom-left","bottom-right"],"x":177.39425873725094,"y":173.02695551513148},"className":"Transformer"}]}]}';

$products = [
    ['price' => 28.84, 'name' => 'Produto 1', 'barcode' => '100000001'],
    ['price' => 45.43, 'name' => 'Produto 2', 'barcode' => '100000002'],
    ['price' => 81.28, 'name' => 'Produto 3', 'barcode' => '100000003'],
    ['price' => 22.66, 'name' => 'Produto 4', 'barcode' => '100000004'],
    ['price' => 51.19, 'name' => 'Produto 5', 'barcode' => '100000005'],
    ['price' => 41.49, 'name' => 'Produto 6', 'barcode' => '100000006'],
    ['price' => 68.63, 'name' => 'Produto 7', 'barcode' => '100000007'],
    ['price' => 99.15, 'name' => 'Produto 8', 'barcode' => '100000008'],
    ['price' => 46.63, 'name' => 'Produto 9', 'barcode' => '100000009'],
    ['price' => 69.54, 'name' => 'Produto 10', 'barcode' => '100000010'],
    ['price' => 88.24, 'name' => 'Produto 11', 'barcode' => '100000011'],
    ['price' => 34.37, 'name' => 'Produto 12', 'barcode' => '100000012'],
    ['price' => 39.78, 'name' => 'Produto 13', 'barcode' => '100000013'],
    ['price' => 53.91, 'name' => 'Produto 14', 'barcode' => '100000014'],
    ['price' => 95.12, 'name' => 'Produto 15', 'barcode' => '100000015'],
    ['price' => 16.44, 'name' => 'Produto 16', 'barcode' => '100000016'],
    ['price' => 64.33, 'name' => 'Produto 17', 'barcode' => '100000017'],
    ['price' => 48.21, 'name' => 'Produto 18', 'barcode' => '100000018'],
    ['price' => 73.85, 'name' => 'Produto 19', 'barcode' => '100000019'],
    ['price' => 12.64, 'name' => 'Produto 20', 'barcode' => '100000020'],
    ['price' => 41.32, 'name' => 'Produto 21', 'barcode' => '100000021'],
    ['price' => 86.70, 'name' => 'Produto 22', 'barcode' => '100000022'],
    ['price' => 75.41, 'name' => 'Produto 23', 'barcode' => '100000023'],
    ['price' => 63.10, 'name' => 'Produto 24', 'barcode' => '100000024'],
    ['price' => 54.25, 'name' => 'Produto 25', 'barcode' => '100000025'],
    ['price' => 25.36, 'name' => 'Produto 26', 'barcode' => '100000026'],
    ['price' => 15.93, 'name' => 'Produto 27', 'barcode' => '100000027'],
    ['price' => 33.47, 'name' => 'Produto 28', 'barcode' => '100000028'],
    ['price' => 92.83, 'name' => 'Produto 29', 'barcode' => '100000029'],
    ['price' => 74.28, 'name' => 'Produto 30', 'barcode' => '100000030'],
    ['price' => 81.02, 'name' => 'Produto 31', 'barcode' => '100000031'],
    ['price' => 55.19, 'name' => 'Produto 32', 'barcode' => '100000032'],
    ['price' => 97.64, 'name' => 'Produto 33', 'barcode' => '100000033'],
    ['price' => 18.27, 'name' => 'Produto 34', 'barcode' => '100000034'],
    ['price' => 13.89, 'name' => 'Produto 35', 'barcode' => '100000035'],
    ['price' => 76.56, 'name' => 'Produto 36', 'barcode' => '100000036'],
    ['price' => 37.48, 'name' => 'Produto 37', 'barcode' => '100000037'],
    ['price' => 60.73, 'name' => 'Produto 38', 'barcode' => '100000038'],
    ['price' => 21.49, 'name' => 'Produto 39', 'barcode' => '100000039'],
    ['price' => 43.61, 'name' => 'Produto 40', 'barcode' => '100000040'],
    ['price' => 19.74, 'name' => 'Produto 41', 'barcode' => '100000041'],
    ['price' => 82.47, 'name' => 'Produto 42', 'barcode' => '100000042'],
    ['price' => 65.34, 'name' => 'Produto 43', 'barcode' => '100000043'],
    ['price' => 29.80, 'name' => 'Produto 44', 'barcode' => '100000044'],
    ['price' => 53.66, 'name' => 'Produto 45', 'barcode' => '100000045'],
    ['price' => 11.25, 'name' => 'Produto 46', 'barcode' => '100000046'],
    ['price' => 91.33, 'name' => 'Produto 47', 'barcode' => '100000047'],
    ['price' => 70.18, 'name' => 'Produto 48', 'barcode' => '100000048'],
    ['price' => 39.50, 'name' => 'Produto 49', 'barcode' => '100000049'],
    ['price' => 84.62, 'name' => 'Produto 50', 'barcode' => '100000050'],
    ['price' => 22.47, 'name' => 'Produto 51', 'barcode' => '100000051'],
    ['price' => 44.29, 'name' => 'Produto 52', 'barcode' => '100000052'],
    ['price' => 98.36, 'name' => 'Produto 53', 'barcode' => '100000053'],
    ['price' => 17.23, 'name' => 'Produto 54', 'barcode' => '100000054'],
    ['price' => 72.84, 'name' => 'Produto 55', 'barcode' => '100000055'],
    ['price' => 66.91, 'name' => 'Produto 56', 'barcode' => '100000056'],
    ['price' => 32.74, 'name' => 'Produto 57', 'barcode' => '100000057'],
    ['price' => 59.13, 'name' => 'Produto 58', 'barcode' => '100000058'],
    ['price' => 93.55, 'name' => 'Produto 59', 'barcode' => '100000059'],
    ['price' => 48.39, 'name' => 'Produto 60', 'barcode' => '100000060'],
    ['price' => 57.27, 'name' => 'Produto 61', 'barcode' => '100000061'],
    ['price' => 36.82, 'name' => 'Produto 62', 'barcode' => '100000062'],
    ['price' => 14.65, 'name' => 'Produto 63', 'barcode' => '100000063'],
    ['price' => 83.71, 'name' => 'Produto 64', 'barcode' => '100000064'],
    ['price' => 63.90, 'name' => 'Produto 65', 'barcode' => '100000065'],
    ['price' => 72.45, 'name' => 'Produto 66', 'barcode' => '100000066'],
    ['price' => 27.15, 'name' => 'Produto 67', 'barcode' => '100000067'],
    ['price' => 49.87, 'name' => 'Produto 68', 'barcode' => '100000068'],
    ['price' => 61.20, 'name' => 'Produto 69', 'barcode' => '100000069'],
    ['price' => 78.44, 'name' => 'Produto 70', 'barcode' => '100000070'],
    ['price' => 31.56, 'name' => 'Produto 71', 'barcode' => '100000071'],
    ['price' => 15.77, 'name' => 'Produto 72', 'barcode' => '100000072'],
    ['price' => 67.93, 'name' => 'Produto 73', 'barcode' => '100000073'],
    ['price' => 24.19, 'name' => 'Produto 74', 'barcode' => '100000074'],
    ['price' => 52.81, 'name' => 'Produto 75', 'barcode' => '100000075'],
    ['price' => 88.66, 'name' => 'Produto 76', 'barcode' => '100000076'],
    ['price' => 40.12, 'name' => 'Produto 77', 'barcode' => '100000077'],
    ['price' => 19.23, 'name' => 'Produto 78', 'barcode' => '100000078'],
    ['price' => 90.15, 'name' => 'Produto 79', 'barcode' => '100000079'],
    ['price' => 33.82, 'name' => 'Produto 80', 'barcode' => '100000080'],
    ['price' => 20.65, 'name' => 'Produto 81', 'barcode' => '100000081'],
    ['price' => 77.43, 'name' => 'Produto 82', 'barcode' => '100000082'],
    ['price' => 26.18, 'name' => 'Produto 83', 'barcode' => '100000083'],
    ['price' => 38.91, 'name' => 'Produto 84', 'barcode' => '100000084'],
    ['price' => 59.77, 'name' => 'Produto 85', 'barcode' => '100000085'],
    ['price' => 12.34, 'name' => 'Produto 86', 'barcode' => '100000086'],
    ['price' => 47.82, 'name' => 'Produto 87', 'barcode' => '100000087'],
    ['price' => 71.99, 'name' => 'Produto 88', 'barcode' => '100000088'],
    ['price' => 34.56, 'name' => 'Produto 89', 'barcode' => '100000089'],
    ['price' => 82.14, 'name' => 'Produto 90', 'barcode' => '100000090'],
    ['price' => 27.49, 'name' => 'Produto 91', 'barcode' => '100000091'],
    ['price' => 53.12, 'name' => 'Produto 92', 'barcode' => '100000092'],
    ['price' => 95.77, 'name' => 'Produto 93', 'barcode' => '100000093'],
    ['price' => 42.86, 'name' => 'Produto 94', 'barcode' => '100000094'],
    ['price' => 21.22, 'name' => 'Produto 95', 'barcode' => '100000095'],
    ['price' => 36.82, 'name' => 'Produto 96', 'barcode' => '100000096'],
    ['price' => 14.65, 'name' => 'Produto 97', 'barcode' => '100000097'],
    ['price' => 83.71, 'name' => 'Produto 98', 'barcode' => '100000098'],
    ['price' => 63.90, 'name' => 'Produto 99', 'barcode' => '100000099'],
    ['price' => 72.45, 'name' => 'Produto 100', 'barcode' => '100000100'],
];


try {

    $priceField = new Field('preco', 'price');
    $nameField = new Field('nome', 'name');
    $barcodeField = Field::barcode('barcode');

    $pageMargin = new PageMargin(rightMargin: 2, leftMargin: 2, topMargin: 0, centralMargin: 3);
    $etiquetaTemplate = new EtiquetaTemplate($template);

    $etiquetasSheet = new EtiquetasSheet($pageMargin, $etiquetaTemplate);

    $etiquetasSheet->setFields([$priceField, $nameField, $barcodeField]);

    $etiquetasSheet->setData($products);

    $etiquetasSheet->setColunas(3);

    $etiquetasSheet->render();

    $pdf = $etiquetasSheet->getPdf();

    file_put_contents('test.txt', $pdf);

    file_put_contents('test.pdf', $pdf);

    echo "PDF gerado com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}