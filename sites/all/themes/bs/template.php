<?php

/**
 * Remove core stylesheets
 *
 * @param $css
 *   An array of stylesheets
 */
function bs_css_alter(&$css) {
  // Remove defaults.css file.
  unset($css[drupal_get_path('module', 'system') . '/system.menus.css']);
  unset($css[drupal_get_path('module', 'system') . '/system.admin.css']);
  unset($css[drupal_get_path('module', 'filter') . '/filter.css']);
  unset($css['misc/vertical-tabs.css']);
}

/**
 * Implements hook_js_alter
 */
function bs_js_alter(&$javascript) {
  // Override default drupal progress bar
  $path = drupal_get_path('theme', 'bs');
  if(isset($javascript['misc/progress.js'])){
    $javascript[$path.'/assets/js/progress.js'] = $javascript['misc/progress.js'];
    $javascript[$path.'/assets/js/progress.js']['data'] = $path.'/assets/js/progress.js';
    unset($javascript['misc/progress.js']);
  }
}

/**
 * Alter a context directly after it has been loaded. Allows modules to alter
 * a context object's reactions. While you may alter conditions, this will
 * generally have no effect as conditions are cached for performance and
 * contexts are loaded after conditions are checked, not before.
 *
 * @param &$context
 *   The context object by reference.
 */
function bs_context_load_alter(&$context) {
  // Do not load global contexts
  if(!empty($context->conditions['sitewide'])){
    unset($context->reactions);
  }

  if(theme_get_setting('bs_context_disable')) unset($context->reactions);
}

/**
 * Override or insert variables into the html template.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function bs_preprocess_html(&$vars) {

  // BS Toolbar relocate
  if(!empty($vars['page']['page_top']['bs_toolbar'])){
    unset($vars['page']['page_top']['bs_toolbar']);
  }

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
function bs_preprocess_page(&$vars) {
  
  if(module_exists('bs_toolbar')){
    $vars['title'] = $vars['title_prefix'] = $vars['title_suffix'] = '';
  }

  // BS Toolbar relocate
  if(!empty($vars['page']['page_top']['bs_toolbar'])){
    $vars['page']['header']['bs_toolbar'] = $vars['page']['page_top']['bs_toolbar'];
    $vars['page']['header']['bs_toolbar']['#pre_render'][] = 'bs_bs_toolbar_pre_render';
  }

  $vars['classes_array'][] = 'container'.(theme_get_setting('bs_layout') == 'fixed' ? '' : '-fluid');
  $vars['classes_row'] = theme_get_setting('bs_layout') == 'fixed' ? 'row' : 'row-fluid';
  $vars['classes_sidebar'] = theme_get_setting('bs_sidebar_well') == 1 ? ' well' : '';
  $vars['powered_by'] = theme_get_setting('bs_powered_by') ? theme_get_setting('bs_powered_by') : '<span>Ashen</span> Rayne';
  $vars['powered_url'] = theme_get_setting('bs_powered_url') ? theme_get_setting('bs_powered_url') : 'http://www.ashenrayne.com';
  
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
}

function bs_bs_toolbar_pre_render($bs_toolbar) {
  $bs_toolbar['float_attributes'] = array();
  return $bs_toolbar;
}


/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 */
function bs_preprocess_node(&$vars) {
  
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
function bs_preprocess_region(&$vars) {
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
function bs_preprocess_block(&$vars) {
  $block = $vars['block'];
  switch($block->region){
    case 'sidebar_first':
    case 'sidebar_second':
      if(theme_get_setting('bs_sidebar_well') == 2) $vars['classes_array'][] = 'well';
      break;
  }
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
function bs_preprocess_comment(&$vars) {
  // Add odd/even classes to comments classes_array 
  static $comment_odd = TRUE;
  $vars['classes_array'][] = $comment_odd ? 'odd' : 'even';
  $comment_odd = !$comment_odd;
}

function bs_form_element($vars) {
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
    if(isset($element['#type'])){
      switch($element['#type']){
        // case 'select':
        //   $attributes['data-selector'] = 'select';
        //   $attributes['data-placement'] = 'right';
        //   break;
        // case 'checkboxes':
        //   $attributes['data-selector'] = '.form-checkboxes';
        //   $attributes['data-placement'] = 'right';
        //   break;
        // case 'radios':
        //   $attributes['data-selector'] = '.form-radios';
        //   $attributes['data-placement'] = 'right';
        //   break;
        case 'textfield':
        case 'checkbox':
        case 'managed_file':
        case 'select':
        case 'checkboxes':
        case 'radios':
          $element['#field_suffix'] .= $tip;
          $attributes['data-hover'] = '.form-tip-wrapper';
          $attributes['data-selector'] = '.form-tip';
          $attributes['data-placement'] = 'right';
          break;
      }
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

function bs_radios($variables) {
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

function bs_checkboxes($variables) {
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

function bs_container($vars) {
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
function bs_preprocess_table(&$vars) {
  if(isset($vars['attributes']['class']) && is_string($vars['attributes']['class'])) $vars['attributes']['class'] = array($vars['attributes']['class']);
  if(empty($vars['attributes']['class'])){
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['attributes']['class'][] = 'table';
}

/**
 * Display a view as a table style.
 */
function bs_preprocess_views_view_table(&$vars) {
  if(empty($vars['attributes']['class'])){
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['classes_array'][] = 'table';
}

function bs_preprocess_views_view_grid(&$vars) {
  $vars['class'] .= ' table';
}

function bs_preprocess_views_flipped_table(&$vars) {
  $vars['classes_array'][] = 'table';
}

function bs_preprocess_table__field_collection_table(&$variables) {
  $variables['attributes']['class'][] = 'table';
  $variables['attributes']['class'][] = 'table-striped';
  $variables['attributes']['class'][] = 'table-bordered';
}

function bs_pager($vars) {
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

function bs_menu_local_task__secondary($vars) {
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

function bs_fieldset($vars) {
  $element = $vars['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form-wrapper'));
  if(!empty($element['#collapsible']) && empty($element['#group'])){
    $collapse = empty($element['#collapsed']) ? 'in' : '';
    $title = isset($element['#title']) ? $element['#title'] : 'Toggle';
    $element['#attributes']['class'][] = 'accordion';
    $element['#id'] = empty($element['#id']) ? '' : $element['#id'];
    $element['#id'] = $element['#id'] . rand(10000,99999);
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

function bs_status_messages($vars) {
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
function bs_links__ctools_dropbutton($vars) {
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

function bs_views_ui_display_tab_alter(&$build, $view, $display_id){
  if(isset($build['details']['top'])){
    $top = &$build['details']['top'];
    $top['actions']['path']['#theme'] = 'button';
    $top['actions']['path']['#value'] = 'View';
    $top['actions']['path']['#button_type'] = 'button';
    $top['actions']['path']['#attributes']['class'][] = 'btn-mini';
    unset($top['actions']['path']['#prefix'],$top['actions']['path']['#suffix']);
    $top['actions']['#prefix'] = '<div class="pull-right ctools-button btn-group ctools-button-processed">'.drupal_render($top['actions']['path']).'<a data-toggle="dropdown" class="btn btn-mini dropdown-toggle" href="#"><span class="caret"></span></a><ul class="horizontal right actions dropdown-menu">';
    $top['actions']['#suffix'] = ' </ul></div>';
  }
}

function bs_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];

  if (!empty($breadcrumb)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $breadcrumb[] = '<span class="active">'.strip_tags(htmlspecialchars_decode(drupal_get_title())).'</span>';

    $output .= '<div class="breadcrumb">' . implode(' <span class="divider">/</span> ', $breadcrumb) . '</div>';
    return $output;
  }
}

/**
 * Preprocessor for theme('button').
 */
function bs_preprocess_button(&$vars) {
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
function bs_button($vars) {

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

/**
 * Returns HTML for an administrative page.
 *
 * @param $variables
 *   An associative array containing:
 *   - blocks: An array of blocks to display. Each array should include a
 *     'title', a 'description', a formatted 'content' and a 'position' which
 *     will control which container it will be in. This is usually 'left' or
 *     'right'.
 *
 * @ingroup themeable
 */
function bs_admin_page($variables) {
  $blocks = $variables['blocks'];

  $stripe = 0;
  $container = array();

  foreach ($blocks as $block) {
    if ($block_output = theme('admin_block', array('block' => $block))) {
      if (empty($block['position'])) {
        // perform automatic striping.
        $block['position'] = ++$stripe % 2 ? 'left' : 'right';
      }
      if (!isset($container[$block['position']])) {
        $container[$block['position']] = '';
      }
      $container[$block['position']] .= $block_output;
    }
  }

  $output = '<div class="admin">';
  $output .= theme('system_compact_link');
  $output .= '<div class="row-fluid">';

  foreach ($container as $id => $data) {
    $output .= '<div class="' . $id . ' span6">';
    $output .= $data;
    $output .= '</div>';
  }
  $output .= '</div>';
  $output .= '</div>';
  return $output;
}

function bs_admin_block($variables) {
  $block = $variables['block'];
  $output = '';

  // Don't display the block if it has no content to display.
  if (empty($block['show'])) {
    return $output;
  }

  $output .= '<div class="admin-panel">';
  if (!empty($block['title'])) {
    if(module_exists('bs_core') && empty($block['no_icon'])){
      if($icon = bs_icons_attach($block['title'])){
        $block['title'] = $icon.$block['title'];
      }
    }
    $output .= '<h3>' . $block['title'] . '</h3>';
  }
  if (!empty($block['content'])) {
    $output .= '<div class="body">' . $block['content'] . '</div>';
  }
  else {
    $output .= '<div class="description">' . $block['description'] . '</div>';
  }
  $output .= '</div>';

  return $output;
}

function bs_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';

  if (!empty($content)) {
    $class = 'admin-list';
    if ($compact = system_admin_compact_mode()) {
      $class .= ' compact';
    }
    $output .= '<dl class="' . $class . '">';
    foreach ($content as $item) {
      if(module_exists('bs_core') && empty($item['no_icon'])){
        if($icon = bs_icons_attach($item['title'])){
          $item['title'] = $icon.$item['title'];
          $item['localized_options']['html'] = TRUE;
        }
      }
      $output .= '<dt>' . l($item['title'], $item['href'], $item['localized_options']) . '</dt>';
      if (!$compact && isset($item['description'])) {
        $output .= '<dd>' . filter_xss_admin($item['description']) . '</dd>';
      }
    }
    $output .= '</dl>';
  }
  return $output;
}

function bs_element_info_alter(&$type) {
  $type['text_format']['#process'][] = 'bs_filter_process_format';
}

function bs_filter_process_format($element) {

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

function bs_filter_tips_more_info() {
  return '<div class="pull-rightt">' . l(t('More information about text formats'), 'filter/tips', array('attributes' => array('class'=>array('btn','btn-mini'), 'target' => '_blank'))) . '</div>';
}

function bs_text_format_wrapper($variables) {
  $element = $variables['element'];
  $output = '<div class="text-format-wrapper">';
  $output .= $element['#children'];
  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . '</div>';
  }
  $output .= "</div>\n";

  return $output;
}

function bs_filter_guidelines($variables) {
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