<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> <?php if(!empty($content['field_splash_image']['#items'])): ?>splash-image<?php endif; ?> node-profile clearfix"<?php print $attributes; ?>>


  <header>
    <?php print render($title_prefix); ?>
    <?php if (!$page && $title): ?>
      <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

    <?php if ($display_submitted): ?>
      <span class="submitted">
        <?php print $user_picture; ?>
        <?php print $submitted; ?>
      </span>
    <?php endif; ?>
  </header>

  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
  	// Field Group :: Basic Information
  	hide($content['field_company_logo']);
    hide($content['field_description']);
    hide($content['field_regions']);
    hide($content['field_project_type']);
    // Field Group :: Contact Information
    hide($content['field_phone']);
    hide($content['field_fax']);
  	hide($content['field_address']);
  	hide($content['field_member_website']);
  	hide($content['field_email']);
  	// Field Group :: Social Media
    hide($content['field_facebook_link']);
    hide($content['field_twitter_link']);
    hide($content['field_youtube_link']);
    hide($content['field_flickr_link']);
    // Field Group :: Premium information
    hide($content['field_splash_image']);
    hide($content['field_awards']);
    hide($content['field_images']);
    hide($content['field_testimony']);
    // Hide Flags
    hide($content['flag_favorite']);
    // Other
    hide($content['field_level']);
    hide($content['field_member_id']);
    hide($content['fb_social_node_builder_remodeler_associate']);
    hide($content['sharethis']);
    // Print rest of content
    //print render($content);
  ?>
  
  <?php if ($level == 0): ?><!-- ONLY LEVEL 1 -->
  
  <div class="member-info row-fluid">
	  <div class="span12">
      <div class="member-name">
        <h1 class="page-header"><?php print $node->member_company_title; ?></h1>
        <?php
          print render($content['field_phone']);
          print render($content['field_address']);
        ?>
      </div><!-- .member-name -->
	  </div>
  </div>
  
  <?php endif; ?><!-- END LEVEL 1 -->
  
  <?php if(in_array($level, array(1,2))): ?><!-- ONLY LEVEL 2 or 3 -->
  
  <?php print render($content['field_splash_image']); ?>
  
  <div class="member-top-info row-fluid">
	  <div class="span12">
			<div class="member-top-social">
				<ul>
					<li><?php print render($content['sharethis']); ?></li>
					<li><?php print render($content['flag_favorite']); ?></li>
					<li class="member-top-social-like"><div class="info">Like this remodeler</div> <?php print render($content['fb_social_node_builder_remodeler_associate']); ?></li>
				</ul>
			</div>
	  	<?php print render($content['field_company_logo']); ?>
	  	
	  	<div class="member-name">
	  	  <h1 class="page-header"><?php print $node->member_company_title; ?></h1>
	  	</div><!-- .member-name -->
	  	
	  	<?php
	  	  print render($content['field_phone']);
    		print render($content['field_fax']);
    		print render($content['field_member_website']);
      ?>
	  	
      <?php if (!empty($content['field_facebook_link']['#items']) || !empty($content['field_twitter_link']['#items']) || !empty($content['field_youtube_link']['#items']) || !empty($content['field_flickr_link']['#items'])): ?>
      <div class="social-media">
        <?php
          print render($content['field_facebook_link']);
          print render($content['field_twitter_link']);
          print render($content['field_youtube_link']);
          print render($content['field_flickr_link']);
        ?>
      </div><!-- .social-media -->
      <?php endif; ?>
	  </div>
  </div><!-- .row -->

  <?php if(!empty($content['field_images']['#items'])): ?>
  <div class="member-gallery row-fluid">
    <div class="span12">
      <h3 class="title gallery-title"><i class="icon-camera"></i> Remodeler Gallery</h3>
      <?php print render($content['field_images']); ?>
    </div>
  </div><!-- .row -->
  <?php endif; ?>
  
  <div class="member-details row-fluid">
    <div class="span12">
      <h3 class="title list-title"><i class="icon-bookmark-empty"></i> Remodeler Details</h3>
      <?php print render($content['field_description']); ?>
      <div class="list">
        <?php
          print render($content['field_address']);
        	print render($content['field_regions']);
        	print render($content['field_project_type']);
        	print render($content['field_awards']);
        ?>
      </div><!-- .list -->
    </div>
  </div><!-- .row -->
  
  <?php if(!empty($content['field_testimony']['#items'])): ?>
  <div class="member-testimony row-fluid">
  	<div class="span12">
      <h3 class="title testimony-title"><i class="icon-comments-alt"></i> Testimonials</h3>
      <?php print render($content['field_testimony']); ?>
  	</div>
  </div><!-- .row -->
  <?php endif; ?>
  
  <?php if(!empty($content['field_email'])): ?>
  <div id="contact-form" class="member-email row-fluid">
    <div class="span12">
    	 <h3 class="title email-title"><i class="icon-envelope"></i> Email this Remodeler</h3>
    	 <?php print render($content['field_email']); ?>
    </div>
  </div><!-- .row -->
	<?php endif; ?>
	
	<?php if (!empty($content['field_tags']['#items']) || !empty($content['links']['#items'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php print render($content['links']); ?>
    </footer>
  <?php endif; ?>
  
  <?php endif; ?><!-- END LEVEL 2 or 3 -->
  
</article> <!-- /.node -->
