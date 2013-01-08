<?php
/**
 * @file
 * Theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/garland.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 *
 * <?php print render($page['Region_Name']); ?>
 * 
 */
?>

<div id="page-wrapper" class="<?php print $classes; ?>">
  <div id="page" class="<?php print $classes; ?>">

    <?php /////////////// REGION: Header ?> 
    <?php print render($page['header']); ?>
    
    <div id="main-wrapper" class="<?php print $classes_row; ?>">
          
      <?php /////////////// FIRST SIDEBAR ?>
      <?php if ($page['sidebar_first']): ?>
        <div id="sidebar-first" class="sidebar span<?php print $sidebar_span.$classes_sidebar; ?>">
          <?php print render($page['sidebar_first']); ?>
        </div>
      <?php endif; ?>

      <div id="content-wrapper" class="span<?php print $content_span; ?>">
        <div id="content" class="clearfix">

          <?php /////////////// TABS ?>
          <?php if (!empty($tabs['#primary'])): ?>
            <div id="content-tabs" class="clearfix pull-right">
              <?php print render($tabs); ?>
            </div><!-- #sidebar-first -->
          <?php endif; ?>
                         
          <?php /////////////// TITLE ?> 
          <?php if ($title): ?>
            <div class="page-header">
              <?php print render($title_prefix); ?>
              <h1 id="page-title"><?php print $title; ?></h1>
              <?php print render($title_suffix); ?>
            </div>
          <?php endif; ?>

          <?php /////////////// BREADCRUMBS ?> 
          <?php if ($breadcrumb || $shortcut_add_remove): ?>
            <div id="breadcrumb">
              <div class="breadcrumb">
                <?php print $breadcrumb; ?>
                <?php print render($shortcut_add_remove); ?>
              </div>
            </div><!-- /breadcrumb -->
          <?php endif; ?>

          <?php /////////////// MESSAGES ?> 
          <?php if ($messages): ?>
            <?php print $messages; ?>
          <?php endif; ?>

          <?php /////////////// HELP ?> 
          <?php if ($page['help']): ?>
            <div class="alert alert-info">
              <?php print render($page['help']); ?>
            </div>
          <?php endif; ?>
                          
          <?php /////////////// ACTION LINKS ?> 
          <?php if ($action_links): ?>
            <ul class="action-links"><?php print render($action_links); ?></ul>
          <?php endif; ?>
              
          <?php /////////////// CONTENT ?> 
          <?php if ($page['content']): ?>
            <?php print render($page['content']); ?>
          <?php endif; ?>

        </div><!-- #content -->
      </div><!-- #content-wrapper -->

      <?php /////////////// SECOND SIDEBAR ?>
      <?php if ($page['sidebar_second']): ?>
        <div id="sidebar-second" class="sidebar span<?php print $sidebar_span.$classes_sidebar; ?>">
          <?php print render($page['sidebar_second']); ?>
        </div><!-- #sidebar-second -->
      <?php endif; ?>

    </div><!-- #main-wrapper -->

    <?php /////////////// REGION: Footer ?> 
    <?php //print render($page['footer']); ?>
   
  </div><!-- #page -->
</div><!-- #page-wrapper -->