<?php
/**
 * @file
 * Default theme implementation to display a single Drupal page.
 */
?>
<div id="page-wrapper" class="container-fluid">
  <div id="page">
  
    <?php if ($breadcrumb): ?>
      <div id="breadcrumb">
        <?php print $breadcrumb; ?>
      </div>
    <?php endif; ?>

    <?php print $messages; ?>

    <div id="main-wrapper">
      <div id="main" class="clearfix">
        <div id="content" class="column">
          <div class="section">
            <a id="main-content"></a>

            <?php if ($action_links): ?>
              <?php print render($action_links); ?>
            <?php endif; ?>

            <?php print render($title_prefix); ?>
            <?php if ($title): ?>
              <h1 class="title" id="page-title"><?php print html_entity_decode($title); ?></h1>
            <?php endif; ?>
            <?php print render($title_suffix); ?>

            <?php if ($tabs): ?>
              <div class="tabs">
                <?php print render($tabs); ?>
              </div>
            <?php endif; ?>

            <?php print render($page['help']); ?>

            <?php print render($page['content']); ?>
            <?php print $feed_icons; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
