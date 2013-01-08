(function($) {

Drupal.behaviors.bs_library = {
  attach: function (context, settings) {
    var options = {
      title: function(){return $(this).attr('data-title')},
    };
    if (typeof eval($().tooltip) == 'function') 
      $('.use-tooltip', context).once('tooltip').tooltip(options);
    var options = {
      title: function(){return $(this).attr('data-title')},
      content: function(){return $(this).attr('data-content')}
    };
    if (typeof eval($().popover) == 'function') 
      $('.use-popover', context).once('popover').popover(options);
  }
}

})(jQuery);