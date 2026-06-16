<?php
// Test script to see how $_POST handles JSON strings
$post_data = ['areas' => '["Area 1", "Area 2"]'];
// In a real POST request, areas would be the value.
echo "Areas: " . $post_data['areas'] . "\n";
$decoded = json_decode($post_data['areas'], true);
var_dump($decoded);
?>
