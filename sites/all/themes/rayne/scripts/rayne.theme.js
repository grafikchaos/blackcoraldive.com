(function ($) {

// Drupal.behaviors.rayne = {
//   attach: function (context, settings){
//     $('div.btn-group[data-toggle-name=*]').each(function(){
//       var group   = $(this);
//       console.log(group);
//       var form    = group.parents('form').eq(0);
//       var name    = group.attr('data-toggle-name');
//       var hidden  = $('input[name="' + name + '"]', form);
//       $('button', group).each(function(){
//         var button = $(this);
//         button.live('click', function(){
//             hidden.val($(this).val());
//         });
//         if(button.val() == hidden.val()) {
//           button.addClass('active');
//         }
//       });
//     });
//   }
// }

// Add tooltips
Drupal.behaviors.rayneTooltips = {
  attach: function (context, settings) {
    $('.use-tooltip').once('rayne').tooltip({title:'data-original-title'});
    $('.form-actions').button();
  }
};

/**
 * Attempt to fix bootstrap tabs and jquery ui tab conflict
 * https://gist.github.com/1344584
 */
if(Drupal.behaviors.menu_block && Drupal.behaviors.menu_block.attach){
  Drupal.behaviors.menu_block = {
    attach: function (context, settings) {
      // This behavior attaches by ID, so is only valid once on a page.
      if ($('#menu-block-settings.menu-block-processed').size()) {
        return;
      }
      $('#menu-block-settings', context).addClass('menu-block-processed');

      // Show the "display options" if javascript is on.
      $('.form-item-display-options.form-type-radios>label', context).addClass('element-invisible');
      $('.form-item-display-options.form-type-radios', context).show();
      // Make the radio set into a jQuery UI buttonset.
      //$('#edit-display-options', context).buttonset();
      $('#edit-display-options', context).button();

      // Override the default show/hide animation for Form API states.
      $('#menu-block-settings', context).bind('state:visible', function(e) {
        if (e.trigger) {
          e.stopPropagation() /* Stop the handler further up the tree. */
          $(e.target).closest('.form-item, .form-wrapper')[e.value ? 'slideDown' : 'slideUp']('fast');
        }
      });

      // Syncronize the display of menu and parent item selects.
      $('.menu-block-parent-mlid', context).change( function() {
        var menuItem = $(this).val().split(':');
        $('.menu-block-menu-name').val(menuItem[0]);
      });
      $('.menu-block-menu-name', context).change( function() {
        $('.menu-block-parent-mlid').val($(this).val() + ':0');
      });
    }
  };
}

/**
 * Views admin helper
 *
 * @TODO Add to a seperate javascript file that only gets loaded on demand
 */

Drupal.behaviors.viewsUiRenderAddViewButton = Drupal.behaviors.viewsUiRenderAddViewButton || {};
Drupal.behaviors.viewsUiRenderAddViewButton.attach = function (context, settings) {
  // Build the add display menu and pull the display input buttons into it.
  var $menu = $('#views-display-menu-tabs', context).once('views-ui-render-add-view-button-processed');

  if (!$menu.length) {
    return;
  }
  $menu.removeClass('secondary').addClass('secondary-bootstrap');
  $('.views-display-disabled-link', $menu).addClass('btn btn-info');
  var $addDisplayDropdown = $('<li class="add"><a href="#" class="btn">Add <span class="caret"></span></a><ul class="action-list" style="display:none;white-space:nowrap;"></ul></li>');
  var $displayButtons = $menu.nextAll('button.add-display').detach();
  $displayButtons.appendTo($addDisplayDropdown.find('.action-list')).wrap('<li>')
    .parent().first().addClass('first').end().last().addClass('last');
  // Remove the 'Add ' prefix from the button labels since they're being palced
  // in an 'Add' dropdown.
  // @todo This assumes English, but so does $addDisplayDropdown above. Add
  //   support for translation.
  $displayButtons.each(function () {
    var label = $(this).val();
    if (label.substr(0, 4) == 'Add ') {
      $(this).val(label.substr(4));
    }
  });
  $addDisplayDropdown.appendTo($menu);

  // Add the click handler for the add display button
  $('li.add > a', $menu).bind('click', function (event) {
    event.preventDefault();
    var $trigger = $(this);
    Drupal.behaviors.viewsUiRenderAddViewButton.toggleMenu($trigger);
  });
  // Add a mouseleave handler to close the dropdown when the user mouses
  // away from the item. We use mouseleave instead of mouseout because
  // the user is going to trigger mouseout when she moves from the trigger
  // link to the sub menu items.
  // We use the live binder because the open class on this item will be
  // toggled on and off and we want the handler to take effect in the cases
  // that the class is present, but not when it isn't.
  $('li.add', $menu).live('mouseleave', function (event) {
    var $this = $(this);
    var $trigger = $this.children('a[href="#"]');
    if ($this.children('.action-list').is(':visible')) {
      Drupal.behaviors.viewsUiRenderAddViewButton.toggleMenu($trigger);
    }
  });
};

/**
 * Modifying the classes for tableDragChangeWarning in tabledrag.js
 */
Drupal.theme.prototype.tableDragChangedWarning = function () {
  return '<div class="tabledrag-changed-warning alert alert-warning">' + Drupal.theme('tableDragChangedMarker') + ' ' + Drupal.t('Changes made in this table will not be saved until the form is submitted.') + '</div>';
};

/**
 * Add button support to ajax
 */
if(Drupal.views && Drupal.views.ajaxView && Drupal.views.ajaxView.prototype){
  Drupal.views.ajaxView.prototype.attachExposedFormAjax = function() {
    var button = $('input[type=submit], input[type=image], button[type=submit]', this.$exposed_form);
    button = button[0];

    this.exposedFormAjax = new Drupal.ajax($(button).attr('id'), button, this.element_settings);
  };
}

})(jQuery);