<?php
/**
 * @file
 * Preprocess functions.
 */

/**
 * Implements hook_js_alter
 */
function rayne_js_alter(&$javascript) {
  // Override default drupal progress bar
  $path = drupal_get_path('theme', 'rayne');
  if(isset($javascript['misc/progress.js'])){
    $javascript[$path.'/scripts/rayne.theme.progress.js'] = $javascript['misc/progress.js'];
    $javascript[$path.'/scripts/rayne.theme.progress.js']['data'] = $path.'/scripts/rayne.theme.progress.js';
    unset($javascript['misc/progress.js']);
  }
}

/**
 * Implements hook_preprocess().
 */
function rayne_preprocess(&$variables) {
  // Consolidate attributes by moving classes into attributes_array.
  $variables['attributes_array']['class'] = $variables['classes_array'];
}

/**
 * Implements hook_preprocess_html().
 */
function rayne_preprocess_html(&$variables) {

	/**
	* Override or insert variables into the html template.
	*/
	// Add conditional CSS for IE8 and below.
  drupal_add_css(path_to_theme() . '/styles/rayne.ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 8', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE7 and below.
  drupal_add_css(path_to_theme() . '/styles/rayne.ie7.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  
  $active_themes = _rayne_theme_collector();
  _rayne_process_stylesheets($active_themes);


   // Try to load the library
/*
  if (module_exists('twitter_bootstrap_ui')){
    $library = libraries_load('twitter_bootstrap', 'minified');
  } 
*/

  // Switch the doctype to one that works better for RDF if the module is
  // enabled.
  $variables['doctype'] = (module_exists('rdf')) ? '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML+RDFa 1.1//EN">' : '<!DOCTYPE html>';

  $variables['skip_link'] = array(
    '#theme' => 'link',
    '#prefix' => '<div id="skip-link">',
    '#suffix' => '</div>',
    '#href' => '',
    '#text' => t('Skip to main content'),
    '#options' => array(
      'html' => FALSE,
      'fragment' => 'main-content',
      'attributes' => array(
        'class' => array(
          'element-invisible',
          'element-focusable',
        ),
      ),
    ),
  );
}

/**
 * Implements hook_preprocess_page().
 */
function rayne_preprocess_page(&$variables) {
  // Use a h1 tag for the site name when the content title is empty.
  $tag = (!empty($variables['title'])) ? 'div' : 'h1';
  // Make the site name into a renderable array.
  $variables['page']['site_name'] = array(
    '#theme' => 'link',
    '#href' => $variables['front_page'],
    '#options' => array(
      'attributes' => array(
        'title' => t('Home'),
        'rel' => 'home',
        'class' => array(
          'brand'
        ),
      ),
      'html' => TRUE,
    ),
    '#text' => '<span>' . $variables['site_name'] . '</span>',
    //'#prefix' => '<' . $tag . ' id="site-name">',
    //'#suffix' => '</' . $tag . '>',
  );

  // Make the logo into a renderable array.
  $logo_image = array(
    '#theme' => 'image',
    '#path' => $variables['logo'],
    '#alt' => t('Home'),
  );
  $variables['page']['logo'] = array(
    '#theme' => 'link',
    '#href' => $variables['front_page'],
    '#options' => array(
      'attributes' => array(
        'title' => t('Home'),
        'rel' => 'home',
        'id' => 'logo',
      ),
      'html' => TRUE,
    ),
    '#text' => render($logo_image),
  );

  // Make main and secondary menus into renderable arrays.
  $variables['page']['main_menu'] = array(
    '#theme' => 'links__system_main_menu',
    '#links' => $variables['main_menu'],
    '#attributes' => array(
      'id' => 'main-menu',
      'class' => array(
        'links',
        'inline',
        'clearfix',
      ),
    ),
    '#heading' => t('Main menu'),
  );
  $variables['page']['secondary_menu'] = array(
    '#theme' => 'links__system_secondary_menu',
    '#links' => $variables['secondary_menu'],
    '#attributes' => array(
      'id' => 'secondary-menu',
      'class' => array(
        'nav',
        'pull-right',
      ),
    ),
  );

  if(!empty($variables['action_links'])){
    $action_links = &$variables['action_links'];

    $link = $action_links[0]['#link'];
    unset($action_links[0]);
    $link['localized_options']['attributes']['class'][] = 'btn btn-primary btn-large';
    $link['localized_options']['html'] = TRUE;
    $links = array(
      '#theme' => 'link',
      '#text' => '<i class="icon-plus-sign icon-white"></i> '.$link['title'],
      '#path' => $link['href'],
      '#options' => $link['localized_options'] ? $link['localized_options'] : array(),
    );

    if(count($action_links) >= 1){
      $links['#suffix'] = '<button class="btn btn-primary btn-large dropdown-toggle" data-toggle="dropdown" style="line-height:18px"><span class="caret"></span></button>';
      $action_links['#prefix'] = '<ul class="action-links dropdown-menu">';
      $action_links['#suffix'] = '</ul>';
    }

    $action_links = array($links,$action_links);

    $action_links['#prefix'] = '<div class="btn-group pull-right">';
    $action_links['#suffix'] = '</div>';
  }

  
  // Replace tabs with dropw down version
  $variables['tabs']['#primary'] = _rayne_local_tasks($variables['tabs']['#primary']);
}

/**
 * Implements hook_preprocess_node().
 */
function rayne_preprocess_node(&$variables) {
  global $theme_key;

  $node = $variables['node'];

  // Make the title into a link and keep it as a renderable array.
  if (!empty($variables['title'])) {
    $variables['title_link'] = array(
      '#theme' => 'link',
      '#path' => 'node/' . $node->nid,
      '#text' => $variables['title'],
      '#options' => array(
        'html' => FALSE,
        'attributes' => array(),
      ),
    );
  }

  // Add some additional node classes.
  rayne_node_classes($variables);

  // Call THEME_preprocess_node_TYPE functions if any exist.
  $function = $theme_key . '_preprocess_node_' . $node->type;
  if (function_exists($function)) {
    call_user_func_array($function, array(&$variables));
  }
}

/**
 * Implements hook_preprocess_comment().
 */
function rayne_preprocess_comment(&$variables) {
  $comment = $variables['comment'];

  $variables['attributes_array']['id'] = 'comment-' . $comment->cid;
  $variables['attributes_array']['class'][] = $variables['status'];
  
  if ($comment->new) {
    $variables['attributes_array']['class'][] = 'comment-new';
  }
  
  if ($variables['zebra']) {
    $variables['attributes_array']['class'][] = 'comment-' . $variables['zebra'];
  }
}

/**
 * Implements hook_preprocess_block().
 */
function rayne_preprocess_block(&$variables) {
  $block = $variables['block'];
  
  $variables['attributes_array']['id'] = $variables['block_html_id'];
  $variables['title_attributes_array']['class'][] = 'block-title';
  $variables['content_attributes_array']['class'][] = 'block-content';
  $variables['content_attributes_array']['class'][] = 'clearfix';

  $variables['title'] = $block->subject;
}

/**
 * Implements hook_preprocess_table().
 */
function rayne_preprocess_table(&$variables) {
  if(empty($variables['attributes']['class'])){
    $variables['attributes']['class'][] = 'table-striped';
    $variables['attributes']['class'][] = 'table-bordered';
  }
  $variables['attributes']['class'][] = 'table';
}

function rayne_preprocess_table__field_collection_table(&$variables) {
  $variables['attributes']['class'][] = 'table';
  $variables['attributes']['class'][] = 'table-striped';
  $variables['attributes']['class'][] = 'table-bordered';
}

/**
 * Display a view as a table style.
 */
function rayne_preprocess_views_view_table(&$vars) {
  $vars['classes_array'][] = 'table';
  //$vars['classes_array'][] = 'table-striped';
  //$vars['classes_array'][] = 'table-bordered';
}

function rayne_preprocess_views_view_grid(&$vars) {
  $vars['class'] .= ' table';
}

/**
 * Returns HTML for primary and secondary local tasks.
 */
function rayne_menu_local_tasks(&$vars) {
  $output = '';

  if ( !empty($vars['primary']) ) {
    $vars['primary']['#prefix'] = '<ul class="nav nav-tabs">';
    $vars['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['primary']);
  }

  if ( !empty($vars['secondary']) ) {
    $vars['secondary']['#prefix'] = '<ul class="nav nav-pills tab-secondary">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
  }

  return $output;
}

/**
 * Preprocessor for theme('button').
 */
function rayne_preprocess_button(&$vars) {
  $vars['element']['#attributes']['class'][] = 'btn';

  if (isset($vars['element']['#value'])) {
    $classes = array(
      //specifics
      t('Save and add') => 'btn-info',
      t('Add another item') => 'btn-info',
      t('Add effect') => 'btn-primary',
      t('Add and configure') => 'btn-primary',
      t('Update style') => 'btn-primary',
      t('Download feature') => 'btn-primary',

      //generals
      t('Save') => 'btn-primary',
      t('Apply') => 'btn-primary',
      t('Add') => 'btn-primary',
      t('Create') => 'btn-primary',
      t('Continue') => 'btn-primary',
      t('Confirm') => 'btn-primary',
      t('Submit') => 'btn-primary',
      t('Export') => 'btn-primary',
      t('Import') => 'btn-primary',
      t('Restore') => 'btn-primary',
      t('Rebuild') => 'btn-primary',
      t('Search') => 'btn-primary',
      t('Add') => 'btn-info',
      t('Enable') => 'btn-success',
      t('Disable') => 'btn-warning',
      t('Block') => 'btn-warning',
      t('Update') => 'btn-info',
      t('Delete') => 'btn-danger',
      t('Remove') => 'btn-danger',
      t('Edit this field') => 'btn-mini',
      t('image') => 'btn-info',
    );
    foreach ($classes as $search => $class) {
      if (strpos($vars['element']['#value'], $search) !== FALSE) {
        $vars['element']['#attributes']['class'][] = $class;
        break;
      }
    }

    $icons = array(
      t('Delete') => 'icon-trash',
      t('Remove') => 'icon-trash',
      t('Save') => 'icon-plus',
      t('Add') => 'icon-plus',
      t('Create') => 'icon-plus',
      t('Update') => 'icon-refresh',
      t('Apply') => 'icon-refresh',
      t('Enable') => 'icon-plus-sign',
      t('Disable') => 'icon-minus-sign',
      t('Block') => 'icon-ban-circle',
      t('Continue') => 'icon-share-alt',
      t('Edit this field') => 'icon-edit',
      t('image') => 'icon-picture',
    );
    foreach ($icons as $search => $icon){
      if (strpos($vars['element']['#value'], $search) !== FALSE) {
        $vars['element']['#field_prefix'] = '<i class="'.$icon.' icon-white"></i> ';
        break;
      }
    }

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
 * Returns HTML for a button form element.
 */
function rayne_button($variables) {

  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  $label = check_plain($element['#value']);
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  $field_prefix = isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
  

  
  // Some elements do not work as buttons
  $skip = false;
  if(isset($element['#op']) && $element['#op'] == 'refresh_table') $skip = true;
  if(!empty($element['#no_button'])) $skip = true;
  //if(isset($element['#process']) && in_array('ajax_process_form', $element['#process'])) $skip = true;
  //if(isset($element['#process']) && in_array('ajax_process_form', $element['#process'])) $skip = true;
  if($skip){
    $element['#attributes']['type'] = 'submit';
    return '<input' . drupal_attributes($element['#attributes']) . ' />';
  }

  return '<button' . drupal_attributes($element['#attributes']) . '>'. $field_prefix . $label .'</button>
  '; // This line break adds inherent margin between multiple buttons
}

/**
 * Returns HTML for status and/or error messages, grouped by type.
 *
 */
function rayne_status_messages($vars) {
  $display = $vars['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"alert alert-block alert-$type\">\n";
    $output .= "  <a class=\"close\" data-dismiss=\"alert\" href=\"#\"><i class=\"icon-remove\"></i></a>\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="alert-heading element-invisible">' . $status_heading[$type] . "</h4>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Create a dropbutton menu.
 *
 * @param $title
 *   The text to place in the clickable area to activate the dropbutton. This
 *   text is indented to -9999px by default.
 * @param $links
 *   A list of links to provide within the dropbutton, suitable for use
 *   in via Drupal's theme('links').
 * @param $image
 *   If true, the dropbutton link is an image and will not get extra decorations
 *   that a text dropbutton link will.
 * @param $class
 *   An optional class to add to the dropbutton's container div to allow you
 *   to style a single dropbutton however you like without interfering with
 *   other dropbuttons.
 */
function rayne_links__ctools_dropbutton($vars) {
  // Check to see if the number of links is greater than 1;
  // otherwise just treat this like a button.
  if (!empty($vars['links'])) {
    $is_drop_button = (count($vars['links']) > 1);

    // Add needed files
    if ($is_drop_button) {
      ctools_add_js('dropbutton');
      ctools_add_css('dropbutton');
    }
    ctools_add_css('button');

    // Provide a unique identifier for every button on the page.
    static $id = 0;
    $id++;

    // Wrapping div
    $class = 'ctools-no-js';
    $class .= ' ctools-button';
    $class .= ($is_drop_button) ? ' btn-group' : '';
    if (!empty($vars['class'])) {
      $class .= ($vars['class']) ? (' ' . implode(' ', $vars['class'])) : '';
    }

    $output = '';

    $output .= '<div class="' . $class . '" id="ctools-button-' . $id . '">';

    // Add a twisty if this is a dropbutton
    if ($is_drop_button) {

      $first = array_shift($vars['links']);
      $attributes = isset($first['attributes']) ? $first['attributes'] : array();
      $attributes['class'][] = 'btn';
      $output .= l($first['title'], $first['href'], array('attributes'=>$attributes));

      $vars['title'] = ($vars['title'] ? check_plain($vars['title']) : t('open'));

      if ($vars['image']) {
        $output .= '<a href="#" class="ctools-twisty ctools-image">' . $vars['title'] . '</a>';
      }
      else {
        $output .= '<a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>';
      }

      $vars['attributes']['class'][] = 'dropdown-menu';
    }else{
      foreach($vars['links'] as &$link){
        $link['attributes']['class'][] = 'btn';
      }
    }

    // The button content

    // Check for attributes. theme_links expects an array().
    $vars['attributes'] = (!empty($vars['attributes'])) ? $vars['attributes'] : array();

    // Remove the inline and links classes from links if they exist.
    // These classes are added and styled by Drupal core and mess up the default
    // styling of any link list.
    if (!empty($vars['attributes']['class'])) {
      $classes = $vars['attributes']['class'];
      foreach ($classes as $key => $class) {
        if ($class === 'inline' || $class === 'links') {
          unset($vars['attributes']['class'][$key]);
        }
      }
    }

    // Call theme_links to render the list of links.
    $output .= theme_links(array('links' => $vars['links'], 'attributes' => $vars['attributes'], 'heading' => ''));
    $output .= '</div>'; // ctools-dropbutton
    return $output;
  }
  else {
    return '';
  }
}

function rayne_views_ui_display_tab_alter(&$build, $view, $display_id){
  if(isset($build['details']['top'])){
    $top = &$build['details']['top'];
    $top['actions']['path']['#theme'] = 'button';
    $top['actions']['path']['#value'] = 'View';
    $top['actions']['path']['#button_type'] = 'button';
    unset($top['actions']['path']['#prefix'],$top['actions']['path']['#suffix']);
    $top['actions']['#prefix'] = '<div class="ctools-button btn-group ctools-button-processed">'.drupal_render($top['actions']['path']).'<a data-toggle="dropdown" class="btn dropdown-toggle" href="#"><span class="caret"></span></a><div class="ctools-content"><ul class="horizontal right actions dropdown-menu">';
// <div id="ctools-button-9" class="ctools-button btn-group ctools-button-processed RemoveIconClass-processed"><a class="btn" href="/admin/structure/views/nojs/edit-details/management_member_types">edit view name/description</a><a data-toggle="dropdown" class="btn dropdown-toggle" href="#"><span class="caret"></span></a><ul class="dropdown-menu"><li class="analyze first"><a class="views-ajax-link views-ajax-processed-processed" href="/admin/structure/views/nojs/analyze/management_member_types/page_1">analyze view</a></li>
// <li class="clone"><a href="/admin/structure/views/view/management_member_types/clone">clone view</a></li>
// <li class="export"><a href="/admin/structure/views/view/management_member_types/export">export view</a></li>
// <li class="reorder"><a class="views-ajax-link views-ajax-processed-processed" href="/admin/structure/views/nojs/reorder-displays/management_member_types/page_1">reorder displays</a></li>
// <li class="delete last"><a href="/admin/structure/views/view/management_member_types/delete">delete view</a></li>
// </ul></div>
  }
}

/**
* Get all primary tasks including subsets
*/
function _rayne_local_tasks($tabs = FALSE) {
  if($tabs == '')
  return $tabs;
  
  if(!$tabs)
  $tabs = menu_primary_local_tasks();
  
  foreach($tabs as $key => $element) {
  $result = db_select('menu_router', NULL, array('fetch' => PDO::FETCH_ASSOC))
    ->fields('menu_router')
    ->condition('tab_parent', $element['#link']['path'])
    ->condition('context', MENU_CONTEXT_INLINE, '<>')
    ->condition('type', array(MENU_DEFAULT_LOCAL_TASK, MENU_LOCAL_TASK), 'IN')
    ->orderBy('weight')
    ->orderBy('title')
    ->execute();
  
  $router_item = menu_get_item($element['#link']['path']);
  $map = $router_item['original_map'];
  
  $i = 0;
  foreach ($result as $item) {
    _menu_translate($item, $map, TRUE);
  
    //only add items that we have access to
    if ($item['tab_parent'] && $item['access']) {
    //set path to that of parent for the first item
    if ($i === 0) {
      $item['href'] = $item['tab_parent'];
    }
  
    if (current_path() == $item['href']) {
      $tabs[$key][] = array(
      '#theme' => 'menu_local_task',
      '#link' => $item,
      '#active' => TRUE,
      );
    }
    else {
      $tabs[$key][] = array(
      '#theme' => 'menu_local_task',
      '#link' => $item,
      );
    }
  
    //only count items we have access to.
    $i++;
    }
  }
  }
  
  return $tabs;
}

/**
 * Returns HTML for an element's children fieldsets as vertical tabs.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties and children of
 *     the fieldset. Properties used: #children.
 *
 * @ingroup themeable
 */
function rayne_vertical_tabs($variables) {
  $element = $variables['element'];
  // Add required JavaScript and Stylesheet.
  drupal_add_library('system', 'drupal.vertical-tabs');
  drupal_add_js(drupal_get_path('theme', 'rayne').'/scripts/rayne.vertical-tabs.js');

  $output = '<h2 class="element-invisible">' . t('Vertical Tabs') . '</h2>';
  $output .= '<div class="vertical-tabs-panes">' . $element['#children'] . '</div>';
  return $output;
}

/**
 * Field handler to present a link to the node.
 *
 * @ingroup views_field_handlers
 */
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

/**
 * Field handler to present a link node edit.
 *
 * @ingroup views_field_handlers
 */
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

/**
 * Field handler to present a link to delete a node.
 *
 * @ingroup views_field_handlers
 */
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

/**
 * Returns HTML for an administrative block for display.
 *
 * @param $variables
 *   An associative array containing:
 *   - block: An array containing information about the block:
 *     - show: A Boolean whether to output the block. Defaults to FALSE.
 *     - title: The block's title.
 *     - content: (optional) Formatted content for the block.
 *     - description: (optional) Description of the block. Only output if
 *       'content' is not set.
 *
 * @ingroup themeable
 */
function rayne_admin_block($variables) {
  $block = $variables['block'];
  $output = '';

  // Don't display the block if it has no content to display.
  if (empty($block['show'])) {
    return $output;
  }

  $output .= '<div class="admin-panel">';
  if (!empty($block['title'])) {
    $output .= '<h3>' . $block['title'] . '</h3>';
  }
  if (!empty($block['content'])) {
    $output .= '<div class="body well">' . $block['content'] . '</div>';
  }
  else {
    $output .= '<div class="description">' . $block['description'] . '</div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for a fieldset form element and its children.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #children, #collapsed, #collapsible,
 *     #description, #id, #title, #value.
 *
 * @ingroup themeable
 */
function rayne_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form-wrapper'));
  
  $show_as_well = FALSE;
  if(empty($element['#group_fieldset'])) $show_as_well = TRUE;
  if(isset($element['#id']) && in_array($element['#id'], array('edit-additional-settings--2'))) $show_as_well = FALSE;

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend class=""><span class="fieldset-legend">' . $element['#title'];

/*
    if (empty($variables['element']['#group_fieldset']) && !empty($element['#description'])) {
      $output .= '<small>' . $element['#description'] . '</small>';
    }
*/
    
    $output .= '</span></legend>';
  }
  $class = 'fieldset-wrapper';
  if($show_as_well){
    $class .= ' well';
  }
  $output .= '<div class="'.$class.'">';
  if (!empty($element['#group_fieldset']) && !empty($element['#description'])) {
    $output .= '<div class="fieldset-description">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= '</div>';
  $output .= "</fieldset>\n";
  return $output;
}

/**
 * Override the exposed home search to add certain classes and placholder text.
 */
function rayne_form_alter(&$form, &$form_state, $form_id) {
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

// function rayne_radios($variables) {
//   $element = $variables['element'];
//   $attributes = array();

//   if (isset($element['#id'])) {
//     $attributes['id'] = $element['#id'];
//   }
//   $attributes['class'] = 'form-radios';
//   if (!empty($element['#attributes']['class'])) {
//     $attributes['class'] .= ' ' . implode(' ', $element['#attributes']['class']);
//   }
//   if (isset($element['#attributes']['title'])) {
//     $attributes['title'] = $element['#attributes']['title'];
//   }

//   $attributes['class'] .= ' btn-group';
//   $attributes['data-toggle-name'] = 'is_private';
//   $attributes['data-toggle'] = 'buttons-radio';

//   $input = '<input type="hidden" name="is_private" value="0" />';

//   return '<div' . drupal_attributes($attributes) . '>' . (!empty($element['#children']) ? $element['#children'] : '') . $input . '</div>';
// }

// function rayne_radio($variables) {
//   $element = $variables['element'];
//   $element['#attributes']['type'] = 'radio';
//   element_set_attributes($element, array('id', 'name','#return_value' => 'value'));

//   if (isset($element['#return_value']) && $element['#value'] !== FALSE && $element['#value'] == $element['#return_value']) {
//     //$element['#attributes']['checked'] = 'checked';
//     $element['#attributes']['class'][] = 'active';
//   }
//   _form_set_class($element, array('form-radio'));

//   $element['#type'] = 'button';
//   $element['#attributes']['class'][] = 'btn';
//   return '<button data-toggle="button" ' . drupal_attributes($element['#attributes']) . '>'.$element['#title'].'</button>';
//   return '<input' . drupal_attributes($element['#attributes']) . ' />';
// }

// function rayne_form_element($variables) {
//   $element = &$variables['element'];
//   // This is also used in the installer, pre-database setup.
//   $t = get_t();

//   // This function is invoked as theme wrapper, but the rendered form element
//   // may not necessarily have been processed by form_builder().
//   $element += array(
//     '#title_display' => 'before',
//   );

//   // Add element #id for #type 'item'.
//   if (isset($element['#markup']) && !empty($element['#id'])) {
//     $attributes['id'] = $element['#id'];
//   }
//   // Add element's #type and #name as class to aid with JS/CSS selectors.
//   $attributes['class'] = array('form-item');
//   if (!empty($element['#type'])) {
//     $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
//   }
//   if (!empty($element['#name'])) {
//     $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
//   }
//   // Add a class for disabled elements to facilitate cross-browser styling.
//   if (!empty($element['#attributes']['disabled'])) {
//     $attributes['class'][] = 'form-disabled';
//   }
//   $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

//   // If #title is not set, we don't display any label or required marker.
//   if (!isset($element['#title'])) {
//     $element['#title_display'] = 'none';
//   }
//   $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
//   $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

//   // Bootstrap override
//   if($element['#type'] == 'radio'){
//     return $element['#children'];
//   }

//   switch ($element['#title_display']) {
//     case 'before':
//     case 'invisible':
//       $output .= ' ' . theme('form_element_label', $variables);
//       $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
//       break;

//     case 'after':
//       $output .= ' ' . $prefix . $element['#children'] . $suffix;
//       $output .= ' ' . theme('form_element_label', $variables) . "\n";
//       break;

//     case 'none':
//     case 'attribute':
//       // Output no label and no required marker, only the children.
//       $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
//       break;
//   }

//   if (!empty($element['#description'])) {
//     $output .= '<div class="description">' . $element['#description'] . "</div>\n";
//   }

//   $output .= "</div>\n";

//   return $output;
// }
