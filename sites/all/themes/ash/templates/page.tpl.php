<div class="full-wrap">
<header id="navbar" role="banner" class="navbar">
  <div class="navbar-inner clearfix">
    <div class="container">
      <?php if ($logo): ?>
        <a class="brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
          <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
        </a>
      <?php endif; ?>

      <?php if ($site_name): ?>
        <hgroup id="site-name-slogan">
          <?php if ($site_name): ?>
          <h1>
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" class="ash-brand"><?php print $site_name; ?></a>
          </h1>
          <?php endif; ?>
          <?php if ($logged_in): ?>
            <div class="register-login">
              <span class="account">Welcome <?php global $user; print $user->name; ?>! View all your favorites in <a href="/user">My Account</a></span> <span class="logout"><a href="/user/logout">Log out</a></span>
            </div>
          <?php else: ?>
            <div class="register-login">
              <span class="reg">Save all of your favorite homes and services! <a href="/user/register">Register</a></span> <span class="login">Already have an account? <a href="/user">Login</a></span>
            </div>
          <?php endif; ?>
          <div class="header-slogan">
            <h3>Where Dream Homes Come True<sup>&reg;</sup></h3>
          </div>
        </hgroup>
      <?php endif; ?>
    </div>
    <?php print render($page['header']); ?>
  </div>
</header>

<div class="container <?php print $classes; ?> clearfix">
  <div class="row">

    <?php if ($page['sidebar_first']): ?>
      <aside class="sidebar-first span3" role="complementary">
        <?php print render($page['sidebar_first']); ?>
      </aside>  <!-- /#sidebar-first -->
    <?php endif; ?>

    <?php if ($page['highlighted']): ?>
      <div class="highlighted span12"><?php print render($page['highlighted']); ?></div>
    <?php endif; ?>

    <section class="main <?php print _twitter_bootstrap_content_span($columns); ?>">
      <div class="main-content">
        <a id="main-content"></a>
        <?php if ($tabs): ?>
          <?php print render($tabs); ?>
        <?php endif; ?>
        <?php print render($title_prefix); ?>
        <?php if ($title && !$title_hide): ?>
          <h1 class="page-header"><?php print $title; ?></h1>
        <?php endif; ?>
        <?php print render($title_suffix); ?>
        <?php if ($messages): ?>
          <div id="status-messages"><?php print $messages; ?></div>
        <?php endif; ?>
        <?php if ($page['help']): ?>
          <div class="well"><?php print render($page['help']); ?></div>
        <?php endif; ?>
        <?php if ($action_links): ?>
          <ul class="action-links"><?php print render($action_links); ?></ul>
        <?php endif; ?>
        <?php print render($page['content']); ?>
        <?php print $feed_icons ?>
      </div>
    </section>

    <?php if ($page['sidebar_second']): ?>
      <aside class="sidebar-second span3" role="complementary">
        <?php print render($page['sidebar_second']); ?>
      </aside>  <!-- /#sidebar-second -->
    <?php endif; ?>

  </div>
</div> <!-- /container -->
</div> <!-- /full-wrap -->

<div class="footer-wrap">
  <footer class="footer container">
    <?php print render($page['footer']); ?>
  </footer>
</div> <!-- /footer-wrap -->
