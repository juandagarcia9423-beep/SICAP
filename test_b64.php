<?php
$m = ['id'=>1, 'desc'=>"Test con ñ, á y 'comillas'"];
$json = json_encode($m); // ASCII safe if no JSON_UNESCAPED_UNICODE
$b64 = base64_encode($json);
echo $b64 . "\n";
echo base64_decode($b64) . "\n";
?>