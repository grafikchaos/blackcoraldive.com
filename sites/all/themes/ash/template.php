<?php

/**
 * Override the exposed home search to add certain classes and placholder text.
 */
function ash_form_alter(&$form, &$form_state, $form_id) {	
	
	// Add classes to the profile email form submit buttons
	if ($form_id == 'email_mail_page_form') {
  	$form['submit']['#attributes']['class'][] = 'btn-large btn-primary';
	}
	
  if ($form_id == 'views_exposed_form') {
  	switch($form_state['view']->name){
	  	case 'search_properties':
	  		$form['query']['#attributes']['placeholder'] = t('Enter Parade Number, Street Address, City, Zip, etc...');
	  		$form['submit']['#value'] = t('Search');
	  		break;
      case 'search_remodeled':
        $form['query']['#attributes']['placeholder'] = t('Enter Parade Number, Street Address, City, Zip, etc...');
        $form['submit']['#value'] = t('Search');
        break;
	  	case 'search':
	  		$form['query']['#attributes']['placeholder'] = t('Search Entire Site...');
	  		break;
  	}
    $form['submit']['#attributes']['class'][] = 'btn-large';
    // Adds class to the development and company name fields in the filter sidebar
    $form['development']['#attributes']['class'][] = 'chosen-selector';
    $form['company_name']['#attributes']['class'][] = 'chosen-selector';
  }
  if ($form_id == 'comment_node_blog_entry_form') {
	  $form['actions']['submit']['#value'] = t('Post Comment');
	  $form['actions']['submit']['#attributes']['class'][] = 'btn-primary';
  }
  
  $form['reset']['#attributes']['class'][] = 'btn-large';
  
  if ($form_id == 'user_login') {
	  $form['actions']['submit']['#attributes']['class'][] = 'btn-large btn-primary';
  }
  
  if ($form_id == 'user_register_form') {
	  $form['actions']['submit']['#attributes']['class'][] = 'btn-large';
  }
}

/**
 * Preprocess HTML
 */
function ash_preprocess_html(&$vars) {
  if(arg(0) == 'homes') $vars['classes_array'][] = 'page-homes';

  // If on an individual node page, add the node type to body classes.
  if ($node = menu_get_object()) {
    if(!empty($node) && !empty($node->field_mn_green_path[$node->language])){
      if(!empty($node->field_mn_green_path[$node->language][0]['value'])) $vars['classes_array'][] = 'mn-green-path';
    }
    if(!empty($node) && !empty($node->field_dream_home[$node->language])){
      if(!empty($node->field_dream_home[$node->language][0]['value'])) $vars['classes_array'][] = 'dream-home';
    }
    if(!empty($node) && !empty($node->field_dream_remodeled_home[$node->language])){
      if(!empty($node->field_dream_remodeled_home[$node->language][0]['value'])) $vars['classes_array'][] = 'dream-remodeled-home';
    }
    if(!empty($node) && $node->type == 'property' && !empty($node->member_company_type)){
      $vars['classes_array'][] = 'home-type-'.$node->member_company_type;
    }
  }
}

/**
 * Add in modernizer js through the plugins layer
 */
function ash_preprocess_page(&$vars) {

  $title_path = $_GET['q'];
  
  if (strpos($title_path,'directory/builders') !== false) {
    drupal_set_title('Find Trusted, Qualified Builders in Minnesota | MN');
  }
  
  if (strpos($title_path,'directory/remodelers') !== false) {
    drupal_set_title('Find Qualified, Professional Remodelers in Minnesota | MN');
  }
  
  if (strpos($title_path,'directory/products-services') !== false) {
    drupal_set_title('Find Minnesota Landscapers, Cabinet Makers, Home Security Services & More');
  }

	$node = isset($vars['node']) ? $vars['node'] : false;
	$vars['title_hide'] = FALSE;
  $vars['site_slogan_sub'] = variable_get('site_slogan_sub', '');

	$path = drupal_get_path('theme', 'ash');

	drupal_add_js('misc/ui/jquery.effects.core.min.js', 'file', JS_LIBRARY);
	drupal_add_js('misc/ui/jquery.effects.pulsate.min.js', 'file', JS_LIBRARY);
	drupal_add_js($path . '/assets/js/ash.modernizr.js', 
		array(
			'type' => 'file',
			'weight' => 1,
			'group' => JS_LIBRARY,
			'every_page' => TRUE
			
		)
	);
	drupal_add_js($path . '/assets/js/ash.jquery.example.min.js',
		array(
			'type' => 'file',
			'weight' => 1,
			'group' => JS_LIBRARY,
			'every_page' => TRUE
		)
	);
	drupal_add_js($path . '/assets/js/ash.jquery.easing.1.3.js', 
		array(
			'type' => 'file',
			'weight' => 1,
			'group' => JS_LIBRARY,
			'every_page' => TRUE
			
		)
	);
	drupal_add_js($path . '/assets/js/ash.jquery.bxSlider.min.js', 
		array(
			'type' => 'file',
			'weight' => 1,
			'group' => JS_LIBRARY,
			'every_page' => TRUE
			
		)
	);
	// if ($path = libraries_get_path('twitter_bootstrap')) {
	//   drupal_add_js($path . '/js/bootstrapx-clickover.js', array('weight'=>1000));
	// }
	drupal_add_js('http://use.typekit.com/hup2ubi.js', 'external');
	drupal_add_js('try{Typekit.load();}catch(e){}', 'inline', 'page_bottom');

	// Remove default title from builder/remodeler/associate pages as it is added in node.tpl
	if($node && in_array($node->type, array('builder', 'remodeler', 'associate'))){
		$vars['title_hide'] = TRUE;
	}

}

/**
 * Implements hook_preprocess_node()
 */
function ash_preprocess_node(&$vars) {
  $node = isset($vars['node']) ? $vars['node'] : false;
  // Allow for custom templates by view mode
  $vars['theme_hook_suggestions'][] = 'node__' . $vars['node']->type . '__' . $vars['view_mode'];  

  // Lets make flags available as standalone elements in the template
  if(!empty($vars['content']['links']['flag']['#links'])){
  	foreach($vars['content']['links']['flag']['#links'] as $key => $values){
  		$vars['content'][str_replace('-', '_', $key)] = array(
  			'#markup' => $values['title'],
  		);
  	}
  }
  if(empty($vars['content']['flag_favorite'])){
    $flag = flag_load('favorite');
    if(in_array($vars['node']->type, $flag->types)){
      $vars['content']['flag_favorite'] = array(
        '#markup' => l($flag->flag_short, 'user/login', array('html'=>true, 'attributes'=>array('class'=>array('flag btn btn-mini')))),
      );
    }
  }

  if($node->type == 'property' && !empty($node->field_showcase_number[LANGUAGE_NONE][0]['value']) && !empty($node->member_company_type) && $node->member_company_type == 'remodeler'){
    $vars['content']['field_showcase_number'][0]['#markup'] = 'R'.$vars['content']['field_showcase_number'][0]['#markup'];
  }

  if(!empty($vars['content']['sharethis'])){

  	$close = '<button class="sharethis-hide btn btn-mini pull-right"><i class="icon-remove"></i></button>';
  	$popup = '
			<div class="sharethis-popup popover clickover fade bottom in" style="display: none; top:-10px; left:-90px">
			<div class="arrow"></div>
			<div class="popover-inner" style="width: 235px;">
			<h3 class="popover-title">Share '.$close.'</h3>
			<div class="popover-content">
			'.render($vars['content']['sharethis']).'
			</div></div></div>
  	';
  	$sharethis = array(
  		'#tag' => 'div',
  		'#type' => 'html_tag',
  		'#attributes' => $vars['content']['sharethis']['#attributes'],
  		'#value' => '<a class="sharethis-btn btn btn-mini" data-original-title="Share" data-content-selector="member-top-social-sharethis"><i class="icon-share" style="color:#3A87AD"></i> Share</a>
        <div class="sharethis-popup-container" style="position:relative;">'.$popup.'</div>',
      '#weight' => $vars['content']['sharethis']['#weight']
  	);
  	$vars['content']['sharethis'] = $sharethis;
  }
  $vars['level'] = 0;
  if(in_array($vars['node']->type, array('builder', 'remodeler', 'associate', 'property')) && !empty($vars['node']->field_level)){
    $vars['level'] = empty($vars['node']->field_level[LANGUAGE_NONE][0]['value']) ? 0 : $vars['node']->field_level[LANGUAGE_NONE][0]['value'];
  }

  if(!empty($vars['content']['field_description'][0]['#markup'])){
    $vars['content']['field_description'][0]['#markup'] = htmlspecialchars_decode(check_markup($vars['content']['field_description'][0]['#markup'], 'html_clean'));
  }

  if(!empty($vars['content']['field_home_features'][0]['#markup'])){
    $vars['content']['field_home_features'][0]['#markup'] = htmlspecialchars_decode(check_markup($vars['content']['field_home_features'][0]['#markup'], 'html_clean'));
  }

  if($node && in_array($node->type, array('property'))){
    $extras = array();
    $content = &$vars['content'];
    // Add energy efficient tour icon to property extras
    if(!empty($content['field_property_extras'][0]['#items'])){
      $extras = $content['field_property_extras'][0]['#items'];
    }
    // Add energy efficient tour icon to property extras
    if(!empty($content['field_energy_efficient_home_tour'][0])){
      $extras[] = $content['field_energy_efficient_home_tour'][0]['#markup'];
    }
    // Green path
    if(!empty($content['field_mn_green_path'][0]['#markup'])){
      $extras[] = $content['field_mn_green_path'][0]['#markup'];
    }
    // Dream homes
    if(!empty($content['field_dream_home'][0])){
      $extras[] = $content['field_dream_home'][0]['#markup'];
    }
    // Dream remodeled homes
    if(!empty($content['field_dream_remodeled_home'][0])){
      $extras[] = $content['field_dream_remodeled_home'][0]['#markup'];
    }
    $extraas = strip_tags(implode('', $extras));
    if(count($extras) && !empty($extraas)){
      $content['extras'] = array(
        '#theme' => 'field',
        '#title' => 'Additional Info',
        '#formatter' => 'textformatter_list',
        '#field_type' => 'list_text',
        '#label_display' => 'inline',
        '#items' => $extras,
        '#bundle' => 'property',
        '#field_name' => 'field_property_extras',
        0 => array(
          '#theme' => 'item_list',
          '#type' => 'ul',
          '#items' => $extras,
          '#attributes' => array('class'=>array('textformatter-list')),
        )
      );
    }
  }
}

function ash_user_view_alter(&$build, $type){
	$view = views_get_view('favorites');
  $view->set_display('all');
  $view->execute();
  $build['favorites'] = array(
  	'#markup' => $view->preview()
  );
	$view->destroy();
}



/**
 * Themes a select drop-down as a collection of links
 *
 * @see theme_select(), http://api.drupal.org/api/function/theme_select/6
 * @param array $vars - An array of arrays, the 'element' item holds the properties of the element.
 *                      Properties used: title, value, options, description, name
 * @return HTML string representing the form element.
 */
function ash_select_as_links($vars) {
  $element = $vars['element'];

  $output = '';
  $name = $element['#name'];

  // Collect selected values so we can properly style the links later
  $selected_options = array();
  if (empty($element['#value'])) {
    if (!empty($element['#default_values'])) {
      $selected_options[] = $element['#default_values'];
    }
  }
  else {
    $selected_options[] = $element['#value'];
  }

  // Add to the selected options specified by Views whatever options are in the
  // URL query string, but only for this filter
  $urllist = parse_url(request_uri());
  if (isset($urllist['query'])) {
    $query = array();
    parse_str(urldecode($urllist['query']), $query);
    foreach ($query as $key => $value) {
      if ($key != $name) {
        continue;
      }
      if (is_array($value)) {
        // This filter allows multiple selections, so put each one on the selected_options array
        foreach ($value as $option) {
          $selected_options[] = $option;
        }
      }
      else {
        $selected_options[] = $value;
      }
    }
  }

  // Go through each filter option and build the appropriate link or plain text
  foreach ($element['#options'] as $option => $elem) {
    // Check for Taxonomy-based filters
    if (is_object($elem)) {
      list($option, $elem) = each(array_slice($elem->option, 0, 1, TRUE));
    }

    /*
     * Check for optgroups.  Put subelements in the $element_set array and add a group heading.
     * Otherwise, just add the element to the set
     */
    $element_set = array();
    if (is_array($elem)) {
      $element_set = $elem;
    }
    else {
      $element_set[$option] = $elem;
    }

    $links = array();
    $multiple = !empty($element['#multiple']);

    foreach ($element_set as $key => $value) {
      // Custom ID for each link based on the <select>'s original ID
      $id = drupal_html_id($element['#id'] . '-' . $key);
      $elem = array(
        '#id' => $id,
        '#markup' => '',
        '#type' => 'bef-link',
        '#name' => $id,
      );
      if (array_search($key, $selected_options) === FALSE) {
        $elem['#children'] = l($value, bef_replace_query_string_arg($name, $key, $multiple), array('attributes'=>array('class'=>array('btn btn-info btn-mini'))));
        $output .= theme('form_element', array('element' => $elem));
      } else {
        $elem['#children'] = l($value, bef_replace_query_string_arg($name, $key, $multiple, true), array('attributes'=>array('class'=>array('active btn btn-mini btn-inverse'))));
        _form_set_class($elem, array('bef-select-as-links-selected'));
        $output .= str_replace('form-item', 'form-item selected', theme('form_element', array('element' => $elem)));
      }
    }
  }

  $properties = array(
    '#description' => isset($element['#bef_description']) ? $element['#bef_description'] : '',
    '#children' => $output,
  );

  return '<div class="bef-select-as-links">'
    . theme('select', array('element' => $vars['element']))
    . theme('form_element', array('element' => $properties))
    . '</div>';
}

/**
 * Theme function for unified login page.
 *
 * @ingroup themable
 */
function ash_lt_unified_login_page($variables) {

  $login_form = $variables['login_form'];
  $register_form = $variables['register_form'];
  $active_form = $variables['active_form'];
  $output = '';

  $output .= '<div class="toboggan-unified ' . $active_form . '">';

  // Create the initial message and links that people can click on.
  $output .= '<div id="login-message">' . t('You are not logged in.') . '</div>';
  $output .= '<div id="login-links">';
  $output .= l(t('I have an account'), 'user/login', array('attributes' => array('class' => array('login-link'), 'id' => 'login-link')));
  $output .= ' ';
  $output .= l(t('I want to create an account'), 'user/register', array('attributes' => array('class' => array('login-link'), 'id' => 'register-link')));

  $output .= '</div>';

  // Add the login and registration forms in.
  $output .= '<div id="login-form">' . $login_form . '</div>';
  $output .= '<div id="register-form">' . $register_form . '</div>';

  if (variable_get('fboauth_id', '') && !fboauth_fbid_load()) {
    $output .= '<div class="toboggan-fboath"><div class="inner">Use your Facebook account to login:&nbsp;&nbsp;'.fboauth_action_display('connect').'</div></div>';
  }

  $output .= '</div>';

  return $output;
}
