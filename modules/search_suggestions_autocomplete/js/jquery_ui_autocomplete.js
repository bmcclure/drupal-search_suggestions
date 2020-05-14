(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.search_suggestions_jquery_ui_autocomplete = {
    attach: function (context, settings) {
      $('.search-suggester-jquery-ui-autocomplete', context).each(function () {
        // @todo Use this or remove it.
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
