<?php
$areas_string = '["Area 1", "Area 2"]';
$areas_decoded = json_decode($areas_string, true);
$areas_to_save = json_encode($areas_decoded);
echo "Original: $areas_string\n";
echo "Encoded: $areas_to_save\n";
?>
