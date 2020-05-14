(function ($, Drupal, drupalSettings) {
  'use strict';

  function getContainersForInput(input) {
    return $(input).closest('.js-form-item').siblings('.search-suggestions-container--search-input');
  }

  function shouldCloseContainers(input, target) {
    var containers = getContainersForInput(input);
    var elements = $(input).add(containers);
    return (!elementHasTarget(elements, target));
  }

  function elementHasTarget(element, target) {
    return (element.is(target) || element.has(target).length > 0);
  }

  function getSearchSuggestionsAdminBarHeight() {
    var spacing = 0;
    var adminTray = $('#toolbar-item-administration-tray.is-active');
    var toolbar = $('#toolbar-bar');

    if ($('body').hasClass('toolbar-fixed') || $('body').hasClass('toolbar-vertical')) {
      spacing += toolbar.outerHeight();

      if ($('body').hasClass('toolbar-horizontal') && adminTray.length > 0) {
        spacing += adminTray.outerHeight();
      }
    }

    return spacing;
  }

  function setContainerTopSpacing(input, spacing) {
    input.closest('.search-suggestions-container-form').css('top', spacing + 'px');
  }

  function addContainerTopSpacing(input) {
    if ($(window).width() < 900) {
      var adminBarHeight = getSearchSuggestionsAdminBarHeight();
      adminBarHeight += 40;
      setContainerTopSpacing(input, adminBarHeight);
    }
  }

  function removeContainerTopSpacing(input) {
    setContainerTopSpacing(input, 0);
  }

  function closeContainers(containers, input) {
    removeContainerTopSpacing(input);
    input.closest('.search-suggestions-container-form').removeClass('is-searchSuggestionsOpen');
  }

  function openContainers(containers, input) {
    input
      .closest('.search-suggestions-container-form')
      .addClass('is-searchSuggestionsOpen')
      .find('.search-suggestions-container-inner')
      .css('-webkit-overflow-scrolling', 'touch');
    addContainerTopSpacing(input);
  }

  Drupal.behaviors.search_suggestions_container = {
    attach: function (context, settings) {
      $('.search-suggester-input', context).each(function () {
        var suggestionsOpen = false;
        var input = $(this);
        var containers = getContainersForInput(input);

        input.on('keyup', function () {
          if (!input.val()) {
            closeContainers(containers, input);
            suggestionsOpen = false;
          } else {
            openContainers(containers, input);
            suggestionsOpen = true;
          }
        });

        containers.find('.search-suggestions-container-close-icon').on('click', function () {
          closeContainers(containers, input);
        });

        $(document).on('mouseup', function (e) {
          if (suggestionsOpen && shouldCloseContainers(input, e.target)) {
            closeContainers(containers, input);
            suggestionsOpen = false;
          }
        });

        $(window).on('resize', function () {
          if (suggestionsOpen) {
            addContainerTopSpacing(input);
          }
        });
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
