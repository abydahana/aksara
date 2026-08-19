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
  var saved = localStorage.getItem('bs-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  document.documentElement.setAttribute('data-bs-theme', saved);
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
  /**
   * Toggle theme dark/light
   */
  if (localStorage.getItem('bs-theme') === 'dark') {
    $('[data-toggle=theme] .mdi').removeClass('mdi-weather-night').addClass('mdi-white-balance-sunny');
  }

  $('body').on('click', '[data-toggle=theme]', function (e) {
    e.preventDefault();

    var current = $('html').attr('data-bs-theme');
    var next = current === 'dark' ? 'light' : 'dark';

    $('html').attr('data-bs-theme', next);
    localStorage.setItem('bs-theme', next);

    if (next === 'dark') {
      $('[data-toggle=theme] .mdi').removeClass('mdi-weather-night').addClass('mdi-white-balance-sunny');
    } else {
      $('[data-toggle=theme] .mdi').removeClass('mdi-white-balance-sunny').addClass('mdi-weather-night');
    }

    $.ajax({
      url: (typeof config !== 'undefined' && config.baseUrl ? config.baseUrl : '/') + 'xhr/theme_toggle',
      method: 'POST',
      data: {
        theme: next
      }
    });
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
