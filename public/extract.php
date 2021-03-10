<?php

$zip = new ZipArchive;
$res = $zip->open('vendor.zip');
if ($res === TRUE) {
  $zip->extractTo(__DIR__);
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}
