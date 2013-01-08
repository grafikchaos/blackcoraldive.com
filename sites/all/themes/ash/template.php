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
 * Add in modernizer js through the plugins layer
 */
function ash_preprocess_page(&$vars) {

  $title_path = $_GET['q'];

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
