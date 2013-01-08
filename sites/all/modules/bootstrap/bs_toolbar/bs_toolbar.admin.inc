<?php

/**
 * Settings form for the toolkit.
 */
function bs_toolbar_settings_form($form, &$form_state) {

  $form['bs_toolbar_admin_only'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show toolbar only when on pages using the admin theme'),
    '#default_value' => variable_get('bs_toolbar_admin_only'),
  );
  
  $tree = bs_toolbar_tree();
  $options = array();
  foreach($tree as $data){
  	if($data['link']['hidden']) continue;
  	$options[$data['link']['mlid']] = $data['link']['link_title'];
  }

  $form['bs_toolbar_sidebar_hidden'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Show in hidden sidebar'),
    '#default_value' => variable_get('bs_toolbar_sidebar_hidden', array()),
    '#options' => $options
  );

  $form['bs_toolbar_compression'] = array(
    '#type' => 'radios',
    '#title' => t('Compression Level'),
    '#options' => array('Production (minified)', 'Development (uncompressed)'),
    '#default_value' => variable_get('bs_toolbar_compression', 0),
    '#required' => TRUE,
  );

  return system_settings_form($form);
}