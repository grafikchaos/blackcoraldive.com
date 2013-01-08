(function($) {

Drupal.behaviors.bs = {
  attach: function (context, settings) {
  	$('#field-development-addnew').once('processed').click(function(){
  		$('#edit-field-development-und-select').val('select_or_other').trigger('change').trigger("liszt:updated");
  		return false;
  	});
  }
}

})(jQuery);