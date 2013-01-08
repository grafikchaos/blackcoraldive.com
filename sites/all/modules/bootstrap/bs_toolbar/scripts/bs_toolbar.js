(function($) {

Drupal.behaviors.bs_toolbar = {
  attach: function (context, settings) {

  	// Fix toolbar
    $('#bs-toolbar', context).once('processed').each(function(){

      Drupal.behaviors.bs_toolbar.$win.resize(function() {
        $('#bs-toolbar', context).each(function(){
          $(this).height(Drupal.behaviors.bs_toolbar.$nav.height());
        });
      }).scrollTop(0, 0);
    	Drupal.behaviors.bs_toolbar.$nav = $('#bs-float');
      $(this).height(Drupal.behaviors.bs_toolbar.$nav.height());
      // Don't do the rest of nav is already fixed
      if(Drupal.behaviors.bs_toolbar.$nav.hasClass('nav-fixed')) return;
    	Drupal.behaviors.bs_toolbar.navTop = Drupal.behaviors.bs_toolbar.$nav.length && Drupal.behaviors.bs_toolbar.$nav.offset().top;
    	Drupal.behaviors.bs_toolbar.processScroll();
      Drupal.behaviors.bs_toolbar.$win.on('scroll', Drupal.behaviors.bs_toolbar.processScroll);
    });

    $('#bs-toggle-hidden', context).click(function(){
      var $toolbar = Drupal.behaviors.bs_toolbar.$toolbar;
      if(Drupal.behaviors.bs_toolbar.hiddenShown){
        $('.sidebar-hidden', $toolbar).animate({width:0,opacity:0}, 400, function(){
          $(this).removeAttr('style');
        });
        Drupal.behaviors.bs_toolbar.hiddenShown = false;
      }else{
        $('.sidebar-hidden', $toolbar).each(function(){
          $(this).show();
          var width = $(this).width();
          var height = $(this).height();
          $(this).css({width:0,height:height,opacity:0,overflow:'hidden'}).animate({width:width, opacity:1}, 400, function(){
            $(this).css({overflow:'',width:''});
          });
        });
        Drupal.behaviors.bs_toolbar.hiddenShown = true;
      }
    });

    $('#bs-toggle', context).once('processed').click(Drupal.behaviors.bs_toolbar.navToggle);

    if(Drupal.behaviors.valet){
      $('.search a', '#bs-toolbar-user').click(function(){
        Drupal.behaviors.valet.show();
        return false;
      });
    }

  },

  $win: $(window),
  $nav: {},
  $navFloat: {},
  navTop: 0,
  isFixed: 0,
  navOpen: false,
  paddingInit: 0,
  hiddenShown: false,

  navToggle: function(){
    var $body = $('body');
    var $page = $('#page');
    var $toolbar = $('#bs-toolbar-nav-slide').length ? $('#bs-toolbar-nav-slide') : $('#bs-toolbar-nav').clone().attr('id', 'bs-toolbar-nav-slide-inner');
    var height = window.innerHeight;
    var $navbarOriginal = $('#bs-toolbar');
    var $navbar = $('<div class="navbar"><div class="navbar-inner"><div class="nav"></div><span class="brand">Navigate</span></div></div>');
    var $toggle = $('#bs-toggle').clone().attr('id', 'bs-toggle-slide').once('process').click(Drupal.behaviors.bs_toolbar.navToggle);
    $('.nav', $navbar).append($toggle);

    if(Drupal.behaviors.bs_toolbar.navOpen){
      // Slide toolbar closed
      $toolbar.css({position:'fixed'}).animate({marginLeft:window.innerWidth * -1}, 400, function(){
        $(this).remove();
      });
      $navbarOriginal.show();
      $page.show();
      // Set slide to closed
      Drupal.behaviors.bs_toolbar.navOpen = false;
    }else{
      $toolbar.appendTo($body).wrap('').wrap('<div id="bs-toolbar-nav-slide-wrapper" class="bs-toolbar-wrapper" />').wrap('<div id="bs-toolbar-nav-slide">').before($navbar);
      $toolbar = $('#bs-toolbar-nav-slide');
      $toolbar.css({position:'fixed',top:0,left:0,width:window.innerWidth,minHeight:height,marginLeft:window.innerWidth * -1}).animate({marginLeft:0}, 400, function(){
        $(window).scrollTop(0, 0);
        $navbarOriginal.hide();
        $page.hide();
        $(this).css({position:'absolute', width:'100%'});
      });
      // Set slide to open
      Drupal.behaviors.bs_toolbar.navOpen = true;
    }
    
    return false;
  },

  processScroll: function() {
    var i, scrollTop = Drupal.behaviors.bs_toolbar.$win.scrollTop();
    //var collapse = $(window).width() > 979 ? false : true;
    if (scrollTop >= Drupal.behaviors.bs_toolbar.navTop && !Drupal.behaviors.bs_toolbar.isFixed) {
      Drupal.behaviors.bs_toolbar.isFixed = 1
      Drupal.behaviors.bs_toolbar.$nav.addClass('nav-fixed')
    } else if (scrollTop <= Drupal.behaviors.bs_toolbar.navTop && Drupal.behaviors.bs_toolbar.isFixed) {
      Drupal.behaviors.bs_toolbar.isFixed = 0
      Drupal.behaviors.bs_toolbar.$nav.removeClass('nav-fixed')
    }
  }
}

})(jQuery);