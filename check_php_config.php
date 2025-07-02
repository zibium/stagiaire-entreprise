<?php
echo "auto_prepend_file: " . ini_get('auto_prepend_file') . "\n";
echo "auto_append_file: " . ini_get('auto_append_file') . "\n";
echo "include_path: " . ini_get('include_path') . "\n";
echo "Current working directory: " . getcwd() . "\n";
echo "Script filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
?>