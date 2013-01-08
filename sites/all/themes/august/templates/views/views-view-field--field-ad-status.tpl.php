<?php

if(strpos($output, 'inactive') !== FALSE){
  print '<span class="btn btn-mini btn-danger"><i class="icon-remove"></i></span>';
  return;
}else{
  print '<span class="btn btn-mini btn-success"><i class="icon-ok"></i></span>';
  return;
}

print $output;

?>