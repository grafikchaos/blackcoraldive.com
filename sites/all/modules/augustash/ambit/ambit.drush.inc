<?php
/**
 * @file
 * Ambit Drush command
 */

/**
 * Implementation of hook_drush_command().
 */
function ambit_drush_command() {
  $items = array();
  $items['ambit'] = array(
    'callback' => 'ambit_drush',
    'description' => dt('Use Ambit to process site sass files.'),
    'arguments'   => array(
      'op'    => dt('Operation: compile or watch'),
    ),
    'options' => array(
      'compact' => "Compile using compact output style.",
      'force' => "Force recreation of CSS files.",
    ),
  );
  return $items;
}

/**
 * Implementation of hook_drush_help().
 */
function ambit_drush_help($section) {
  switch ($section) {
    case 'drush:ambit':
    return dt("Use Ambit to process site sass files.");
  }
}

/**
 * Process sass files 
 */
function ambit_drush($op = 'compile'){
  
  // Options
  $commands = array();
  if(drush_get_option('compact')){
    $commands['output-style'] = 'compact --force';
    $commands['force'] = '';
  }
  if(drush_get_option('force')) $commands['force'] = '';

  $handler = _ambit_get_handler('assets', 'scss');
  $settings = $handler->settings_get();
  $path = drupal_realpath($settings['uri']);
  switch($op){
    case 'watch':
      drush_log(dt("Ambit is watching for changes..."), 'ok');
      break;
  }
	if (drush_shell_exec('compass '.$op.' '.ambit_compass_options($commands).' "'.$path.'"')) {
    drush_log(dt("Ambit compiled successfully:\n!status", array('!status' => implode("\n", drush_shell_exec_output()))), 'ok');
  }
  else {
    drush_set_error('DRUSH_AMBIT', dt("Problems were encountered running Ambit.\nThe specific errors are below:\n!errors", array('!errors' => implode("\n", drush_shell_exec_output()))));
  }
}

function ambit_compass_options($options) {
  $output = '';
  foreach ($options as $key => $value) {
    $key = str_replace('_', '-', $key);
    if($key == 'c'){
      $output .= '-' . $key . ' ' .$value;
    }else if (!empty($value)) {
      $output .= '--' . $key . ' ' . $value;
    }
    else {
      $output .= '--' . $key;
    }
    $output .= ' ';
  }
  return $output;
}