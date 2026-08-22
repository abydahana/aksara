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

  // Apply saved sidebar collapse state immediately on desktop
  if (window.innerWidth >= 992 && localStorage.getItem('sidebar-collapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
  }

  document.addEventListener('DOMContentLoaded', function () {
    const icons = document.querySelectorAll('[data-sidebar-toggle-icon]');
    const toggles = document.querySelectorAll('.mobile-menu-toggle[data-toggle="sidebar"]');

    if (!icons.length && !toggles.length) {
      return;
    }

    const updateSidebarIcon = function () {
      const isDesktop = window.innerWidth >= 992;
      const isCollapsed = isDesktop ? document.body.classList.contains('sidebar-collapsed') : !document.body.classList.contains('sidebar-expanded');
      const isExpanded = !isCollapsed;

      icons.forEach(function (icon) {
        icon.classList.toggle('mdi-chevron-right', isCollapsed);
        icon.classList.toggle('mdi-chevron-left', !isCollapsed);
      });

      toggles.forEach(function (toggle) {
        toggle.classList.toggle('is-open', isExpanded);
        toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
      });
    };

    const observer = new MutationObserver(function () {
      updateSidebarIcon();
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

    document.body.addEventListener('click', function (event) {
      if (event.target.closest('[data-toggle="sidebar"]')) {
        setTimeout(function () {
          if (window.innerWidth >= 992) {
            const collapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');
          }
          updateSidebarIcon();
        }, 0);
      }
    });

    updateSidebarIcon();
  });
})();

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
   * Toggle fullscreen
   */
  $('body').on('click touch', '[data-toggle=fullscreen]', function (e) {
    e.preventDefault();

    if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document.webkitIsFullScreen)) {
      var success = false;

      if (document.documentElement.requestFullScreen) {
        success = true;

        document.documentElement.requestFullScreen();
      } else if (document.documentElement.mozRequestFullScreen) {
        success = true;

        document.documentElement.mozRequestFullScreen();
      } else if (document.documentElement.webkitRequestFullScreen) {
        success = true;

        document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
      }

      if (success) {
        $(this).find('.mdi').removeClass('mdi-fullscreen').addClass('mdi-fullscreen-exit');
      }
    } else {
      var success = false;

      if (document.cancelFullScreen) {
        success = true;

        document.cancelFullScreen();
      } else if (document.mozCancelFullScreen) {
        success = true;

        document.mozCancelFullScreen();
      } else if (document.webkitCancelFullScreen) {
        success = true;

        document.webkitCancelFullScreen();
      }

      if (success) {
        $(this).find('.mdi').removeClass('mdi-fullscreen-exit').addClass('mdi-fullscreen');
      }
    }
  });

  /**
   * Notification picker
   */
  $('body').on('click touch', '[data-role=notifications]', function (e) {
    let context = $(this);

    $.ajax({
      url: $(this).attr('href'),
      method: 'POST',
      context: this,
      data: {
        prefer: 'dropdown'
      },
      beforeSend: function () {
        context.closest('li').find('ul').html('');
      }
    })
      .done(function (response) {
        if (typeof response === 'object') {
          context.closest('li').find('ul').css('minWidth', 340);
          context.closest('li').find('ul').html(`
            <li class="nav-item px-3 mb-2 d-none d-md-block">
              <h5>
                <i class="mdi mdi-bell-ring"></i> ${phrase('Notifications')}
              </h5>
            </li>
          `);

          if (response.length) {
            $.each(response, function (key, val) {
              $(`
                <li class="nav-item px-2 mb-2">
                  <a href="${val.url}" class="nav-link rounded --xhr" target="${val.target}">
                    <div class="row g-0">
                      <div class="col-2">
                        <div class="position-relative">
                          <i class="mdi ${'comment' === val.type ? 'mdi-comment-processing bg-success' : 'reply' === val.type ? 'mdi-reply bg-dark' : 'like' === val.type ? 'mdi-thumb-up bg-primary' : 'upvote' === val.type ? 'mdi-arrow-up-circle bg-info' : 'mdi-heart bg-danger'} text-light px-1 rounded-circle gradient position-absolute end-0 bottom-0"></i>
                          <img src="${val.avatar}" class="rounded-circle img-fluid" alt="${val.user}" loading="lazy" decoding="async" />
                        </div>
                      </div>
                      <div class="col-10 ps-2">
                        <p class="mb-0">
                          <b>${val.user}</b> ${val.text}
                        </p>
                        <p class="mb-0 text-muted">
                          ${val.created_at}
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              `).appendTo(context.closest('li').find('ul'));
            });
          } else {
            $(`
              <li class="nav-item px-3 mb-2">
                <div class="text-warning">
                  ${phrase('You have no notification at the moment.')}
                </div>
              </li>
            `).appendTo(context.closest('li').find('ul'));
          }
        }
      })
      .fail(function (response, textStatus, errorThrown) {
        if (textStatus === 'abort') {
          return;
        }

        if (typeof response.responseJSON !== 'undefined') {
          // Get response JSON
          response = response.responseJSON;
        }

        return throw_exception(response.code, response?.message);
      });
  });

  /**
   * Toggle theme dark/light
   */
  // Set initial icon based on saved theme
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
      $(this).find('.mdi').removeClass('mdi-weather-night').addClass('mdi-white-balance-sunny');
    } else {
      $(this).find('.mdi').removeClass('mdi-white-balance-sunny').addClass('mdi-weather-night');
    }

    if (typeof updateStatusBarColor === 'function') {
      setTimeout(function () {
        updateStatusBarColor();
      }, 0);
    }

    syncTheme(next);
  });

  // The .off() method removes event handlers that were attached with .on()
  ($('body').off('change.layer_type'),
    $('body').on('change.layer_type', 'input[name=layer_type]', function (e) {
      // Modify text label on input changes
      if (['polygon', 'linestring'].includes($(this).val())) {
        $(this)
          .closest('form')
          .find('label[for=icon_scale_input]')
          .html(phrase('Opacity') + ' <span class="text-danger font-weight-bold">*</span>');
      } else {
        $(this)
          .closest('form')
          .find('label[for=icon_scale_input]')
          .html(phrase('Icon Scale') + ' <span class="text-danger font-weight-bold">*</span>');
      }
    }));
});

/**
 * Include your function into afterCall to run it after ajax call
 */
afterCall.push(function () {
  if ($('input[name=layer_type]').length) {
    $('input[name=layer_type]').trigger('change');
  }
});
