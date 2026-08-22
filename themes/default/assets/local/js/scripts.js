/**
 * A script library to import the javascript / css file on the fly
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled with this
 * source code in the LICENSE.txt file.
 */
'use strict';

var prevScrollpos = window.pageYOffset;

/**
 * Apply saved theme immediately (before DOM ready) to prevent flash
 */
(function () {
  var system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  var saved = window.AksaraTheme ? window.AksaraTheme.initialTheme : localStorage.getItem('bs-theme') || system;

  document.documentElement.setAttribute('data-bs-theme', saved);
  localStorage.setItem('bs-theme', saved);

  if (window.AksaraTheme && !window.AksaraTheme.hasUserTheme) {
    window.AksaraTheme.shouldSyncInitialTheme = true;
  }
})();

/**
 * Hide navbar on scroll down, restore on scroll up.
 */
window.addEventListener(
  'scroll',
  function () {
    var currentScrollPos = window.pageYOffset,
      navbar = document.getElementById('header-wrapper');

    if (navbar && window.navigator.userAgent.indexOf('Trident/') === -1) {
      if (navbar.getAttribute('data-hide-on-scroll') !== 'true') {
        navbar.style.transform = 'translateY(0)';
        prevScrollpos = currentScrollPos;
        return;
      }

      navbar.style.transition = 'transform 0.25s ease';

      if (prevScrollpos < currentScrollPos && currentScrollPos > navbar.offsetHeight) {
        navbar.style.transform = 'translateY(-100%)';
      } else {
        navbar.style.transform = 'translateY(0)';
      }

      prevScrollpos = currentScrollPos;
    }
  },
  { passive: true }
);

$(document).ready(function () {
  var themeToggleUrl = function () {
    return (typeof config !== 'undefined' && config.baseUrl ? config.baseUrl : '/') + 'xhr/theme/toggle';
  };
  var syncTheme = function (theme) {
    return $.ajax({
      url: themeToggleUrl(),
      method: 'POST',
      data: {
        theme: theme
      }
    });
  };

  /**
   * Toggle theme dark/light
   */
  if ($('html').attr('data-bs-theme') === 'dark') {
    $('[data-toggle=theme] .mdi').removeClass('mdi-weather-night').addClass('mdi-white-balance-sunny');
  }

  if (typeof updateStatusBarColor === 'function') {
    updateStatusBarColor();
  }

  if (window.AksaraTheme && window.AksaraTheme.shouldSyncInitialTheme) {
    syncTheme($('html').attr('data-bs-theme')).done(function () {
      window.AksaraTheme.hasUserTheme = true;
      window.AksaraTheme.shouldSyncInitialTheme = false;
    });
  }

  $('body').on('click', '[data-toggle=theme]', function (e) {
    e.preventDefault();

    var current = $('html').attr('data-bs-theme');
    var next = current === 'dark' ? 'light' : 'dark';

    $('html').attr('data-bs-theme', next);
    localStorage.setItem('bs-theme', next);
    if (window.AksaraTheme) {
      window.AksaraTheme.initialTheme = next;
      if (window.AksaraTheme.config) {
        window.AksaraTheme.config.activeMode = next;
      }
    }

    if (next === 'dark') {
      $('[data-toggle=theme] .mdi').removeClass('mdi-weather-night').addClass('mdi-white-balance-sunny');
    } else {
      $('[data-toggle=theme] .mdi').removeClass('mdi-white-balance-sunny').addClass('mdi-weather-night');
    }

    if (typeof updateStatusBarColor === 'function') {
      setTimeout(function () {
        updateStatusBarColor();
      }, 0);
    }

    syncTheme(next);
  });

  $('.carousel').on('slide.bs.carousel', function (e) {
    var nextHeight = $(e.relatedTarget).height();

    $(this).find('.active.carousel-item').parent().animate(
      {
        height: nextHeight
      },
      500
    );
  });

  if ($('[data-role=announcements]').length) {
    $('body').css({
      paddingBottom: 40
    });
  }
});

$(document).ready(function () {
  // Intersection Observer for scroll fade-in animation
  if ('IntersectionObserver' in window) {
    var fadeObserver = new IntersectionObserver(
      function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting || entry.intersectionRatio > 0) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        root: null,
        rootMargin: '0px 0px -20px 0px',
        threshold: 0
      }
    );

    var observeFadeElements = function () {
      $('.fade-in:not(.visible)').each(function () {
        fadeObserver.observe(this);
      });
    };

    // Initial run
    observeFadeElements();

    // Watch for dynamically added DOM elements (AJAX)
    var mutationObserver = new MutationObserver(function (mutations) {
      var shouldRecheck = false;
      mutations.forEach(function (mutation) {
        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
          shouldRecheck = true;
        }
      });
      if (shouldRecheck) {
        observeFadeElements();
      }
    });

    mutationObserver.observe(document.body, { childList: true, subtree: true });
  } else {
    // Fallback for older browsers
    $('.fade-in').addClass('visible');
  }
});
