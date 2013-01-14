(function($) {

Drupal.behaviors.crest = {
  attach: function (context, settings) {
  }
};


Drupal.behaviors.crestFilters = {
  attach: function (context) {
    $('.filter-buttons-wrapper', context).once('bs').each(function(){
      var wrapper = $(this);
      var list = $('select.filter-list', wrapper);
      $('.form-type-select', wrapper).hide();
      $('button', wrapper).click(function(){
        var format = $(this).attr('data-format');
        list.val(format).trigger('change');
      });
    });
  }
};

/**
 * Views admin helper
 *
 * @TODO Add to a seperate javascript file that only gets loaded on demand
 */
Drupal.behaviors.viewsUiRenderAddViewButton = Drupal.behaviors.viewsUiRenderAddViewButton || {};
Drupal.behaviors.viewsUiRenderAddViewButton.attach = function (context, settings) {
  // Build the add display menu and pull the display input buttons into it.
  var $menu = $('#views-display-menu-tabs', context).once('btn-group views-ui-render-add-view-button-processed');

  if (!$menu.length) {
    return;
  }
  $menu.removeClass('secondary');
  //$('.views-display-disabled-link', $menu).addClass('btn btn-info');
  var $addDisplayDropdown = $('<a href="#" class="btn btn-mini dropdown-toggle" data-toggle="dropdown">Add <span class="caret"></span></a><ul class="action-list dropdown-menu"></ul></div>');
  var $displayButtons = $menu.nextAll('button.add-display').detach();
  // Remove the 'Add ' prefix from the button labels since they're being palced
  // in an 'Add' dropdown.
  // @todo This assumes English, but so does $addDisplayDropdown above. Add
  // support for translation.
  $displayButtons.each(function () {
    var label = $(this).val();
    if (label.substr(0, 4) == 'Add ') {
      $(this).val(label.substr(4));
    }
  });
  $addDisplayDropdown.unwrap().appendTo($menu);
  var $actionList = $menu.find('.action-list');
  $displayButtons.appendTo($actionList).css({width:'200px','text-align':'left',margin:'5px 10px',}).wrap('<li>').parent().first().addClass('first').end().last().addClass('last');
  $actionList.find('button.btn').addClass('btn-mini');
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



/**
 * This script transforms a set of fieldsets into a stack of vertical
 * tabs. Another tab pane can be selected by clicking on the respective
 * tab.
 *
 * Each tab may have a summary which can be updated by another
 * script. For that to work, each fieldset has an associated
 * 'verticalTabCallback' (with jQuery.data() attached to the fieldset),
 * which is called every time the user performs an update to a form
 * element inside the tab pane.
 */
Drupal.behaviors.verticalTabs = {
  attach: function (context) {
    $('.vertical-tabs-panes', context).once('vertical-tabs', function () {
      var focusID = $(':hidden.vertical-tabs-active-tab', this).val();
      var tab_focus;

      // Check if there are some fieldsets that can be converted to vertical-tabs
      var $fieldsets = $('> fieldset', this);
      if ($fieldsets.length === 0) {
        return;
      }

      // Create the tab column.
      var tab_list = $('<ul class="nav nav-tabs"></ul>');
      $(this).wrap('<div class="vertical-tabs clearfix"></div>').before(tab_list);

      // Transform each fieldset into a tab.
      $fieldsets.each(function () {
        var vertical_tab = new Drupal.verticalTab({
          title: $('> legend', this).text(),
          fieldset: $(this)
        });
        tab_list.append(vertical_tab.item);
        $(this)
          .removeClass('collapsible collapsed')
          .addClass('vertical-tabs-pane')
          .data('verticalTab', vertical_tab);
        if (this.id == focusID) {
          tab_focus = $(this);
        }
      });

      $('> li:first', tab_list).addClass('first');
      $('> li:last', tab_list).addClass('last');

      if (!tab_focus) {
        // If the current URL has a fragment and one of the tabs contains an
        // element that matches the URL fragment, activate that tab.
        if (window.location.hash && $(window.location.hash, this).length) {
          tab_focus = $(window.location.hash, this).closest('.vertical-tabs-pane');
        }
        else {
          tab_focus = $('> .vertical-tabs-pane:first', this);
        }
      }
      if (tab_focus.length) {
        tab_focus.data('verticalTab').focus();
      }
    }).addClass('tab-content').parents('.vertical-tabs').addClass('tabbable tabs-left');
  }
};

Drupal.behaviors.accordion = {
  attach: function (context, settings) {
    $('div.accordion', context).once('accordion', function () {
      var $fieldset = $(this);
      var $fieldsetBody = $('.accordion-body', $fieldset);
      if($fieldset.hasClass('collapsed')){
        $fieldsetBody.removeClass('in');
      }else{
        $fieldsetBody.addClass('in');
      }
      // Expand fieldset if there are errors inside, or if it contains an
      // element that is targeted by the uri fragment identifier.
      var anchor = location.hash && location.hash != '#' ? ', ' + location.hash : '';
      if ($('.error' + anchor, $fieldset).length) {
        $fieldsetBody.addClass('in');
      }
    });
  }
};

})(jQuery);

