<?php

switch(strip_tags($output)){
	case 'Pending':
		$output = '<span class="label label-warning">Pending</span>';
		break;
	case 'Processing':
		$output = '<span class="label label-info">Processing</span>';
		break;
	case 'Published':
		$output = '<span class="label label-success">Published</span>';
		break;
}

print $output;

?>