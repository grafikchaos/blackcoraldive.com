<?php
if(strpos($output, '✔') !== FALSE){
  print '<span class="btn btn-mini btn-success"><i class="icon-ok"></i></span>';
  return;
}

if(strpos($output, '✖') !== FALSE){
  print '<span class="btn btn-mini btn-danger"><i class="icon-remove"></i></span>';
  return;
}

print $output;

?>