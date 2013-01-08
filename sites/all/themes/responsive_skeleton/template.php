<?php
/**
 * @file
 * Theme override and preprocess functions for Get Skeleton.
 */

/**
 * Implements hook_preprocess_html().
 */
function get_skeleton_preprocess_html(&$vars) {
  $meta = array(
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' => 'width=device-width, initial-scale=1, maximum-scale=1',
    ),
  );
  drupal_add_html_head($meta, 'get_skeleton-viewport');
  drupal_add_js('http://html5shim.googlecode.com/svn/trunk/html5.js');
}

/**
 * Implements hook_preprocess_page().
 */
function get_skeleton_preprocess_page(&$variables) {
  switch ($variables['layout']) {
    case 'first':
      $variables['left_classes'] = ' three columns';
      $variables['content_classes']  = ' thirteen columns';
      break;

    case 'second':
      $variables['content_classes']  = ' thirteen columns';
      $variables['right_classes'] = ' three columns';
      break;

    case 'both':
      $variables['left_classes'] = ' three columns';
      $variables['content_classes'] = ' ten columns';
      $variables['right_classes'] = ' three columns';
      break;

    case 'none':
      $variables['content_classes'] = 'column';
      break;

    case 'default':
      $variables['content_classes'] = 'column';
  }
}
