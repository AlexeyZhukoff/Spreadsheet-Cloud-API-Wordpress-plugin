<?php
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public, no-cache');
    header('Content-Length: ' . strlen($downloadresponse['data']));
    echo $downloadresponse['data'];  
    exit();          
?>