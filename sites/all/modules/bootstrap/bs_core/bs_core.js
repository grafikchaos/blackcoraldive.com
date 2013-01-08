(function($) {

Drupal.behaviors.bs_core = {
  attach: function (context, settings) {
  	$('.form-item.has-description').once('tooltip').each(function(){
      if(hover = $(this).attr('data-hover')){
        var $hover = $(hover, this);
      }else{
        var $hover = $(this);
      }
      $hover
        .data('tip', { 
          selector: $(this).attr('data-selector'), 
          title: $(this).attr('data-title'), 
          placement: $(this).attr('data-placement')
        })
        .hover(Drupal.behaviors.bs_core.tipShow, Drupal.behaviors.bs_core.tipHide);
    });

  	$('.tooltip').live({
      mouseover: function(){
        var id = Drupal.behaviors.bs_core.tipCurrentId;
        clearTimeout(Drupal.behaviors.bs_core.tipTimer[id]);
        clearTimeout(Drupal.behaviors.bs_core.rowHideTimer[id]);
      },
      mouseout: function(){
        var item = $(this);
        var id = Drupal.behaviors.bs_core.tipCurrentId;
        Drupal.behaviors.bs_core.tipTimer[id] = setTimeout(function(){
          $('.tip-'+id).removeClass('hover');
      	  item.remove();
        }, 400);
      }
    });
  },  
  rowShowTimer: {},
  rowHideTimer: {},
  tipTimer: {},
  tipId: 1,
  tipCurrentId: 0,
  tipShow: function(){
    var item = $(this);
    var id = Drupal.behaviors.bs_core.tipCurrentId = item.data('tipId') ? item.data('tipId') : Drupal.behaviors.bs_core.tipId++;
    item.data('tipId', id);
    clearTimeout(Drupal.behaviors.bs_core.tipTimer[id]);
    clearTimeout(Drupal.behaviors.bs_core.rowHideTimer[id]);
    if(item.hasClass('hover')) return;
    var selector = item.data('tip').selector;
    Drupal.behaviors.bs_core.rowShowTimer[id] = setTimeout(function(){
      item.addClass('hover tip-'+id);
      if(selector){
       var options = {
         title: item.data('tip').title,
         placement: item.data('tip').placement,
         animation: true,
         trigger: 'manual'
       }
       $(selector, item).tooltip(options).tooltip('show');
      }else{
       item.tooltip();
      }
    }, 500);
    
  },
  tipHide: function(){
    var item = $(this);
    var id = item.data('tipId');
    var selector = item.data('tip').selector;
    clearTimeout(Drupal.behaviors.bs_core.rowShowTimer[id]);
    Drupal.behaviors.bs_core.rowHideTimer[id] = setTimeout(function(){
      item.removeClass('hover');
      if(selector){
         $(selector, item).tooltip('destroy');
      }else{
         item.tooltip('destroy');
      }
    }, 500);
  }
}

})(jQuery);