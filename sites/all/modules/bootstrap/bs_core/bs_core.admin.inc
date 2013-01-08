<?php

/**
 * Settings form for the toolkit.
 */
function bs_core_settings_form($form, &$form_state) {

  $form['bs_core_admin_only'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use Bootstrap only when on pages using the admin theme'),
    '#default_value' => variable_get('bs_core_admin_only'),
  );

  return system_settings_form($form);
}