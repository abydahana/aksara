/**
 * Bunch of functions
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled with this
 * source code in the LICENSE.txt file.
 */
'use strict';

/**
 * Mimic user agent
 */
const UA = window.innerWidth >= 1024 ? 'desktop' : 'mobile';

/**
 * Store AJAX promise to prevent multiple request
 */
let xhr;

/**
 * Pulse time to dismiss toast
 */
let pulseTime;

/**
 * Call your own function to run after AJAX call. Add anywhere to your
 * javascript file.
 *
 * Usage:
 * afterCall.push( function() { // Expected code to run });
 */
let afterCall = [];

/**
 * Get and set content wrapper's height
 */
let fullHeight = window.innerHeight;

/**
 * Buzzer's sound, applied when app enable the action's sound
 */
let successBuzzer = new Audio(config.baseUrl + 'assets/local/sound/success.mp3');
let failBuzzer = new Audio(config.baseUrl + 'assets/local/sound/fail.mp3');
let warningBuzzer = new Audio(config.baseUrl + 'assets/local/sound/alert.mp3');

/**
 * Polling
 */
let polling;

/**
 * Mimic phrase helper
 */
function phrase(phrase) {
  if (typeof phrases[phrase] !== 'undefined') {
    phrase = phrases[phrase];
  } else {
    phrase.toLowerCase().replace(/\b[a-z]/g, function (letter) {
      return letter.toUpperCase();
    });
  }

  return phrase;
}

/**
 * Go to URL slug
 *
 * @param   string url
 * @param   object params
 *
 * @return  string
 */
function go_to(path, params) {
  let url = window.location.href.split(/[?#]/)[0];
  let queryParams = {};

  $.each(params, function (key, val) {
    if (val) {
      queryParams[key] = val;
    }
  });

  if (Object.keys(queryParams).length <= 1) {
    queryParams = null;
  }

  return url.replace(/\/$/, '') + (path ? '/' + path : '') + (queryParams ? '?' + $.param(queryParams) : '');
}

/**
 * Get the query string value from url
 *
 * @param   string keyword,
 * @param   string url
 *
 * @return  string
 */
function get_query_params(keyword, url) {
  if (!url) {
    // No URL's set, use window location
    url = window.location;
  } else {
    url = new URL(url);
  }

  // Extract parameter
  let params = new URLSearchParams(url.search);

  // Get query string value based on given keyword
  return params.get(keyword);
}

/**
 * Rewrite PHP function to javascript format
 */
function htmlspecialchars(string) {
  if (typeof string === 'string') {
    string = string.replace(/&/g, '&amp;');
    string = string.replace(/"/g, '&quot;');
    string = string.replace(/'/g, '&#039;');
    string = string.replace(/</g, '&lt;');
    string = string.replace(/>/g, '&gt;');
  }

  return string;
}

/**
 * Convert HEX color to RGBA
 */
function hex2rgba(hex, alpha) {
  let channel;

  if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
    channel = hex.substring(1).split('');

    if (channel.length == 3) {
      channel = [channel[0], channel[0], channel[1], channel[1], channel[2], channel[2]];
    }

    channel = '0x' + channel.join('');

    return 'rgba(' + [(channel >> 16) & 255, (channel >> 8) & 255, channel & 255].join(',') + ',' + (alpha ? alpha : 1) + ')';
  }

  throw new Error('Bad Hex');
}

/**
 * Convert the RGBA color to HEX
 */
function rgba2hex(rgba) {
  if (!rgba) {
    rgba = 'rgba(255,255,255,1)';
  }

  if (typeof rgba === 'string' && rgba.startsWith('#')) {
    return rgba;
  }

  let rgb = rgba.replace(/\s/g, '').match(/^rgba?\((\d+),(\d+),(\d+),?([^,\s)]+)?/i);
  let hex = '000000';

  if (rgb) {
    hex = (rgb[1] | (1 << 8)).toString(16).slice(1) + (rgb[2] | (1 << 8)).toString(16).slice(1) + (rgb[3] | (1 << 8)).toString(16).slice(1);
  }

  return '#' + hex;
}

/**
 * Set the color scheme of browser status bar
 */
function updateStatusBarColor(color) {
  if (!color) {
    color = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || getComputedStyle(document.documentElement).getPropertyValue('--wg-primary').trim();
  }

  if (color) {
    color = rgba2hex(color);

    let msNav = document.querySelector('meta[name=msapplication-navbutton-color]');
    let themeColor = document.querySelector('meta[name=theme-color]');
    let appleStatusBar = document.querySelector('meta[name=apple-mobile-web-app-status-bar-style]');

    if (msNav) msNav.setAttribute('content', color);
    if (themeColor) themeColor.setAttribute('content', color);
    if (appleStatusBar) appleStatusBar.setAttribute('content', color);
  }
}

/**
 * Execute the jQuery script inline the document
 */
function jExec(script) {
  return script;
}

/**
 * Pop the message up
 */
function throw_exception(code, message, target, redirect) {
  if (typeof redirect !== 'undefined' && redirect && redirect !== 'soft') {
    // Hard redirect to target
    return window.location.replace(target);
  } else {
    // Soft redirect to target
    $(document.createElement('a')).attr('href', target).addClass('--xhr').appendTo('body').trigger('click').remove();
  }

  if (!message) {
    return false;
  }

  let color = 'danger';
  let icon = 'mdi mdi-emoticon-sad-outline';

  if ($.inArray(code, [200, 301]) !== -1) {
    color = 'success';
    icon = 'mdi mdi-check-circle-outline';
  } else if ($.inArray(code, [403, 404]) !== -1) {
    color = 'warning';
    icon = 'mdi mdi-alert-octagram-outline';
  }

  // Add twig function
  Twig.extendFunction('phrase', function (words, check) {
    return words ? phrase(words.trim()) : '';
  });

  Twig.extendFunction('currentPage', function (path, params, unset) {
    let url = window.location.href.split(/[?#]/)[0];
    let queryParams = {};

    if (params && typeof params === 'object') {
      $.each(params, function (key, val) {
        if (val && key !== unset) {
          queryParams[key] = val;
        }
      });
    }

    let queryString = Object.keys(queryParams).length ? '?' + $.param(queryParams) : '';

    return url.replace(/\/$/, '') + (path ? '/' + path : '') + queryString;
  });

  Twig.extendFunction('truncate', function (string, length, delimeter) {
    if (string.length <= length) {
      return string;
    }

    return string.slice(0, length) + delimeter;
  });

  let template = Twig.twig({
    data: components['core/exception.twig']
  });

  let content = template.render({
    color: color,
    icon: icon,
    message: message
  });

  // Remove previous notification
  $('[data-bs-dismiss=toast]').trigger('click');

  // Pop new notification
  $(content).appendTo('body');

  // Clear previous timeout
  clearTimeout(pulseTime);

  // Add timeout
  pulseTime = setTimeout(function () {
    $('[data-bs-dismiss=toast]').trigger('click');

    // Clear previous timeout
    clearTimeout(pulseTime);
  }, 5000);
}

/**
 * Notification
 */
function fetchNotifications() {
  $.ajax({
    url: `${config.baseUrl}notifications/polling`,
    method: 'POST',
    success: function (notifications) {
      if (notifications.unread > 0) {
        $('[data-role=notifications]').find('#notification-count').removeClass('d-none');
      } else {
        $('[data-role=notifications]').find('#notification-count').addClass('d-none');
      }

      $('[data-role=notifications]').find('#notification-count').text(notifications.unread);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error('Error fetching notifications:', textStatus, errorThrown);
    }
  });
}

/**
 * Because this website is adopt SPA concept, sometime it's require to
 * re-trigger required plugins
 */
function reactivate(individual, ignoreSelf) {
  if (typeof individual === 'undefined' || individual == true) {
    individual = [];
  }

  if (typeof ignoreSelf === 'undefined') {
    ignoreSelf = false;
  }

  if (typeof polling !== 'undefined') {
    clearInterval(polling);
  }

  // Global reactivate trigger
  if (!individual.length) {
    // Clear previous timeout
    clearTimeout(pulseTime);

    // Remove previous alert
    pulseTime = setTimeout(function () {
      $('[data-bs-dismiss=toast]').trigger('click');

      // Clear previous timeout
      clearTimeout(pulseTime);
    }, 5000);

    // Stop QR Reader
    if (typeof qrReader !== 'undefined' && qrReader.getState() !== 1) {
      try {
        qrReader.stop();
      } catch (e) {
        // Safe abstraction
      }
    }

    /**
     * Update element when viewport is modified
     */
    _viewport_modifier();

    /**
     * Detect if device browser is mobile
     */
    if (UA != 'mobile') {
      /**
       * Focus to first field, expect there's modal open. to trigger focus the field inside modal,
       * check the correspondent of shown.bs.modal from the global.min.js
       */
      $(':input').blur();

      if ($('form[method=POST]:visible').last().find('.autofocus:enabled:visible:first').length) {
        $('form[method=POST]:visible').last().find('.autofocus:enabled:visible:first').focus();
      } else {
        $('form[method=POST]:visible').last().find('input[type=text], textarea').filter(':enabled:visible:first:not(.nofocus)').focus();
      }

      if ($('body').hasClass('sidebar-collapsed auto-collapse')) {
        $('body').removeClass('sidebar-collapsed auto-collapse sidebar-hovered');
      }
    } else {
      $('html').removeClass('overflow-hidden');
      $('body').removeClass('sidebar-expanded sidebar-hovered');
    }

    /**
     * Sometime the component need to be resolve when the
     * browser window is resized
     */
    $(window)
      .on('resize', function () {
        _viewport_modifier();
      })
      .on('scroll', function (e) {
        if ($('.navbar').first().next('[data-role=content]').children('.carousel').length || $('.navbar').first().next('[data-role=content]').children('.leading').length) {
          // Apply navigation menu modification
          let element = $('.navbar').first().next('[data-role=content]').children('.carousel').length
            ? $('.navbar').first().next('[data-role=content]').children('.carousel')
            : $('.navbar').first().next('[data-role=content]').children('.leading').length
              ? $('.navbar').first().next('[data-role=content]').children('.leading')
              : null;
          let toe = element.offset().top;
          let boe = element.offset().top + element.outerHeight();
          let bos = $(window).scrollTop() + $(window).innerHeight();
          let tos = $(window).scrollTop();

          if (bos > toe && tos < boe && !$('body').hasClass('sidebar-expanded')) {
            $('.navbar').first().addClass('bg-transparent');
          } else {
            $('.navbar').first().removeClass('bg-transparent');
          }
        }
      });

    /**
     * Alert user if they using unstable browser
     */
    if ($('input[type=file]').length) {
      let agent = navigator.userAgent || navigator.vendor || window.opera;

      if (agent.toLowerCase().indexOf('fban') > -1 || agent.toLowerCase().indexOf('fbav') > -1 || agent.toLowerCase().indexOf('instagram') > -1) {
        alert(phrase('This browser might experience problems related to file reading permissions on your device.'));
      }
    }

    /**
     * Dispose previous autocomplete element
     */
    if (typeof $.fn.autocomplete !== 'undefined') {
      $('[data-role=autocomplete]').autocomplete('dispose');
    }

    /**
     * Fix the hidden button that break the radius in button group
     */
    if ($('.btn-group').find('.btn:hidden:first').nextAll('.btn:visible:first').length) {
      $('.btn-group').find('.btn:hidden:first').nextAll('.btn:visible:first').addClass('rounded-start');
    } else {
      $('.btn-group').find('.btn').not(':first').removeClass('rounded-start');
    }

    if ($('.btn-group').find('.btn:hidden:last').prevAll('.btn:visible:first').length) {
      $('.btn-group').find('.btn:hidden:last').prevAll('.btn:visible:first').addClass('rounded-end');
    } else {
      $('.btn-group').find('.btn').not(':last').removeClass('rounded-end');
    }

    /**
     * Disable input autocomplete
     */
    $('input, textarea').attr('autocomplete', 'off');
    $('input[type=password]').attr('autocomplete', 'new-password');

    /**
     * Remove the DOM's element that used before
     */
    $('.dcalendarpicker, #sortableListsBase, .autocomplete-suggestions, .note-popover').remove();

    /**
     * Remove previous shown tooltip
     */
    $('.tooltip.show').remove();

    /**
     * Reload tooltip and popover
     */
    $('[data-bs-toggle=tooltip]').tooltip();
    $('[data-bs-toggle=popover]').popover();

    /**
     * Close offcanvas
     */
    $('[data-bs-dismiss=offcanvas]').trigger('click');
    $('[data-bs-toggle=offcanvas].is-open').trigger('click');

    /**
     * Make textarea autogrow
     */
    $('textarea')
      .each(function () {
        $(this).css({
          height: this.scrollHeight > $(this).actual('outerHeight') ? this.scrollHeight + 2 : $(this).actual('outerHeight'),
          overflowY: 'hidden'
        });
      })
      .on('input', function () {
        if (!$(this).hasClass('no-resize')) {
          this.style.height = 'auto';
          this.style.height = this.scrollHeight + 2 + 'px';
        }
      });

    $('body').on('keyup', '.select2-search__field', function (e) {
      if (typeof OverlayScrollbarsGlobal !== 'undefined') {
        $('.select2-results').each(function () {
          const instance = OverlayScrollbarsGlobal.OverlayScrollbars(this);
          if (instance) {
            instance.update();
          }
        });
      }
    });

    /**
     * Triggering mousewheel
     */
    $('input[type=number]').bind('DOMMouseScroll mousewheel', function (e) {
      $(this).trigger('change');
    });

    /**
     * Trigger stopped carousel to slide
     */
    if ($('[data-bs-ride=carousel]').length) {
      $('[data-bs-ride=carousel]').carousel();
      $('[data-bs-ride=carousel]').bind('slide.bs.carousel');
    }

    /**
     * Retrigger fetching parameter of API
     */
    if ($('.fetch-parameter').length) {
      $('.fetch-parameter').trigger('change');
    }

    /**
     * Load more first click
     */
    if ($('.load-more').length) {
      $('.load-more').trigger('click');
    }

    /**
     * Check parent item and open if it's link were active
     */
    $('.sidebar-menu .nav-item.active').each(function () {
      $(this).parents('li').addClass('active is-parent');
      $(this).parents('ul').addClass('show').prev('.nav-link').addClass('is-expanded');
    });

    /**
     * Restore table hidden columns if saved in sessionStorage
     */
    if ($('.--toggle-column').length) {
      const pageKey = 'hidden_cols_' + window.location.pathname;
      const hiddenCols = JSON.parse(sessionStorage.getItem(pageKey) || '[]');
      if (hiddenCols.length) {
        hiddenCols.forEach(function (col) {
          $(`.form-check-input.--toggle-column[data-column="${col}"]`).prop('checked', false);
          $(`th[data-column="${col}"], td[data-column="${col}"]`).addClass('d-none');
        });
      }
    }

    /**
     * Call your own function to run after AJAX call. Add anywhere to your
     * javascript file.
     *
     * Usage:
     * afterCall.push( function() { // Expected code to run });
     */
    if (typeof afterCall !== 'undefined' && $.isArray(afterCall) && afterCall.length) {
      $.each(afterCall, function (key, val) {
        if (typeof afterCall[key] === 'function') {
          afterCall[key].call();
        } else {
          afterCall[key];
        }
      });
    }
  }

  /**
   * Select2 plugin
   */
  if ($('select[data-role=select]').length && (!individual.length || individual.includes('select')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/select2/select2.min.css', config.baseUrl + 'assets/select2/select2-bootstrap-5-theme.min.css', config.baseUrl + 'assets/local/css/select2.min.css']);
    require.js([config.baseUrl + 'assets/select2/select2.min.js'], function () {
      $('select[data-role=select]').each(function () {
        if ($(this).next('.select2').length || $(this).hasClass('no-js')) {
          return;
        }

        const group = $(this).closest('.input-group');
        const extra = group
          .find('.input-group-text')
          .toArray()
          .reduce((sum, el) => sum + $(el).outerWidth(true), 0);
        const config = {
          theme: 'bootstrap-5',
          width: () => group.width() - extra + 'px',
          placeholder: $(this).attr('placeholder'),
          dropdownParent: $(this).parent(),
          minimumResultsForSearch: 10,
          templateSelection: function (response) {
            if (!$(response.element).data('icon')) {
              return response.text;
            }

            return $('<i class="' + $(response.element).data('icon') + '"></i> ' + response.text);
          },
          templateResult: function (response) {
            if (!$(response.element).data('icon')) {
              return response.text;
            }

            return $('<i class="' + $(response.element).data('icon') + '"></i> ' + response.text);
          }
        };

        if ($(this).data('relation') || $(this).data('href')) {
          config.ajax = {
            url: $(this).data('href') ?? $(this).closest('form').attr('action'),
            method: 'POST',
            dataType: 'json',
            data: function (params) {
              let query = {
                method: 'ajax_select',
                source: $(this).attr('name'),
                page: params.page || 1,
                search: params.term,
                selected_list: $(this).val()
              };

              const depends = ($(this).data('depends') || '').toString().split(',');
              if (depends.length) {
                depends.forEach((field) => {
                  field = field.trim();
                  if (!field) {
                    return;
                  }

                  const form = $(this).closest('form');
                  let input = form.find('[name="' + field + '"]');
                  if (!input.length) {
                    input = form.find('[name="' + field + '[]"]');
                  }
                  if (!input.length) {
                    input = form.find('#' + field + '_input, #' + field);
                  }

                  query[field] = input.val() || '';
                });
              }

              return query;
            }
          };
        }

        $(this)
          .select2(config)
          .on('change', function (e) {
            // Remove highlight border
            $(e.target).next('.select2-container').find('.select2-selection').removeClass('border-danger');

            let autosubmit = $(this).closest('form').attr('data-autosubmit');

            if (autosubmit) {
              $(this).closest('form').trigger('submit');
            }
          });
      });

      $('body')
        .off('change.ajaxSelectDepends')
        .on('change.ajaxSelectDepends', 'select[data-role=select]', function () {
          const source = $(this).attr('name');
          const form = $(this).closest('form');
          if (!source) {
            return;
          }

          form.find('select[data-role=select][data-depends]').each(function () {
            const depends = ($(this).data('depends') || '')
              .toString()
              .split(',')
              .map((field) => field.trim());
            if (depends.indexOf(source) === -1) {
              return;
            }

            $(this).val(null).empty().trigger('change');
          });
        });
    });
  }

  /**
   * Load wysiwyg editor plugin
   */
  if ($('[data-role=wysiwyg]').length && (!individual.length || individual.includes('wysiwyg')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/summernote/summernote.min.css', config.baseUrl + 'assets/local/css/summernote.min.css']);
    require.js([config.baseUrl + 'assets/summernote/summernote.min.js'], function () {
      $('[data-role=wysiwyg]').each(function () {
        if ($(this).attr('disabled') || $(this).attr('readonly')) return;

        let context = $(this);

        context.summernote({
          placeholder: context.attr('placeholder') ? context.attr('placeholder') : phrase('Your content goes here...'),
          dialogsInBody: true,
          dialogsFade: false,
          spellCheck: false,
          disableResizeEditor: true,
          disableDragAndDrop: true,
          disableTags: ['script', 'iframe', 'object', 'embed'],
          styleTags: [
            {
              tag: 'h2',
              title: 'Heading'
            },
            {
              tag: 'h3',
              title: 'Subheading'
            },
            {
              tag: 'p',
              title: 'Normal'
            }
          ],
          toolbar: [['style', ['style', 'bold', 'italic', 'underline']], ['para', ['ul', 'ol']], UA !== 'mobile' ? ['insert', ['link', 'table', 'picture', 'video']] : null, UA !== 'mobile' ? ['misc', ['clear', 'help']] : null],
          callbacks: {
            onDialogShown: function (e) {
              $('.modal-dialog').addClass('modal-dialog-centered');
              $('.form-control-file').addClass('form-control');
            },
            onImageUpload: function (image) {
              let data = new FormData();

              data.append('image', image[0]);

              // Upload file
              $.ajax({
                url: config.baseUrl + 'xhr/summernote/upload',
                contentType: false,
                processData: false,
                data: data,
                method: 'POST',
                beforeSend: function () {},
                success: function (response) {
                  if (!response || 'success' !== response.status || !response.source) {
                    return throw_exception(403, response && response.messages ? response.messages : phrase('Upload Error!'));
                  }

                  context.summernote('insertImage', response.source);
                },
                error: function (response) {
                  response = response && response.responseJSON ? response.responseJSON : response;

                  return throw_exception(403, response && response.messages ? response.messages : phrase('Upload Error!'));
                }
              });
            },
            onMediaDelete: function (image) {
              // Delete file
              $.ajax({
                url: config.baseUrl + 'xhr/summernote/delete',
                method: 'POST',
                data: {
                  source: image[0].src
                }
              });
            }
          }
        });
      });
    });
  }

  /**
   * Load swiperJS
   */
  if ($('.swiper').length && (!individual.length || individual.includes('swiper')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/swiper/swiper-bundle.min.css', config.baseUrl + 'assets/local/css/swiper.min.css']);
    require.js([config.baseUrl + 'assets/swiper/swiper-bundle.min.js'], function () {
      $('.swiper').each(function () {
        let context = $(this);

        if (context.hasClass('swiper-initialized') || context.data('swiper-initialized')) {
          return;
        }
        context.addClass('swiper-initialized').data('swiper-initialized', true);

        let _unique = Math.floor(Math.random() * 9999),
          _autoplay = 1 == context.attr('data-autoplay') || 'true' == context.attr('data-autoplay') ? true : false,
          _autoHeight = 1 == context.attr('data-auto-height') || 'true' == context.attr('data-auto-height') ? true : false,
          _loop = 1 == context.attr('data-loop') || 'true' == context.attr('data-loop') ? true : false;

        context.addClass('swiper_' + _unique);

        if (typeof context.attr('data-pagination') !== 'undefined') {
          $(`<div class="swiper-pagination swiper-pagination_${_unique}"></div>`).appendTo(context);
        }

        if (typeof context.attr('data-navigation') !== 'undefined') {
          $(`<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>`).appendTo(context);
        }

        if (_autoplay) {
          _autoplay = {
            delay: typeof context.attr('data-delay') !== 'undefined' ? parseInt(context.attr('data-delay')) : 3000,
            pauseOnMouseEnter: true,
            disableOnInteraction: false
          };
        }

        let swiper = new Swiper('.swiper_' + _unique, {
          watchSlidesProgress: true,
          loop: _loop,
          autoHeight: _autoHeight,
          autoplay: _autoplay,
          speed: typeof context.attr('data-speed') !== 'undefined' ? parseInt(context.attr('data-speed')) : 300,
          spaceBetween: typeof context.attr('data-space-between') !== 'undefined' ? parseInt(context.attr('data-space-between')) : 20,
          slidesPerView: typeof context.attr('data-slide-count') !== 'undefined' ? parseInt(context.attr('data-slide-count')) : 'auto',
          breakpoints: {
            640: {
              slidesPerView: typeof context.attr('data-slide-count-sm') !== 'undefined' ? context.attr('data-slide-count-sm') : typeof context.attr('data-slide-count') !== 'undefined' ? parseInt(context.attr('data-slide-count')) : 'auto',
              spaceBetween: typeof context.attr('data-space-between') !== 'undefined' ? parseInt(context.attr('data-space-between')) : 20
            },
            768: {
              slidesPerView:
                typeof context.attr('data-slide-count-md') !== 'undefined'
                  ? context.attr('data-slide-count-md')
                  : typeof context.attr('data-slide-count-sm') !== 'undefined'
                    ? context.attr('data-slide-count-sm')
                    : typeof context.attr('data-slide-count') !== 'undefined'
                      ? parseInt(context.attr('data-slide-count'))
                      : 'auto',
              spaceBetween: typeof context.attr('data-space-between') !== 'undefined' ? parseInt(context.attr('data-space-between')) : 20
            },
            1024: {
              slidesPerView:
                typeof context.attr('data-slide-count-lg') !== 'undefined'
                  ? context.attr('data-slide-count-lg')
                  : typeof context.attr('data-slide-count-md') !== 'undefined'
                    ? context.attr('data-slide-count-md')
                    : typeof context.attr('data-slide-count-sm') !== 'undefined'
                      ? context.attr('data-slide-count-sm')
                      : typeof context.attr('data-slide-count') !== 'undefined'
                        ? parseInt(context.attr('data-slide-count'))
                        : 'auto',
              spaceBetween: typeof context.attr('data-space-between') !== 'undefined' ? parseInt(context.attr('data-space-between')) : 20
            },
            1280: {
              slidesPerView:
                typeof context.attr('data-slide-count-xl') !== 'undefined'
                  ? context.attr('data-slide-count-xl')
                  : typeof context.attr('data-slide-count-lg') !== 'undefined'
                    ? context.attr('data-slide-count-lg')
                    : typeof context.attr('data-slide-count-md') !== 'undefined'
                      ? context.attr('data-slide-count-md')
                      : typeof context.attr('data-slide-count-sm') !== 'undefined'
                        ? context.attr('data-slide-count-sm')
                        : typeof context.attr('data-slide-count') !== 'undefined'
                          ? parseInt(context.attr('data-slide-count'))
                          : 'auto',
              spaceBetween: typeof context.attr('data-space-between') !== 'undefined' ? parseInt(context.attr('data-space-between')) : 20
            }
          },
          pagination: {
            el: '.swiper-pagination_' + _unique,
            clickable: true
          },
          navigation: {
            prevEl: '.swiper-button-prev',
            nextEl: '.swiper-button-next'
          }
        });
      });
    });
  }

  /**
   * Load video player (mediaelement.js)
   */
  if ($('[data-role=videoplayer]').length && (!individual.length || individual.includes('videoplayer')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/mediaelement/mediaelement.min.css']);
    require.js([config.baseUrl + 'assets/mediaelement/mediaelement.min.js'], function () {
      $('[data-role=videoplayer]').each(function () {
        $(this).mediaelementplayer({
          pauseOtherPlayers: true,
          stretching: 'fill',
          iconSprite: config.baseUrl + 'assets/mediaelement/mejs-controls.svg',
          success: function (player, node) {
            player.pause();
          }
        });
      });
    });
  }

  /**
   * Load datetimepicker plugin
   */
  if ($('[data-role=calendar]').length && (!individual.length || individual.includes('calendar')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/fullcalendar/fullcalendar.min.css']);
    require.js([config.baseUrl + 'assets/fullcalendar/fullcalendar.min.js'], function () {
      $('[data-role=calendar]').each(function () {
        let context = $(this);
        let calendar = new FullCalendar.Calendar(document.getElementById(context.attr('id')), {
          timeZone: 'GMT',
          slotMinTime: context.attr('data-start-time') ? context.attr('data-start-time') : '08:00:00',
          slotMaxTime: context.attr('data-start-time') ? context.attr('data-start-time') : '22:00:00',
          height: fullHeight - (($('[data-role=toolbar]').outerHeight(true) ?? 0) + ($('[data-role=toolbar]').outerHeight(true) ?? 0) + ($('[data-role=pagination]').outerHeight(true) ?? 0)),
          initialView: 'timeGridWeek',
          headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: {
            url: context.attr('data-event-url'),
            method: 'POST'
          },
          selectable: context.attr('data-crud-url') ? true : false,
          select: function (info) {
            let crudUrl = context.attr('data-crud-url');

            if (crudUrl) {
              try {
                const url = new URL(crudUrl, window.location.origin);

                if (url.origin !== window.location.origin) {
                  return;
                }

                const link = document.createElement('a');

                link.href = url.href;
                link.setAttribute('data-start', info.startStr);
                link.setAttribute('data-finish', info.endStr);
                link.className = '--modal';

                document.body.appendChild(link);
                link.click();
                link.remove();
              } catch (e) {}
            }
          }
        });

        calendar.render();
      });
    });
  }

  /**
   * Load money format plugin
   */
  if ($('[data-role=money]').length && (!individual.length || individual.includes('money')) && !ignoreSelf) {
    require.js([config.baseUrl + 'assets/autonumeric/autonumeric.min.js'], function () {
      let moneyOptions = {
        vMin: '-999999999.99'
      };

      let decimalCommaLanguages = ['id', 'de', 'es', 'it', 'nl', 'pt', 'tr', 'vi', 'da', 'el', 'is', 'ro', 'sl'];

      if (decimalCommaLanguages.includes(config.language)) {
        moneyOptions.aSep = '.';
        moneyOptions.aDec = ',';
      }

      $('[data-role=money]').autoNumeric('init', moneyOptions);
      $('[data-role=money]').on('focus', function (e) {
        $(this).select();
      });
    });
  }

  /**
   * Uploader plugin
   */
  if ($('[data-role=uploader]').length && (!individual.length || individual.includes('uploader')) && !ignoreSelf) {
    $('[data-role=uploader]').each(function () {
      let context = $(this);
      let files = [];
      let getFileExtension = function (file) {
        let source = file && (file.file || file.name || file.url) ? (file.file || file.name || file.url).toString() : '';

        try {
          source = decodeURIComponent(source);
        } catch (e) {}

        source = source.split(/[?#]/)[0].split('/').pop();

        let match = source.match(/\.([a-z0-9]+)$/i);

        return match ? '.' + match[1].toLowerCase() : '';
      };

      try {
        files = JSON.parse(context.attr('data-fileuploader-files'));
      } catch (e) {}

      context.removeAttr('data-fileuploader-files');

      if (files) {
        $.each(files, function (key, val) {
          let extension = getFileExtension(val);

          $(`
            <div class="uploader-list w-100">
              <div class="row mb-2 align-items-center">
                <div class="col-2 pe-0">
                  <a href="${htmlspecialchars(val.url)}" target="_blank" download="${htmlspecialchars(val.name)}">
                    ${val.icon ? '<img src="' + htmlspecialchars(val.icon) + '" class="img-fluid rounded" style="max-height:32px" alt="' + htmlspecialchars(val.name || '') + '" loading="lazy" decoding="async" />' : '<button type="button" class="btn btn-primary pe-2 ps-2">' + htmlspecialchars(extension) + '</button>'}
                  </a>
                </div>
                <div class="col-10 position-relative">
                  <button type="button" class="btn btn-sm position-absolute end-0 me-2" onclick="jExec($(this).closest('.input-group').find('.custom-file-label').text(($(this).closest('.input-group').find('textarea').length - 1) + ' ' + (($(this).closest('.uploader-list').find('textarea').length - 1) > 1 ? phrase('files were chosen') : phrase('file was chosen'))), $('[data-bs-toggle=tooltip]').tooltip('hide'), $(this).closest('.uploader-list').remove())" data-bs-toggle="tooltip" title="${htmlspecialchars(phrase('Remove'))}">
                    <i class="mdi mdi-delete text-danger"></i>
                  </button>
                  <textarea name="${htmlspecialchars(context.attr('name').replace('[]', ''))}_label[${htmlspecialchars(val.file)}]" class="form-control form-control-sm" rows="1">${htmlspecialchars(val.name)}</textarea>
                </div>
              </div>
            </div>
          `).insertBefore(context.closest('.uploader-input'));
        });

        const labelText = files.length + ' ' + (files.length > 1 ? phrase('files were chosen') : phrase('file was chosen'));
        context.next('.custom-file-label').text(labelText);

        $('[data-bs-toggle=tooltip]').tooltip();
      }

      context.off('change.uploader');
      context.on('change.uploader', function (e) {
        // Prevent browser to take place as well
        e.preventDefault();

        let context = $(this);

        if (this.files && this.files[0]) {
          let file = this.files[0];
          let extension = getFileExtension(file);
          let reader = new FileReader();

          if (context.attr('accept') && $.inArray(extension, context.attr('accept').split(',')) === -1) {
            return throw_exception(403, phrase('Allowed file type') + ' <b>' + context.attr('accept') + '</b>.');
          } else if (file.size / 1024 > config.maxUploadSize) {
            return throw_exception(403, file.name + ' ' + phrase('is too large'));
          }

          reader.readAsDataURL(this.files[0]);

          reader.onload = function (response) {
            if (!response.target.error) {
              const uploaderInput = context.closest('.uploader-input');
              if (!context.attr('multiple')) {
                // Remove previous selected file for non-multiple input
                uploaderInput.prev('.uploader-list').remove();
              }

              $(`
                <div class="uploader-list w-100">
                  <div class="row mb-2 align-items-center">
                    <div class="col-2 pe-0">
                      <a href="${htmlspecialchars(response.target.result)}" target="_blank" download="${htmlspecialchars(file.name)}">
                        ${$.inArray(extension, ['.jpg', '.jpeg', '.png', '.gif']) !== -1 ? '<img src="' + htmlspecialchars(response.target.result) + '" class="img-fluid rounded" style="max-height:32px" alt="' + htmlspecialchars(file.name || '') + '" loading="lazy" decoding="async" />' : '<button type="button" class="btn btn-primary pe-2 ps-2">' + htmlspecialchars(extension) + '</button>'}
                      </a>
                    </div>
                    <div class="col-10 position-relative uploader-file-input">
                      <button type="button" class="btn btn-sm position-absolute end-0 me-2" onclick="jExec($(this).closest('.input-group').find('.custom-file-label').text(($(this).closest('.input-group').find('textarea').length - 1) + ' ' + (($(this).closest('.input-group').find('textarea').length - 1) > 1 ? phrase('files were chosen') : phrase('file was chosen'))), $('[data-bs-toggle=tooltip]').tooltip('hide'), $(this).closest('.uploader-list').remove())" data-bs-toggle="tooltip" title="${htmlspecialchars(phrase('Remove'))}">
                        <i class="mdi mdi-delete text-danger"></i>
                      </button>
                      <textarea name="${htmlspecialchars(context.attr('name').replace('[]', ''))}_label[]" class="form-control form-control-sm" rows="1">${htmlspecialchars(file.name)}</textarea>
                    </div>
                  </div>
                </div>
              `).insertBefore(uploaderInput);

              const targetList = uploaderInput.prev('.uploader-list');
              context.clone().css('display', 'none').removeAttr('data-role class id').appendTo(targetList);

              const textareaCount = context.closest('.input-group').find('textarea').length;
              const statusText = textareaCount + ' ' + (textareaCount > 1 ? phrase('files were chosen') : phrase('file was chosen'));
              context.next('.custom-file-label').text(statusText);

              context.val('');

              $('[data-bs-toggle=tooltip]').tooltip();
            }
          };
        }
      });
    });
  }

  /**
   * Checkbox
   */
  if ($('input[data-role=checker]').length && (!individual.length || individual.includes('checker')) && !ignoreSelf) {
    $('input[data-role=checker]').each(function () {
      let _parent = $(this).attr('data-parent');

      if ($(this).closest(_parent).find(':checkbox.checker-children:checked').length) {
        $(this).prop('checked', true);
      } else {
        $(this).prop('checked', false);
      }
    });
  }

  /**
   * Autocomplete plugin
   */
  if ($('[data-role=autocomplete]').length && (!individual.length || individual.includes('autocomplete')) && !ignoreSelf) {
    require.js([config.baseUrl + 'assets/autocomplete/autocomplete.min.js'], function () {
      $('[data-role=autocomplete]').each(function () {
        let context = jQuery(this);

        context.on('input', function (e) {
          if (!context.val()) {
            context.next('input[type=hidden]').remove();
          }
        });
        context.autocomplete({
          serviceUrl: context.attr('data-href') ? context.attr('data-href') : context.closest('form').attr('action'),
          params: {
            method: 'autocomplete',
            origin: context.attr('name'),
            siblings: function () {
              let data = {};
              $.each(_this.closest('form').find(':input').not('[type=file]').not(_this).serializeArray(), function (key, val) {
                data[val.name] = val.value;
              });

              return JSON.stringify(data);
            }
          },
          minChars: 3,
          zIndex: 1056,
          appendTo: _this.data('append-to') ? _this.closest(_this.data('append-to')) : 'body',
          noSuggestionNotice: phrase('Nothing found'),
          triggerSelectOnValidInput: false,
          onSelect: function (suggestion) {
            if (typeof suggestion.target !== 'undefined' && suggestion.target) {
              // Create and click the temporary link
              $(document.createElement('a')).attr('href', suggestion.target).addClass('--xhr').appendTo('body').trigger('click').remove();
            } else if (context.closest('form').hasClass('--xhr-form')) {
              context.closest('form.--xhr-form').trigger('submit');
            } else if (context.hasClass('on-autocomplete-trigger')) {
              $('input[data-mask-input=autocomplete]').remove();
              $(document.createElement('input'))
                .attr({
                  type: 'hidden',
                  name: context.attr('name'),
                  value: suggestion.value,
                  'data-mask-input': 'autocomplete'
                })
                .insertAfter(context);
            }

            if (typeof suggestion.affected_field !== 'undefined') {
              $.each(suggestion.affected_field, function (key, val) {
                _this
                  .closest('form')
                  .find('input[name=' + key + ']')
                  .val(val)
                  .trigger('change');
              });
            }
          },
          onSearchComplete: function (query, suggestion) {
            // Additional trigger on search complete
          }
        });
      });
    });
  }

  /**
   * Fetch additional file to initialize openlayers map
   */
  if ($('[data-role=map]').length && (!individual.length || individual.includes('map')) && !ignoreSelf) {
    $('[data-role=map]').each(function () {
      let context = $(this);

      require.css([
        config.baseUrl + 'assets/openlayers/ol.min.css',
        config.baseUrl + 'assets/openlayers/ol-geocoder/ol-geocoder.min.css',
        config.baseUrl + 'assets/openlayers/ol-popup/ol-popup.min.css',
        config.baseUrl + 'assets/local/css/openlayers.min.css'
      ]);

      require.js(
        [
          config.baseUrl + 'assets/jszip/jszip-utils.min.js',
          config.baseUrl + 'assets/jszip/jszip.min.js',
          config.baseUrl + 'assets/openlayers/ol.min.js',
          config.baseUrl + 'assets/openlayers/ol-geocoder/ol-geocoder.min.js',
          config.baseUrl + 'assets/openlayers/ol-popup/ol-popup.min.js',
          config.baseUrl + 'assets/openlayers/ol-dropfile/ol-dropfile.min.js',
          config.baseUrl + 'assets/local/js/openlayers.min.js'
        ],
        function () {
          openlayers.render(context);

          $('.ol-zoom-in, .ol-zoom-out, .ol-zoom-extent button, .ol-track button, .ol-attribution button, .ol-rotate-reset button').tooltip({
            placement: 'right'
          });
        }
      );
    });
  }

  /**
   * Fetch additional file to initialize syntax highlighter
   */
  if ($('pre code').length && (!individual.length || individual.includes('code')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/prism/prism.min.css']);
    require.js([config.baseUrl + 'assets/prism/prism.min.js'], function () {
      Prism.highlightAll();
    });
  }

  /**
   * Fetch additional file to initialize iconpicker
   */
  if ($('[data-role=iconpicker]').length && (!individual.length || individual.includes('iconpicker')) && !ignoreSelf) {
    require.css([config.baseUrl + 'assets/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css']);
    require.js([config.baseUrl + 'assets/bootstrap-iconpicker/js/iconset/materialdesignicons.3.9.97.min.js', config.baseUrl + 'assets/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js'], function () {
      $('[data-role=iconpicker]').iconpicker({
        searchText: phrase('Search')
      });
    });
  }

  /**
   * Fetch additional file if to initialize sortable
   */
  if ($('[data-role=sortable-menu]').length && (!individual.length || individual.includes('sortable-menu')) && !ignoreSelf) {
    $('[data-role=sortable-menu]').each(function () {
      let context = $(this),
        maxDepth = 10;
      let sanitizeMenu = function (items) {
        let menus = [];

        $.each(items, function (key, item) {
          if (typeof item.id === 'undefined' || typeof item.label === 'undefined' || typeof item.slug === 'undefined') return;

          item.children = item.children ? sanitizeMenu(item.children) : [];
          menus.push(item);
        });

        return menus;
      };
      let serializeMenu = function () {
        return JSON.stringify(sanitizeMenu(context.sortableToHierarchy()));
      };

      require.css([config.baseUrl + 'assets/sortable/sortable.min.css']);
      require.js([config.baseUrl + 'assets/sortable/sortable.min.js'], function () {
        context
          .sortable({
            maxDepth: maxDepth,
            selector: 'ul',
            list: 'li',
            onChange: function (e) {
              context.next('.menu_structure').val(serializeMenu());
            }
          })
          .on('click', '.item-add', function (e) {
            if ($(this).parents('ul').length >= maxDepth) return;

            let new_item = context.find('li.item-placeholder').prop('outerHTML');

            new_item = new_item.replace('{{id}}', $(this).parents('ul').children('li').length);
            new_item = new_item.replace(' item-placeholder hidden"', '');
            new_item = new_item.replace('aria-label="', 'title="');

            if ($(this).hasClass('children')) {
              if ($(this).closest('li').find('ul:first').length) {
                $(new_item).appendTo($(this).closest('li').find('ul:first'));
              } else {
                $('<ul>' + new_item + '</ul>').appendTo($(this).closest('li'));
              }
            } else {
              $(new_item).appendTo(context);
            }

            $('[data-role=iconpicker]').iconpicker({
              searchText: phrase('Search')
            });
            $('[data-bs-toggle=tooltip]').tooltip();
            context.trigger('change');
          })
          .on('click', '.item-remove', function (e) {
            e.preventDefault();

            $(this)
              .closest('li')
              .slideToggle(200, function () {
                $(this).remove();
                context.trigger('change');
              });
          })
          .on('change', '.menu-icon', function () {
            let _icon = $(this).find('i').attr('class');

            $(this).closest('li').attr('data-icon', _icon).parents(context).trigger('change');
          })
          .on('change keyup', '.menu-label', function (e) {
            $(this).closest('li').attr('data-label', $(this).val()).parents(context).trigger('change');
          })
          .on('change keyup', '.menu-slug', function () {
            $(this).closest('li').attr('data-slug', $(this).val()).parents(context).trigger('change');
          })
          .on('click touch', '.menu-newtab', function () {
            $(this)
              .closest('li')
              .attr('data-newtab', $(this).is(':checked') ? 1 : 0)
              .parents(context)
              .trigger('change');
          })
          .on('change', function (e) {
            context.next('.menu_structure').val(serializeMenu());
          });
      });
    });
  }

  /**
   * Sortable
   */
  if ($('[data-role=sortable]').length && (!individual.length || individual.includes('sortable')) && !ignoreSelf) {
    $('[data-role="sortable"]').each(function () {
      let context = $(this);

      require.js([config.baseUrl + 'assets/sortable/jquery-ui.sortable.min.js'], function () {
        context.sortable({
          axis: 'y',
          containment: 'parent',
          helper: function (e, row) {
            const $originals = row.children();
            const $helper = row.clone();
            $helper.children().each(function (index) {
              $(this).width($originals.eq(index).width());
            });

            return $helper;
          },
          update: function (event, ui) {
            let orderedIDs = context
              .children()
              .map(function () {
                return $(this).data('id');
              })
              .get();

            if (context.data('url')) {
              $.ajax({
                url: context.data('url'),
                method: 'POST',
                data: {
                  method: 'sort_table',
                  ordered_id: orderedIDs
                }
              });
            }
          }
        });
      });
    });
  }

  /**
   * Widgets
   */
  if ($('[data-role=widget]').length && (!individual.length || individual.includes('widget')) && !ignoreSelf) {
    $('[data-role="widget"]').each(function () {
      $.ajax({
        url: $(this).attr('data-source'),
        context: this
      }).done(function (response) {
        if (typeof response.content !== 'undefined') {
          $(this).html(response.content);

          const _this = $(this);
          setTimeout(function () {
            _this.find('.--fetch-comments').trigger('click');
          }, 1000);
        }

        reactivate([], true);
      });
    });
  }

  /**
   * Notification's polling
   */
  if ($('[data-role=notifications]').length) {
    // Fetch notification
    fetchNotifications();

    // Start polling
    polling = setInterval(fetchNotifications, 10000);
  }
}

/**
 * Apply on resize context
 */
function _viewport_modifier() {
  /**
   * Check again the browser viewport
   */
  fullHeight = window.innerHeight - (($('[data-role=header]:visible').outerHeight(true) ?? 0) + ($('[data-role=footer]:visible').outerHeight(true) ?? 0) + ($('[data-role=breadcrumb]:visible').outerHeight(true) ?? 0));

  $('.full-height').css({
    minHeight: fullHeight - (($('[data-role=meta]:visible').outerHeight(true) ?? 0) + ($('[data-role=toolbar]:visible').outerHeight(true) ?? 0) + ($('[data-role=pagination]:visible').outerHeight(true) ?? 0))
  });

  if (UA != 'mobile') {
    require.css([config.baseUrl + 'assets/overlayscrollbars/overlayscrollbars.min.css']);
    require.js([config.baseUrl + 'assets/overlayscrollbars/overlayscrollbars.min.js'], function () {
      const isExpanded = $('body').hasClass('content-expanded');
      const sidebarWidth = isExpanded || !$('[data-role=sidebar]:visible').length ? 0 : ($('[data-role=sidebar]:visible').outerWidth(true) ?? 0);
      const tableWidth = isExpanded ? $(window).outerWidth(true) : $(window).outerWidth(true) - (sidebarWidth + 24);

      $('[data-role=sidebar]').height(fullHeight - 48);
      $('[data-role=table]').width(tableWidth);
      $('[data-role=table]').height(fullHeight - (($('[data-role=meta]:visible').outerHeight(true) ?? 0) + ($('[data-role=toolbar]:visible').outerHeight(true) ?? 0) + ($('[data-role=pagination]:visible').outerHeight(true) ?? 0)));
      $('[data-role=grid]').height(fullHeight - (($('[data-role=meta]:visible').outerHeight(true) ?? 0) + ($('[data-role=toolbar]:visible').outerHeight(true) ?? 0) + ($('[data-role=pagination]:visible').outerHeight(true) ?? 0)));

      // Initialize or update OverlayScrollbars
      const osOptions = {
        overflow: {
          x: 'hidden',
          y: 'scroll'
        },
        scrollbars: {
          theme: 'os-theme-dark',
          autoHide: 'leave',
          autoHideDelay: 500,
          clickScroll: true
        }
      };

      $('[data-role=sidebar], [data-role=table], [data-role=grid], .pretty-scrollbar').each(function () {
        const instance = typeof OverlayScrollbarsGlobal !== 'undefined' && OverlayScrollbarsGlobal.OverlayScrollbars ? OverlayScrollbarsGlobal.OverlayScrollbars(this) : null;
        if (instance) {
          instance.update(true);
        } else if (typeof OverlayScrollbarsGlobal !== 'undefined' && OverlayScrollbarsGlobal.OverlayScrollbars) {
          OverlayScrollbarsGlobal.OverlayScrollbars(this, osOptions);
        }
      });
    });
  }
}
