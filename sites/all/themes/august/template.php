<?php

/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function august_preprocess_node(&$vars) {
  
  // We don't ever need this in the admin side
  unset($vars['content']['links'], $vars['content']['sharethis']);

}

/**
 * Implements hook_bs_button_class_alter
 */
function august_bs_button_class_alter(&$classes){
  $classes[t('Submit Entry')] = 'btn-primary';
  $classes[t('Processing')] = 'btn-info';
  $classes[t('Reset Password')] = 'btn-info';
  $classes[t('Pending')] = 'btn-warning';
  $classes[t('Publish')] = 'btn-success';
}

/**
 * Implements hook_bs_icons_alter
 */
function august_bs_icons_alter(&$icons){
  $icons['icon-plus'][] = t('Submit Entry');
  $icons['icon-plus'][] = t('Processing');
  $icons['icon-plus'][] = t('Pending');
  $icons['icon-plus'][] = t('Publish');
  $icons['icon-user'][] = t('Reset Password');
}


/**
 * Implements hook_form_description_tip_exclude
 */
function august_form_element($vars) {
  $element = &$vars['element'];
  if(arg(0) == 'node' && (arg(1) == 'add' || arg(2) == 'edit')) $element['#show_description'] = TRUE;
  if(arg(1) == 'member' && arg(2) == 'entry' && is_numeric(arg(3))) $element['#show_description'] = TRUE;
  if(arg(0) == 'system' && arg(1) == 'ajax') $element['#show_description'] = TRUE;
  if(arg(0) == 'field_collection' && arg(1) == 'ajax') $element['#show_description'] = TRUE;
  $output = bs_form_element($vars);

  return $output;
}

/**
 * Implements theme_node_add_list
 */
function august_node_add_list($variables) {
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

/**
 * Override the exposed home search to add certain classes and placholder text.
 */
function bs_form_alter(&$form, &$form_state, $form_id) {
  if($form_id == 'views_exposed_form'){
    $form['#attributes']['class'][] = 'form-inline';
  }
  
  if(isset($form['views_bulk_operations'])){
    $form['select']['#type'] = 'container';
    $form['select']['#attributes']['class'][] = 'well';
    $form['select']['#attributes']['class'][] = 'bulk-operations-container';
    $form['select']['#weight'] = 1000;
  }
  
  // Adds class to Associates Services field
  $form['field_services']['#attributes']['class'][] = 'chosen-selector';
}

/**
 * Preprocessor for theme('button').
 */
function august_preprocess_button(&$vars) {

  if (isset($vars['element']['#value'])) {
    $icons = array(
      t('Edit this field')
    );
    foreach ($icons as $search){
      if (strpos($vars['element']['#value'], $search) !== FALSE) {
        $vars['element']['#value'] = '';
        break;
      }
    }
  }
}

/**
 * Field handler to present a link to the node.
 *
 * @ingroup views_field_handlers
 */
if (arg(1) != 'structure' && arg(2) != 'views' && arg(6) != 'pdf') {
  class views_handler_field_node_link extends views_handler_field_entity {

    function option_definition() {
      $options = parent::option_definition();
      $options['text'] = array('default' => '', 'translatable' => TRUE);
      return $options;
    }

    function options_form(&$form, &$form_state) {
      $form['text'] = array(
        '#type' => 'textfield',
        '#title' => t('Text to display'),
        '#default_value' => $this->options['text'],
      );
      parent::options_form($form, $form_state);

      // The path is set by render_link function so don't allow to set it.
      $form['alter']['path'] = array('#access' => FALSE);
      $form['alter']['external'] = array('#access' => FALSE);
    }

    function render($values) {
      if ($entity = $this->get_value($values)) {
        return $this->render_link($entity, $values);
      }
    }

    function render_link($node, $values) {
      if (node_access('view', $node)) {
        $this->options['alter']['make_link'] = TRUE;
        $this->options['alter']['path'] = "node/$node->nid";
        $this->options['alter']['link_class'] = 'btn btn-mini';
        $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
        return $text;
      }
    }
  }
}

/**
 * Field handler to present a link node edit.
 *
 * @ingroup views_field_handlers
 */
if (arg(1) != 'structure' && arg(2) != 'views' && arg(6) != 'pdf') {
  class views_handler_field_node_link_edit extends views_handler_field_node_link {

    /**
     * Renders the link.
     */
    function render_link($node, $values) {
      // Ensure user has access to edit this node.
      if (!node_access('update', $node)) {
        return;
      }

      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = "node/$node->nid/edit";
      $this->options['alter']['query'] = drupal_get_destination();
      $this->options['alter']['link_class'] = 'btn btn-mini';

      $text = !empty($this->options['text']) ? $this->options['text'] : t('edit');
      return $text;
    }
  }
}

/**
 * Field handler to present a link to delete a node.
 *
 * @ingroup views_field_handlers
 */
if (arg(1) != 'structure' && arg(2) != 'views') {
  class views_handler_field_node_link_delete extends views_handler_field_node_link {

    /**
     * Renders the link.
     */
    function render_link($node, $values) {
      // Ensure user has access to delete this node.
      if (!node_access('delete', $node)) {
        return;
      }

      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = "node/$node->nid/delete";
      $this->options['alter']['query'] = drupal_get_destination();
      $this->options['alter']['link_class'] = 'btn btn-mini btn-danger';

      $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
      return $text;
    }
  }
}

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 */
if (arg(1) != 'structure' && arg(2) != 'views') {
  class views_handler_field_user_link_edit extends views_handler_field_user_link {
    function render_link($data, $values) {
      // Build a pseudo account object to be able to check the access.
      $account = new stdClass();
      $account->uid = $data;

      if ($data && user_edit_access($account)) {
        $this->options['alter']['make_link'] = TRUE;

        $text = !empty($this->options['text']) ? $this->options['text'] : t('edit');

        $this->options['alter']['path'] = "user/$data/edit";
        $this->options['alter']['query'] = drupal_get_destination();
        $this->options['alter']['link_class'] = 'btn btn-mini';

        return $text;
      }
    }
  }
}

function augustt_form_element($variables) {
  $element = &$variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Theme a drag-sortable table of fields in the exposed filter
 * @param unknown_type $form
 */
function august_views_dynamic_fields_sort_filter_fields($variables) {
  $form = $variables['form'];
  drupal_add_tabledrag('views-dynamic-fields-filters-table-sort-'.$form['#id'], 'order', 'self', 's');
  $header = array('Select', 'Field', 'Weight');
  $rows = array();
  foreach (element_children($form) as $key) {
    $row = array(); // This is important. We need to start with an empty element for the drag handle.
    // Add class to group weight fields for drag and drop.
    $form[$key]['s']['#attributes']['class'] = array('s');
    $row[] = drupal_render($form[$key]['c']);
    $parts = explode(':', $form[$key]['title']['#markup']);
    foreach($parts as &$part){
      switch(trim($part)){
        case 'Company':
        case 'Home':
          $part = '<span class="label">'.$part.'</span>';
          break;
        case 'Premium':
          $part = '<span class="label label-info">'.$part.'</span>';
          break;
        case 'Contact':
          $part = '<span class="label label-warning">'.$part.'</span>';
          break;
        case 'Address':
          $part = '<span class="label label-success">'.$part.'</span>';
          break;
        case 'Facebook':
          $part = '<i class="icon-facebook"></i> '.$part;
          break;
        case 'Twitter':
          $part = '<i class="icon-twitter"></i> '.$part;
          break;
        case 'YouTube':
          $part = '<i class="icon-facetime-video"></i> '.$part;
          break;
        case 'Flickr':
          $part = '<i class="icon-camera-retro"></i> '.$part;
          break;
        case 'Email':
          $part = '<i class="icon-envelope"></i> '.$part;
          break;
        case 'Phone':
        case 'Fax':
        case 'Phone 1':
        case 'Phone 2':
          $part = '<i class="icon-phone"></i> '.$part;
          break;
      }
    }
    $form[$key]['title']['#markup'] = implode(' ', $parts);
    $row[] = drupal_render($form[$key]['title']);
    $row[] = drupal_render($form[$key]['s']);
   $rows[] = array('data' => $row, 'class' => array('draggable')); // note the difference between $row and $rows
  }
  $output = theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'views-dynamic-fields-filters-table-sort-'.$form['#id'])));
  $output .= drupal_render_children($form);

  return $output;
}