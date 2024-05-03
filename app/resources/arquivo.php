<?php

$msg = "opa";
$myfile = fopen("sinistro.txt", "a");
fwrite($myfile, $msg . "\n");
fclose($myfile);
