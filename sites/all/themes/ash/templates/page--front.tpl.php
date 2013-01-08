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
      <aside class="span3" role="complementary">
        <?php print render($page['sidebar_first']); ?>
      </aside>  <!-- /#sidebar-first -->
    <?php endif; ?>

    <section class="main <?php print _twitter_bootstrap_content_span($columns); ?>">
      <div class="main-content">
      <?php if ($page['highlighted']): ?>
        <div class="highlighted">
          <h2 class="slogan"><?php print $site_slogan; ?></h2>
          <?php if ($site_slogan_sub): ?><h5 class="slogan-sub"><?php print $site_slogan_sub; ?></h5><?php endif; ?>
          <?php print render($page['highlighted']); ?>
        </div>
      <?php endif; ?>
      <a id="main-content"></a>
      <?php if ($page['help']): ?>
        <div class="well"><?php print render($page['help']); ?></div>
      <?php endif; ?>
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      </div>
    </section>

    <?php if ($page['sidebar_second']): ?>
      <aside class="span3" role="complementary">
        <?php print render($page['sidebar_second']); ?>
      </aside>  <!-- /#sidebar-second -->
    <?php endif; ?>
  </div>

  <div class="home-callouts row">
    <div class="span4">
      <?php print render($page['callout_one']); ?>
    </div>
    <div class="span4">
      <?php print render($page['callout_two']); ?>
    </div>
    <div class="span4">
      <?php print render($page['callout_three']); ?>
    </div>
  </div>
</div> <!-- /container -->
</div> <!-- /full-wrap -->

<div class="footer-wrap">
  <footer class="footer container">
    <?php print render($page['footer']); ?>
  </footer>
</div> <!-- /footer-wrap -->
