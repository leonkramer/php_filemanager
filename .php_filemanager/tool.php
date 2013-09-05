#!/usr/bin/env php5
<?php

require_once("class/PasswordHash.php");

$phpass = new PasswordHash(8, FALSE);

echo $phpass->HashPassword($argv[1])."\n";

?>
