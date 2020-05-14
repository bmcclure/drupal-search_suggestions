(function ($, Drupal, drupalSettings) {
  'use strict';

  function handleSearchSuggestionInput(input, containers) {
    input
      .on('keyup', function () {
        var keys = input.val();

        if (keys) {
          containers.each(function () {
            var container = $(this);

            container.find('.search-suggestions-container__heading').each(function () {
              var heading = $(this);
              var headingText = heading
                .data('heading-template')
                .replace('%s', keys);
              heading.html(headingText);
            });

            container.find('.search-suggester-results').each(function () {
              var result = $(this);
              var suggester = result.attr('data-search-suggester');
              var innerContainer = result.find('.search-suggester-results__ajax-container');

              $.ajax({
                dataType: 'html',
                url: drupalSettings.path.baseUrl + 'search_suggestions/suggest/' + suggester,
                data: {keys: keys},
                success: function (data) {
                  if (data) {
                    innerContainer.html(data);
                  } else {
                    innerContainer.html('');
                  }
                },
                fail: function (data) {
                  innerContainer.html('');
                }
              });
            });
          });
        }
      })
  }

  Drupal.behaviors.search_suggestions = {
    attach: function (context, settings) {
      $('.search-suggester-input', context).each(function () {
        var input = $(this);
        var containers = input
          .closest('.js-form-item')
          .siblings('.search-suggestions-container')
          .add(
            input
              .closest('.search-suggestions-search-form')
              .siblings('.search-suggestions-container')
          );
        handleSearchSuggestionInput(input, containers);
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
