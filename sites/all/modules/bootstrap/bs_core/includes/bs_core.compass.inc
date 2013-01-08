<?php

function bs_core_compass_update($directory, $file, $import_paths) {
  $command = 'compile "' . drupal_realpath($directory) . '" "' . drupal_realpath($file) .'"';

  $args = array();
  $args['output_style'] = 'nested';
  $args['force'] = '';
  $args['boring'] = '';
  $args['relative_assets'] = '';
  $args['additional_import_paths'] = $import_paths;

  return $command . ' ' . bs_core_compass_options($args);
}

function bs_core_compass_options($options) {
  $output = '';
  foreach ($options as $key => $value) {
    $key = str_replace('_', '-', $key);
    if(is_array($value)){
    	$output .= implode('; ', $value);
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

// http://wiki.github.com/chriseppstein/compass/command-line-tool

function bs_core_compass_exec($command, &$output, &$errors) {
  $compass_path = '/usr/local/bin/compass';
  $errors = bs_core_compass_check_path($compass_path);
  if ($errors) {
    watchdog('compass', '!errors', array('!errors' => implode('<br />', $errors)), WATCHDOG_ERROR);
    return FALSE;
  }

  // Specify Drupal's root as the working a working directory so that relative
  // paths are interpreted correctly.
  $drupal_path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME'] ? $_SERVER['SCRIPT_NAME'] : $_SERVER['SCRIPT_FILENAME']);

  $descriptors = array(
    0 => array('pipe', 'r'), // stdin
    1 => array('pipe', 'w'), // stdout
    2 => array('pipe', 'w'), // stderr
  );

  $env = array();
  $env['HOME'] = '/';

  if ($h = proc_open($compass_path . ' ' . $command, $descriptors, $pipes, $drupal_path, $env)) {
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    while (!feof($pipes[1]) || !feof($pipes[2])) {
      if (!feof($pipes[1])) {
        $output .= stream_get_contents($pipes[1]);
      }
      if (!feof($pipes[2])) {
        $errors .= stream_get_contents($pipes[2]);
      }
    }

    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $return_code = proc_close($h);

    // Display debugging information to authorized users.
    if (variable_get('compass_debugging', FALSE) && user_access('administer site configuration')) {
      drupal_set_message(t('Compass command: @command', array('@command' => $compass_path . ' ' . $command)));
      drupal_set_message(t('Compass output: !output', array('!output' => '<pre>' . $output . '</pre>')));
    }

    if ($return_code != 0) {
      // If Compass returned a non-zero code, trigger a PHP error that will
      // be caught by Drupal's error handler, logged to the watchdog and
      // eventually displayed to the user if configured to do so.

      // If $errors is empty, only report the error code.
      if (empty($errors)) {
        trigger_error(t('Compass reported error code !code.', array('!code' => $return_code)), E_USER_ERROR);
      }
      // Otherwise report the error code, and the error message.
      else {
        trigger_error(t("Compass reported error code !code.\nMessage:\n!error", array('!code' => $return_code, '!error' => $errors)), E_USER_ERROR);
      }
    }

    return $return_code;
  }
  return FALSE;
}

function bs_core_compass_check_path($path) {
  $errors = array();
  if (!is_file($path)) {
    $errors[] = t('The specified Compass path %file does not exist.', array('%file' => $path));
  }
  if (!$errors && !is_executable($path)) {
    $errors[] = t('The specified Compass path %file is not executable.', array('%file' => $path));
  }
  if ($errors && $open_basedir = ini_get('open_basedir')) {
    $errors[] = t('PHP\'s <a href="!open-basedir">open_basedir</a> security restriction is set to %open-basedir, which may be interfering with attempts to locate Compass.', array('%file' => $path, '%open-basedir' => $open_basedir, '!info-link' => url('http://php.net/features.safe-mode#ini.open-basedir')));
  }
  return $errors;
}