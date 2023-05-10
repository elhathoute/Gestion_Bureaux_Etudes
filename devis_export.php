<?php
    include 'includes/config.php';
    include 'functions.php';
    include 'dompdf/autoload.inc.php';

    checkUrlVars("devis",$_GET['id']);
    checkUrlVars("client",$_GET['client_id']);
    // reference the Dompdf namespace
    use Dompdf\Dompdf;
    //**************** */
    //reference the Options namespace
    use Dompdf\Options;

    //set options to enable embedded PHP
    $options = new Options();
    $options->set('isPhpEnabled','true');

    // instantiate and use the dompdf class
    $dompdf = new Dompdf($options);

    ob_start();
    require ('devis-pdf-content.php');
    $html = ob_get_contents();
    ob_get_clean();

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');
    // $cp = array(288,100,360,360);
    // $dompdf->setPaper($cp);
    // Render the HTML as PDF
    $dompdf->render();
    
    //************** */
    $canvas = $dompdf->getCanvas();
    $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        

        // $canvas->set_opacity(.1,'Multiply');//Multiply means apply to all pages.
        // Specify watermark text
        $imageURL = 'images/doc_bg.png';
        $imgWidth = 550;
        $imgHeight = 810;
        //set image opacity
        // use (.8,Multiply)
        // $canvas->set_opacity(.8);

        
        
        //specify horizontal and vertical position
        $x = (($w-$imgWidth)/2);
        $y = (($h-$imgHeight)/2);
        // $y = (($h-$imgHeight)-10);

        //Add an image to the pdf


        $canvas->image($imageURL,$x,$y,$imgWidth,$imgHeight);
        

        $canvas->set_opacity(.8, "Normal");
    });


    
    // Output the generated PDF to Browser
    $dompdf->stream('Export_devis.pdf',['Attachment'=>0]);

?>