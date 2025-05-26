<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';
adminOnly();

require __DIR__.'/../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$html = generateReportHtml($conn); // Create this function

$mpdf->WriteHTML($html);
$mpdf->Output('mlm_report.pdf', 'D');
