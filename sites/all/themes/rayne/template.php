<?php
/**
 * @file
 * Includes the library of functions.
 */

include('includes/rayne.functions.inc');
include('includes/rayne.preprocess.inc');

/**
 * Implements theme_node_add_list
 */
function rayne_node_add_list($variables) {
  $content = $variables['content'];
  $output = '';

  if ($content) {
    $output = '<dl class="node-type-list">';
    foreach ($content as $item) {
    	if(!user_access('administer_content') && in_array($item['path'], array('node/add/property'))) continue;
      $output .= '<dt>' . l($item['title'], $item['href'], $item['localized_options']) . '</dt>';
      $output .= '<dd>' . filter_xss_admin($item['description']) . '</dd>';
    }
    $output .= '</dl>';
  }
  else {
    $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
  }
  return $output;
}
