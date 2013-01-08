<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


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
    // Image Fields
    hide($content['field_image']);
    hide($content['field_images']);
    // Field Group :: Location Information
    hide($content['field_belongs_to_showcase']);
    hide($content['field_address']);
    hide($content['field_geo_location']);
    // Field Group :: Property Information
    hide($content['field_price']);
    hide($content['field_bedrooms']);
    hide($content['field_bathrooms']);
    hide($content['field_square_footage']);
    hide($content['field_home_features']);
    hide($content['field_style']);
    hide($content['field_regions']);
    hide($content['field_school_district']);
    hide($content['field_development']);
    hide($content['field_property_extras']);
    hide($content['field_property_id']);
    // Field Group :: Additional Information
    hide($content['field_virtual_tour']);
    hide($content['field_your_personal_designer']);
    hide($content['field_belongs_to_type']);
    hide($content['bing_places']);
    // Hide Flags
    hide($content['flag_favorite']);
    // Other
    hide($content['fb_social_node_property']);
    hide($content['sharethis']);
    // Print rest of content
    //print render($content);
  ?>
  
  <div class="homes-top row">
    <div class="span9">

      <div class="home-images">
      	<div class="home-images-inner">
      	 <div class="home-main-image">
	        <?php print render($content['field_image']); ?>
	        <div class="dream-home-badge"></div>
	        <div class="green-badge"></div>
	       </div>
         <?php if(!empty($content['field_images'])): ?>
	         <?php print render($content['field_images']); ?>
           <div class="gallery-howto">Click the thumbnails to enlarge the images</div>
         <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="member-aside span3">
      <?php print render($content['field_belongs_to_type']); ?>
    </div>
  </div><!-- .row -->
  
  <div class="homes-bottom row">
    <div class="span12">
      <div class="home-details">
        
        <div class="homes-top-social">
          <div class="homes-top-social-inner">
            <ul>
              <li><?php print render($content['sharethis']); ?></li>
              <li><?php print render($content['flag_favorite']); ?></li>
              <li class="homes-top-social-like"><div class="info">Like this home</div> <?php print render($content['fb_social_node_property']); ?></li>
            </ul>
          </div>
        </div>
  
      	<ul class="nav nav-tabs" id="propertyTabs">
      		<li class="active"><a href="#home">Home Details</a></li>
      		<li><a href="#area-map">Map</a></li>
      	</ul>
      	 
      	<div class="tab-content">
      		<div class="tab-pane fade active in row-fluid" id="home">
      			<div class="<?php print empty($content['zillow']) ? 'span12' : 'span8'; ?>">
      				<?php
      				  print render($content['field_belongs_to_showcase']);
      				  print render($content['field_showcase_number']);
      				  print render($content['field_address']);
      				?>
      				
      				<?php print render($content['field_home_highlights']); ?>
      				
      				 <div class="home-features">
      				  <h3 class="title features-title"><i class="icon-bookmark-empty"></i> Home Description</h3>
      				  <?php print render($content['field_home_features']); ?>
      				 </div>
               
              <?php if(!empty($content['field_directions'])): ?>
                 <div class="home-directions">
                  <h3 class="title directions-title"><i class="icon-road"></i> Directions</h3>
                  <?php print render($content['field_directions']); ?>
                 </div>
               <?php endif; ?>
      				 
      				 <div class="list">
                <h3 class="title features-title"><i class="icon-check"></i> Home Details</h3>
                <?php
                  print render($content['field_property_project_type']);
                  print render($content['field_price']);
                  print render($content['field_bedrooms']);
                  print render($content['field_bathrooms']);
                  print render($content['field_square_footage']);
                  print render($content['field_style']);
                  print render($content['field_regions']);
                  print render($content['field_school_district']);
                  print render($content['field_development']);
                  //print render($content['field_property_extras']);
                  print render($content['extras']);
                ?>
      				 </div>
              <?php if (!empty($content['field_virtual_tour']['#items']) || !empty($content['field_your_personal_designer']['#items'])): ?>
              	<div class="tour-designer clearfix">
              	<?php
                	print render($content['field_virtual_tour']);
                	print render($content['field_your_personal_designer']);
                ?>
                </div>
              <?php endif; ?>
      			</div>
            <?php if(!empty($content['zillow'])): ?>
        			<div class="span4 well">
        					<h3 class="title neighborhood-title"><i class="icon-map-marker"></i> Neighborhood Info</h3>
                  <?php print render($content['zillow']); ?>
        			</div>
            <?php endif; ?>
      		</div><!-- .row -->
      		<div class="tab-pane fade" id="area-map">
      			<div class="row-fluid">
      				<div class="span12">
      					<?php
      					 print render($content['field_belongs_to_showcase']);
      					 print render($content['field_showcase_number']);
      					 print render($content['field_address']);
      				  ?>
      				</div>
      			</div>
      			<div class="row-fluid">
      				<div class="span3">
      					<?php print render($content['bing_places']); ?>
      				</div>
      				<div class="span9">
      					<?php print render($content['field_geo_location']); ?>
      				</div>
      			</div><!-- .row -->
      		</div><!-- .tab-pane -->
      	</div>
    	</div>
    </div>
	</div><!-- .row -->
	
	<?php if (!empty($content['field_tags']['#items']) || !empty($content['links']['#items'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php print render($content['links']); ?>
    </footer>
  <?php endif; ?>

</article> <!-- /.node -->
