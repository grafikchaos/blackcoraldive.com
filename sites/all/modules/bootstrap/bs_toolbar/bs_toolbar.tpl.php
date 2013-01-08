<?php

/**
 * @file
 * Default template for admin toolbar.
 *
 * Available variables:
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default value has the following:
 *   - toolbar: The current template type, i.e., "theming hook".
 * - $toolbar['toolbar_user']: User account / logout links.
 * - $toolbar['toolbar_menu']: Top level management menu links.
 * - $toolbar['toolbar_drawer']: A place for extended toolbar content.
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_toolbar()
 */
?>
<div id="bs-toolbar-wrapper" class="bs-toolbar-wrapper">
<div id="bs-toolbar" <?php print drupal_attributes($bs_toolbar['attributes']); ?>>
  <div id="bs-float" <?php print drupal_attributes($bs_toolbar['float_attributes']); ?>>
    <div class="bs-primary">
      <div class="navbar navbar-inverse" data-toggle="collapse" data-target=".nav-collapse">
        <div class="navbar-inner">
          <div class="container">
            <?php print render($bs_toolbar['toolbar']); ?>
            <?php print render($bs_toolbar['toolbar_drawer']); ?>
            <?php print render($bs_toolbar['toolbar_user']); ?>
          </div>
        </div>
      </div>
    </div>
    <?php if(!empty($bs_toolbar['toolbar_title']['#markup']) || !empty($bs_toolbar['toolbar_local'])  || !empty($bs_toolbar['toolbar_actions'])): ?>
    <div class="bs-secondary">
      <div class="navbar">
        <div class="navbar-inner">
          <div class="container">
            <div class="nav">
              <?php print render($bs_toolbar['toolbar_title']); ?>
              <?php print render($bs_toolbar['toolbar_add_or_remove_shortcut']); ?>
            </div>
            <?php print render($bs_toolbar['toolbar_local']); ?>
            <?php print render($bs_toolbar['toolbar_actions']); ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
</div>