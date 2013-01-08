<?php 
/**
 * @file
 * Get Skeleton theme implementation to display a single Drupal page.
 */
?>
  <div class="container">
    <div<?php print $attributes; ?>>
    <div id="header">
      <?php if ($logo): ?>
        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
          <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
        </a>
      <?php endif; ?>

      <?php if ($site_name || $site_slogan): ?>
        <div id="name-and-slogan">
          <?php if ($site_name): ?>
            <div id="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </div>
          <?php endif; ?>

          <?php if ($site_slogan): ?>
            <div id="site-slogan"><?php print $site_slogan; ?></div>
          <?php endif; ?>
        </div>

      <?php endif; ?>

      <?php if (isset($page['header'])) : ?>
        <?php print render($page['header']); ?>
      <?php endif; ?>

    </div> <!-- /header -->
    
    <?php if ($messages): ?>
      <div id="messages"><div class="section clearfix">
        <?php print $messages; ?>
      </div></div> <!-- /.section, /#messages -->
    <?php endif; ?>      
      
    <?php if ($breadcrumb): ?>
      <div id="breadcrumb"><?php print $breadcrumb; ?></div>
    <?php endif; ?>
    
    <?php if ($page['sidebar_first']): ?>
      <div id="sidebar-first" class="column sidebar<?php print $left_classes ?>"><div class="section">
        <?php print render($page['sidebar_first']); ?>
      </div></div> <!-- /.section, /#sidebar-first -->
    <?php endif; ?>
    
    <div id="content" class="column<?php print $content_classes ?>">
      <?php if ($title): ?>
        <h1 class="title" id="page-title">
          <?php print $title; ?>
        </h1>
      <?php endif; ?>

      <?php if ($tabs): ?>
        <div class="tabs">
          <?php print render($tabs); ?>
        </div>
      <?php endif; ?>

      <?php print render($page['help']); ?>
      
      <?php if (isset($page['content'])) : ?>
        <?php print render($page['content']); ?>
      <?php endif; ?>
      
      <?php print $feed_icons; ?>
      
    </div>
      
    <?php if ($page['sidebar_second']): ?>
      <div id="sidebar-second" class="column sidebar<?php print $right_classes ?>"><div class="section">
        <?php print render($page['sidebar_second']); ?>
      </div></div> <!-- /.section, /#sidebar-second -->
    <?php endif; ?>      
      
      <?php if (isset($page['footer'])) : ?>
        <?php print render($page['footer']); ?>
      <?php endif; ?>
    </div>
  </div><!-- container -->
