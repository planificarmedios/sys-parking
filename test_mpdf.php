<?php
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/tmp/mpdf'
]);

$mpdf->WriteHTML('<h2>mPDF funcionando OK</h2>');
$mpdf->Output();
