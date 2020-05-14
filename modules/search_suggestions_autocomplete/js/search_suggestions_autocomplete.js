(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.search_suggestions_autocomplete = {
    attach: function (context, settings) {
      $('.search-suggester-autocomplete', context).each(function () {
        // @todo Use this or remove it.
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
