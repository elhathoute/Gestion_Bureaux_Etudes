<?php
    include 'includes/config.php';
    include 'functions.php';
    include 'dompdf/autoload.inc.php';

    // reference the Dompdf namespace
    use Dompdf\Dompdf;
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    ob_start();
    require ('receipt-pdf-content.php');
    $html = ob_get_contents();
    ob_get_clean();

    $dompdf->loadHtml($html);
    

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');
    // $dompdf->setPaper('letter', 'landscape');
    // Render the HTML as PDF
    $dompdf->render();
    
    // Output the generated PDF to Browser
    $dompdf->stream('Export_recu.pdf',['Attachment'=>0]);

?>