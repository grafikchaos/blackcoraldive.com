<?php

/**
 * @file
 * Theme setting callbacks for the bs theme.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @param $form
 *   The form.
 * @param $form_state
 *   The form state.
 */
function bs_form_system_theme_settings_alter(&$form, &$form_state) {

  $form['bs_layout'] = array(
    '#type' => 'radios',
    '#title' => t('Content width'),
    '#options' => array(
      'fluid' => t('Fluid width'),
      'fixed' => t('Fixed width'),
    ),
    '#default_value' => theme_get_setting('bs_layout'),
    '#description' => t('Specify whether the content will wrap to a fixed width or will fluidly expand to the width of the browser window.'),
    // Place this above the color scheme options.
    '#weight' => -10,
  );

  $form['bs_sidebar_well'] = array(
    '#type' => 'radios',
    '#title' => t('Use Bootstrap wells for sidebars'),
    '#options' => array(
      0 => t('None'),
      1 => t('Full sidebar'),
      2 => t('Individual sidebar blocks'),
    ),
    '#default_value' => theme_get_setting('bs_sidebar_well'),
    '#description' => t('This will apply a .well to both sidebars.'),
    // Place this above the color scheme options.
    '#weight' => -9,
  );

  $form['bs_powered_by'] = array(
    '#type' => 'textfield',
    '#title' => t('Powered by'),
    '#default_value' => theme_get_setting('bs_powered_by') ? theme_get_setting('bs_powered_by') : '<span>Ashen</span> Rayne',
    '#weight' => -8,
  );

  $form['bs_powered_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Powered by URL'),
    '#default_value' => theme_get_setting('bs_powered_url') ? theme_get_setting('bs_powered_url') : 'http://www.ashenrayne.com',
    '#weight' => -8,
  );

  if(module_exists('context')){
    $form['bs_context_disable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Disable context for this theme'),
      '#default_value' => theme_get_setting('bs_context_disable'),
      '#weight' => -7,
    );
  }
}