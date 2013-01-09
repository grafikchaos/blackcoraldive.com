<?php

/**
 * Remove core stylesheets
 *
 * @param $css
 *   An array of stylesheets
 */
function crest_css_alter(&$css) {
  // Remove defaults.css file.
  unset($css[drupal_get_path('module', 'system') . '/system.menus.css']);
  unset($css[drupal_get_path('module', 'system') . '/system.admin.css']);
  unset($css[drupal_get_path('module', 'filter') . '/filter.css']);
  unset($css['misc/vertical-tabs.css']);
}

/**
 * Override or insert variables into the html template.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function crest_preprocess_html(&$vars) {
  // give <body> tag a unique class depending on PATHs
  $path_alias = strtolower(drupal_clean_css_identifier(drupal_get_path_alias($_GET['q'])));
  if ($path_alias == 'node') {
    $vars['classes_array'][] = '';
  }
  else {
    $vars['classes_array'][] = 'path-'. $path_alias;
  }

  // Remove any HTML from page title
  $vars['head_title'] = strip_tags(html_entity_decode($vars['head_title']));

  // Add to the array of body classes
  // layout classes
  $vars['classes_array'][] = 'layout-'. (!empty($vars['page']['sidebar_first']) ? 'first-main' : 'main') . (!empty($vars['page']['sidebar_second']) ? '-second' : '');
}


/**
 * Override or insert variables into the page template.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function crest_preprocess_page(&$vars) {

  $vars['classes_array'][] = 'container-fluid';
  $vars['classes_row'] = 'row-fluid';
  $vars['classes_sidebar'] = '';
  $vars['powered_by'] = '<span>Grafikchaos</span>';
  $vars['powered_url'] = 'http://www.grafikchaos.com';

  if(!empty($vars['page']['sidebar_first']) && !empty($vars['page']['sidebar_second'])){
    // Two sidebars
    $vars['sidebar_span'] = 3;
    $vars['content_span'] = 6;
  }elseif(!empty($vars['page']['sidebar_first']) || !empty($vars['page']['sidebar_second'])){
    // One sidebar
    $vars['sidebar_span'] = 3;
    $vars['content_span'] = 9;
  }else{
    // No sidebars
    $vars['sidebar_span'] = 0;
    $vars['content_span'] = 12;
  }

  $vars['shortcut_add_remove'] = '';
  if(!module_exists('bs_toolbar')){
    if(isset($vars['title_suffix']['add_or_remove_shortcut'])){
      if(!$vars['is_front']) $vars['shortcut_add_remove'] = $vars['title_suffix']['add_or_remove_shortcut'];
      unset($vars['title_suffix']['add_or_remove_shortcut']);
    }
  }
}


/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function crest_preprocess_node(&$vars) {

  // Allow for custom templates by view mode
  $vars['theme_hook_suggestions'][] = 'node__' . $vars['view_mode'];
  $vars['theme_hook_suggestions'][] = 'node__' . $vars['node']->type . '__' . $vars['view_mode'];

  $vars['classes_array'][] = $vars['zebra'];
  if ($vars['view_mode'] == 'full') {
    $vars['classes_array'][] = 'node-full';
  }

  // Add node-type-page template suggestion
  if ($vars['page']) {
    $vars['theme_hook_suggestions'][] = 'node__'. $vars['node']->type .'_page';
    $vars['theme_hook_suggestions'][] = 'node__'. $vars['node']->type .'-'. $vars['node']->nid .'_page';
  }
  else {
    $vars['theme_hook_suggestions'][] = 'node__'. $vars['node']->type .'_teaser';
    $vars['theme_hook_suggestions'][] = 'node__'. $vars['node']->nid;
  }
}


/**
 * Preprocess variables for region.tpl.php
 *
 * Prepare the values passed to the theme_region function to be passed into a
 * pluggable template engine. Uses the region name to generate a template file
 * suggestions. If none are found, the default region.tpl.php is used.
 *
 * @see region.tpl.php
 */
function crest_preprocess_region(&$vars) {
  // Sidebar region template suggestion.
  if (strpos($vars['region'], 'sidebar_') === 0) {
    $vars['theme_hook_suggestions'][] = 'region__sidebar';
    $vars['theme_hook_suggestions'][] = 'region__' . $vars['region'];
  }
}


/**
 * Override or insert variables into the block templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function crest_preprocess_block(&$vars) {
  $block = $vars['block'];
  // First/last block position
  $vars['position'] = ($vars['block_id'] == 1) ? 'first' : '';
  if ($vars['block_id'] == count(block_list($block->region))) {
    $vars['position'] = ($vars['position']) ? '' : 'last';
  }
}


/**
 * Override or insert variables into the comment templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function crest_preprocess_comment(&$vars) {
  // Add odd/even classes to comments classes_array
  static $comment_odd = TRUE;
  $vars['classes_array'][] = $comment_odd ? 'odd' : 'even';
  $comment_odd = !$comment_odd;
}

function crest_form_element($vars) {
  $element = &$vars['element'];
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

  if (!empty($element['#description']) && empty($element['#show_description'])) {
    $attributes['class'][] = 'has-description';
    $attributes['data-title'] = $element['#description'];
    $attributes['data-placement'] = 'bottom';
    $element['#field_suffix'] = empty($element['#field_suffix']) ? '' : $element['#field_suffix'];
    $tip = '&nbsp;<span class="form-tip-wrapper"><i class="icon-info-sign form-tip"></i></span>';
    //$attributes['data-trigger'] = 'manual';
    switch($element['#type']){
      case 'select':
        $attributes['data-selector'] = 'select';
        $attributes['data-placement'] = 'right';
        break;
      case 'checkboxes':
        $attributes['data-selector'] = '.form-checkboxes';
        $attributes['data-placement'] = 'right';
        break;
      case 'radios':
        $attributes['data-selector'] = '.form-radios';
        $attributes['data-placement'] = 'right';
        break;
      case 'textfield':
      case 'checkbox':
      case 'managed_file':
        $element['#field_suffix'] .= $tip;
        $attributes['data-hover'] = '.form-tip-wrapper';
        $attributes['data-selector'] = '.form-tip';
        $attributes['data-placement'] = 'right';
        break;
    }
  }

  $output = '';
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
      $output .= ' ' . theme('form_element_label', $vars);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $vars) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description']) && !empty($element['#show_description'])) {
    $output .= '<div class="description clearfix">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

function crest_radios($variables) {
  $element = $variables['element'];
  $attributes = array();
  if (isset($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  $attributes['class'][] = 'form-radios';
  $attributes['class'][] = 'inline';
  if (!empty($element['#attributes']['class'])) {
    $attributes['class'] .= ' ' . implode(' ', $element['#attributes']['class']);
  }
  if (isset($element['#attributes']['title'])) {
    $attributes['title'] = $element['#attributes']['title'];
  }
  return '<div' . drupal_attributes($attributes) . '>' . (!empty($element['#children']) ? $element['#children'] : '') . '</div>';
}

function crest_checkboxes($variables) {
  $element = $variables['element'];
  $attributes = array();
  if (isset($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  $attributes['class'][] = 'form-checkboxes';
  $attributes['class'][] = 'inline';
  if (!empty($element['#attributes']['class'])) {
    $attributes['class'] = array_merge($attributes['class'], $element['#attributes']['class']);
  }
  if (isset($element['#attributes']['title'])) {
    $attributes['title'] = $element['#attributes']['title'];
  }
  return '<div' . drupal_attributes($attributes) . '>' . (!empty($element['#children']) ? $element['#children'] : '') . '</div>';
}

function crest_container($vars) {
  $element = $vars['element'];

  // Special handling for form elements.
  if (isset($element['#array_parents'])) {
    // Assign an html ID.
    if (!isset($element['#attributes']['id'])) {
      $element['#attributes']['id'] = $element['#id'];
    }
    // Add the 'form-wrapper' class.
    $element['#attributes']['class'][] = 'form-wrapper';
  }

  //return $element['#children'];
  return '<div' . drupal_attributes($element['#attributes']) . '>' . $element['#children'] . '</div>';
}

/**
 * Implements hook_preprocess_table().
 */
function crest_preprocess_table(&$vars) {
  if(empty($vars['attributes']['class'])){
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['attributes']['class'][] = 'table';
}

/**
 * Display a view as a table style.
 */
function crest_preprocess_views_view_table(&$vars) {
  if(empty($vars['attributes']['class'])){
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['classes_array'][] = 'table';
}

function crest_preprocess_views_view_grid(&$vars) {
  $vars['class'] .= ' table';
}

function crest_preprocess_views_flipped_table(&$vars) {
  $vars['classes_array'][] = 'table';
}

function crest_preprocess_table__field_collection_table(&$variables) {
  $variables['attributes']['class'][] = 'table';
  $variables['attributes']['class'][] = 'table-striped';
  $variables['attributes']['class'][] = 'table-bordered';
}

function crest_pager($vars) {
  $tags = $vars['tags'];
  $element = $vars['element'];
  $parameters = $vars['parameters'];
  $quantity = $vars['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('« first')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('last »')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => array('pager-first'),
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => array('pager-ellipsis'),
          'data' => '<a onclick="return false;" href="#">…</a>',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('pager-current active'),
            'data' => "<a href=\"#\">$i</a>",
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array('pager-ellipsis disabled'),
          'data' => '<a onclick="return false;" href="#">…</a>',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array('pager-next'),
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => array('pager-last'),
        'data' => $li_last,
      );
    }
    return '<div class="pagination pagination-centered">'.theme('item_list', array(
      'items' => $items,
    )).'</div>';
  }
}

function crest_menu_tree($variables) {
  return '<ul class="menu nav nav-list">' . $variables['tree'] . '</ul>';
}

function crest_menu_link($v) {
  $element = &$v['element'];
  $atts = &$v['element']['#attributes']; //alias
  if( isset($atts['class'])
      && in_array('active-trail',$atts['class']) ) {
    $atts['class'][] = 'active';
  }

  if(module_exists('bs_core') && empty($element['#no_icon'])){
    if(!empty($element['#icon'])){
      $element['#title'] = bs_icons_format($element['#icon']).$element['#title'];
      $element['#localized_options']['html'] = TRUE;
    }
    else if($icon = bs_icons_attach($element['#title'])){
      $element['#title'] = $icon.$element['#title'];
      $element['#localized_options']['html'] = TRUE;
    }
  }

  return theme_menu_link($v);
}

function crest_menu_local_task__secondary($vars) {
  $link = $vars['element']['#link'];
  $link_text = $link['title'];

  if (!empty($vars['element']['#active'])) {
    // Add text to indicate active tab for non-visual users.
    $active = '<span class="element-invisible">' . t('(active tab)') . '</span>';

    // If the link does not contain HTML already, check_plain() it now.
    // After we set 'html'=TRUE the link will not be sanitized by l().
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title!active', array('!local-task-title' => $link['title'], '!active' => $active));
  }

  $link['localized_options']['attributes']['class'][] = 'btn';
  return l($link_text, $link['href'], $link['localized_options']) . "\n";
}

function crest_fieldset($vars) {
  $element = $vars['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form-wrapper'));
  if(!empty($element['#collapsible']) && empty($element['#group'])){
    $collapse = empty($element['#collapsed']) ? 'in' : '';
    $title = isset($element['#title']) ? $element['#title'] : 'Toggle';
    $element['#attributes']['class'][] = 'accordion';
    $output = '<div' . drupal_attributes($element['#attributes']) . '>';
    $output .= ' <div class="accordion-group">';
    $output .= '   <div class="accordion-heading">';
    $output .= '     <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$element['#id'].'" href="#'.$element['#id'].'-content">'.$title.'</a>';
    $output .= '   </div>';
    $output .= '   <div id="'.$element['#id'].'-content" class="accordion-body collapse in">';
    $output .= '     <div class="accordion-inner">';
    $output .= $element['#children'];
    if (isset($element['#value'])) {
      $output .= $element['#value'];
    }
    $output .= '     </div>';
    $output .= '   </div>';
    $output .= ' </div>';
    $output .= '</div>';
  }else{
    $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
    if (!empty($element['#title'])) {
      // Always wrap fieldset legends in a SPAN for CSS positioning.
      $output .= '<legend><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
    }
    $output .= '<div class="fieldset-wrapper clearfix">';
    if (!empty($element['#description'])) {
      $output .= '<div class="fieldset-description">' . $element['#description'] . '</div>';
    }
    $output .= '<div class="well well-small">';
    $output .= $element['#children'];
    if (isset($element['#value'])) {
      $output .= $element['#value'];
    }
    $output .= '</div>';
    $output .= '</div>';
    $output .= "</fieldset>\n";
  }
  return $output;
}

function crest_status_messages($vars) {
  $display = $vars['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"alert alert-$type\">\n";
    $output .= " <a class=\"close\" data-dismiss=\"alert\" href=\"#\"><i class=\"icon-remove\"></i></a>\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="alert-heading element-invisible">' . $status_heading[$type] . "</h4>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= ' <li>' . $message . "</li>\n";
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
 * The text to place in the clickable area to activate the dropbutton. This
 * text is indented to -9999px by default.
 * @param $links
 * A list of links to provide within the dropbutton, suitable for use
 * in via Drupal's theme('links').
 * @param $image
 * If true, the dropbutton link is an image and will not get extra decorations
 * that a text dropbutton link will.
 * @param $class
 * An optional class to add to the dropbutton's container div to allow you
 * to style a single dropbutton however you like without interfering with
 * other dropbuttons.
 */
function crest_links__ctools_dropbutton($vars) {
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
      $attributes['class'][] = 'btn-mini';
      $output .= l($first['title'], $first['href'], array('attributes'=>$attributes));

      $vars['title'] = ($vars['title'] ? check_plain($vars['title']) : t('open'));

      if ($vars['image']) {
        $output .= '<a href="#" class="ctools-twisty ctools-image">' . $vars['title'] . '</a>';
      }
      else {
        $output .= '<a href="#" class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>';
      }

      $vars['attributes']['class'][] = 'dropdown-menu';
    }else{
      foreach($vars['links'] as &$link){
        $link['attributes']['class'][] = 'btn';
        $link['attributes']['class'][] = 'btn-mini';
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
    //if(count($vars['links']) > 1){
      $output .= theme_links(array('links' => $vars['links'], 'attributes' => $vars['attributes'], 'heading' => ''));
    //}else{
      //$link = array_pop($vars['links']);
      //$output .= l($link['title'], $link['href'], array('attributes'=>$link['attributes'], 'html'=>$link['html']));
    //}
    $output .= '</div>'; // ctools-dropbutton
    return $output;
  }
  else {
    return '';
  }
}

function crest_views_ui_display_tab_alter(&$build, $view, $display_id){
  if(isset($build['details']['top'])){
    $top = &$build['details']['top'];
    $top['actions']['path']['#theme'] = 'button';
    $top['actions']['path']['#value'] = 'View';
    $top['actions']['path']['#button_type'] = 'button';
    unset($top['actions']['path']['#prefix'],$top['actions']['path']['#suffix']);
    $top['actions']['#prefix'] = '<div class="ctools-button btn-group ctools-button-processed">'.drupal_render($top['actions']['path']).'<a data-toggle="dropdown" class="btn dropdown-toggle" href="#"><span class="caret"></span></a><div class="ctools-content"><ul class="horizontal right actions dropdown-menu">';
  }
}

function crest_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];

  if (!empty($breadcrumb)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $breadcrumb[] = '<span class="active">'.strip_tags(htmlspecialchars_decode(drupal_get_title())).'</span>';

    $output .= implode(' <span class="divider">/</span> ', $breadcrumb);
    return $output;
  }
}

/**
 * Preprocessor for theme('button').
 */
function crest_preprocess_button(&$vars) {
  $vars['element']['#attributes']['class'][] = 'btn';

  if (isset($vars['element']['#value'])) {
    if(module_exists('bs_core') && $class = bs_button_class_attach($vars['element']['#value'])){
      $vars['element']['#attributes']['class'][] = $class;
    }

    if(module_exists('bs_core') && $icon = bs_icons_attach($vars['element']['#value'])){
      $vars['element']['#field_prefix'] = $icon;
    }
  }
}

/**
 * Returns HTML for a button form element.
 */
function crest_button($vars) {

  $element = $vars['element'];
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
  if(!empty($element['#id']) && in_array($element['#id'], array('views-add-group'))) $skip = true;
  if($skip){
    $element['#attributes']['type'] = 'submit';
    return '<input' . drupal_attributes($element['#attributes']) . ' />';
  }

  return '<button' . drupal_attributes($element['#attributes']) . '>'. $field_prefix . $label .'</button>
  '; // This line break adds inherent margin between multiple buttons
}

function crest_element_info_alter(&$type) {
  $type['text_format']['#process'][] = 'crest_filter_process_format';
}

function crest_filter_process_format($element) {

  if(!empty($element['format']['format']['#options'])){
    $element['format']['#attributes']['class'][] = 'filter-buttons-wrapper';
    $active = $element['format']['format']['#default_value'];
    $output = '<div class="btn-group filter-buttons" data-toggle="buttons-radio">';
    foreach($element['format']['format']['#options'] as $format => $title){
      $class = $format == $active ? ' active' : '';
      $output .= '<button data-format="'.$format.'" type="button" class="btn btn-mini'.$class.'">'.$title.'</button>';
    }
    $output .= '</div>';
    $element['format']['guidelines']['#weight'] = 0;
    $element['format']['format'] = array(
      'guidelines' => $element['format']['guidelines'],
      'format' => $element['format']['format'],
      'buttons' => array('#markup' => $output),
      //'help' => $element['format']['help'],
    );
    unset($element['format']['help'], $element['format']['guidelines']);
  }
  return $element;
}

function crest_filter_tips_more_info() {
  return '<div class="pull-rightt">' . l(t('More information about text formats'), 'filter/tips', array('attributes' => array('class'=>array('btn','btn-mini'), 'target' => '_blank'))) . '</div>';
}

function crest_text_format_wrapper($variables) {
  $element = $variables['element'];
  $output = '<div class="text-format-wrapper">';
  $output .= $element['#children'];
  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . '</div>';
  }
  $output .= "</div>\n";

  return $output;
}

function crest_filter_guidelines($variables) {
  $format = $variables['format'];
  $title = check_plain($format->name);
  $content = theme('filter_tips', array('tips' => _filter_tips($format->format, FALSE)));
  $attributes['class'][] = 'pull-right';
  $attributes['class'][] = 'filter-guidelines-item';
  $attributes['class'][] = 'filter-guidelines-' . $format->format;
  $output = '<span' . drupal_attributes($attributes) . '>';
  $output .= '<h3>' . check_plain($format->name) . '</h3>';
  if(!empty($content)){
    $output .= '<span class="btn btn-mini use-popover" data-title="'.$title.'" data-content="'.htmlspecialchars($content).'" data-placement="left">More Info</span>';
  }
  $output .= '</span>';
  return $output;
}

function crest_links($variables) {
  $links = $variables['links'];
  $attributes = $variables['attributes'];
  $inline = isset($attributes['class']) && in_array('inline', $attributes['class']) ? TRUE : FALSE;
  if($inline) $attributes['class'][] = 'btn-group';
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    if($inline)
      $output .= '<div' . drupal_attributes($attributes) . '>';
    else
      $output .= '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = array($key);

      if($inline) $link['attributes']['class'][] = 'btn';
      if($inline) $link['attributes']['class'][] = 'btn-mini';

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class[] = 'first';
      }
      if ($i == $num_links) {
        $class[] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
           && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $class[] = 'active';
      }
      if(!$inline)
        $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      if(!$inline)
        $output .= "</li>\n";
    }

    if($inline)
      $output .= '</div>';
    else
      $output .= '</ul>';
  }

  return $output;
}
