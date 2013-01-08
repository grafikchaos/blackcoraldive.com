(function ($) {

// ============================= GLOBAL ============================= //

// Add subhover class to parent menu item in main nav
Drupal.behaviors.subHover = {
	attach: function (context, settings) {
  	$('#block-menu-block-2 ul.menu > li > ul', context).hover(function() {
      $(this).parent().addClass('subhover');
    }, function(){
      $(this).parent().removeClass('subhover');
  	})
  }
};

// Initiate the Twitter Bootstrap tab js for the search tabs
Drupal.behaviors.searchTabs = {
	attach: function (context, settings) {
		$('#searchTabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		})
	}
};

// Check to see if the placeholder text is available
Drupal.behaviors.placeholderText = {
	attach: function (context, settings) {
		function supports_input_placeholder() {
			var i = document.createElement('input');
			return 'placeholder' in i;
		}
		if (Modernizr.input.placeholder) {
			// The placeholder text is visible, no need for any code!
		} else {
			// Home Search Text
			$('#home-search input.form-text').example('Enter Parade Number, City, Zip, etc...');
			// Global Search Text
			$('#global-search input.form-text').example('Search Entire Site...');
		}
	}
};

// Show search on button click
Drupal.behaviors.globalSearch = {
  attach: function (context, settings) {
  $('.not-front #block-frontpage-frontpage-search', context).hide();
  $('.not-front #block-menu-block-2 ul.menu li.menu-mlid-1575 span', context).click(function() {
    $('.not-front #block-frontpage-frontpage-search').slideToggle(500);
    return false;
  });
  }
};

// Add tooltips
Drupal.behaviors.ashTooltips = {
  attach: function (context, settings) {
    $('.use-tooltip').once('ash').tooltip({title:'data-original-title'});
  }
};

// Add sharebar
Drupal.behaviors.ashSharebar = {
  attach: function (context, settings) {
    $('.sharethis-btn').once('ash').click(function(){
    	$(this).parents('.sharethis-buttons').find('.sharethis-popup').fadeIn(400);
    	return false;
    });
    $('.sharethis-hide').once('ash').click(function(){
    	$(this).parents('.sharethis-popup').fadeOut(200);
    	return false;
    });
  }
};

// Gallery carousel slider animation
Drupal.behaviors.gallerySlider = {
  attach: function (context, settings) {
    $('.member-gallery .field-items, .home-images .field-name-field-images .field-items', context).bxSlider({
      easing: 'easeOutExpo',
      displaySlideQty: 7,
      moveSlideQty: 1,
      infiniteLoop: false,
      hideControlOnEnd: true
    });
  }
};


// ============================= HOMEPAGE ============================= //

// Homepage search button focus animation
Drupal.behaviors.homeFocus = {
  attach: function (context, settings) {
    $('.front #block-menu-block-2 ul.menu li.menu-mlid-1575', context).click(function () {
      $('.tab-content .tab-pane form').effect('pulsate', { times:2 }, 1500);
    });
  }
};

// Show the homepage sponsored member on input click
Drupal.behaviors.homeSponsor = {
	attach: function (context, settings) {
	   $('.front .tab-content .sponsor').hide();
	   $('.front .tab-content input.form-text', context).hover(function () {
  	   $('.front .tab-content .sponsor').show();
		})
	}
};


// ============================= HOMES PAGE ============================= //

// Initiate the Twitter Bootstrap tab js for the property profile tabs
Drupal.behaviors.propertyTabs = {
	attach: function (context, settings) {
  	$('.tab-content > .tab-pane', context).hide();
		$('#propertyTabs a', context).click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		})
	}
};

})(jQuery);