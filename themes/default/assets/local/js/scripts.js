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

/* ──────────────────────────────────────────────
   Bottom Sheet Drag-to-Close
   Shared by theme navigation and page sheets
   ────────────────────────────────────────────── */
(function () {
  function initBottomSheetDrag(sheetEl) {
    if (!sheetEl || sheetEl.dataset.dragToCloseReady === '1') return;

    var header = sheetEl.querySelector('.offcanvas-header');
    if (!header) return;

    var startY = 0;
    var startTime = 0;
    var isDragging = false;

    function resetDrag() {
      isDragging = false;
      sheetEl.classList.remove('is-dragging');
      sheetEl.style.removeProperty('--sheet-drag-y');
    }

    function closeSheet() {
      sheetEl.style.setProperty('--sheet-drag-y', '100vh');
      setTimeout(function () {
        var inst = bootstrap.Offcanvas.getInstance(sheetEl);
        if (inst) inst.hide();
      }, 300);
    }

    header.addEventListener(
      'touchstart',
      function (e) {
        if (!e.touches || !e.touches.length) return;
        startY = e.touches[0].clientY;
        startTime = Date.now();
        isDragging = true;
        sheetEl.classList.add('is-dragging');
      },
      { passive: true }
    );

    header.addEventListener(
      'touchmove',
      function (e) {
        if (!isDragging || !e.touches || !e.touches.length) return;
        var dy = e.touches[0].clientY - startY;
        if (dy < 0) dy = 0;
        sheetEl.style.setProperty('--sheet-drag-y', dy + 'px');
      },
      { passive: true }
    );

    function onEnd() {
      if (!isDragging) return;

      var dy = parseFloat(sheetEl.style.getPropertyValue('--sheet-drag-y')) || 0;
      var dt = Math.max(1, Date.now() - startTime);
      var velocity = dy / dt;

      isDragging = false;
      sheetEl.classList.remove('is-dragging');

      if (velocity > 0.4 || dy > sheetEl.offsetHeight * 0.35) {
        closeSheet();
      } else {
        sheetEl.style.setProperty('--sheet-drag-y', '0px');
        setTimeout(function () {
          sheetEl.style.removeProperty('--sheet-drag-y');
        }, 300);
      }
    }

    header.addEventListener('touchend', onEnd);
    header.addEventListener('touchcancel', onEnd);
    sheetEl.addEventListener('hidden.bs.offcanvas', resetDrag);
    sheetEl.dataset.dragToCloseReady = '1';
  }

  function initAllBottomSheetDrag(root) {
    var scope = root || document;
    if (scope.matches && scope.matches('.offcanvas.offcanvas-bottom')) {
      initBottomSheetDrag(scope);
    }
    scope.querySelectorAll('.offcanvas.offcanvas-bottom').forEach(initBottomSheetDrag);
  }

  window.initBottomSheetDrag = initBottomSheetDrag;
  window.initAllBottomSheetDrag = initAllBottomSheetDrag;

  document.addEventListener('DOMContentLoaded', function () {
    initAllBottomSheetDrag(document);
  });

  document.addEventListener('show.bs.offcanvas', function (e) {
    initBottomSheetDrag(e.target);
  });
})();

/* ──────────────────────────────────────────────
   Bottom Sheet Sub-Menu System
   Handles unlimited nesting depth via stacked sheets
   ────────────────────────────────────────────── */
$(document).ready(function () {
  var isMobile = function () {
    return window.innerWidth < 992;
  };

  /* Track open sub-sheets as a stack */
  var subSheetStack = [];

  /**
   * Build sub-sheet items from a Bootstrap dropdown-menu <ul>
   * Returns an array of { html, icon, label, href, children (jQuery <ul>) }
   */
  function parseDropdownItems($ul) {
    var items = [];

    $ul.children('li').each(function () {
      var $li = $(this);
      var $a = $li.children('a').first();
      var $hr = $li.children('hr');

      if ($hr.length) {
        items.push({ type: 'divider' });
        return;
      }

      if (!$a.length) return;

      var icon = '';
      var $icon = $a.children('i.mdi');
      if ($icon.length) {
        icon = $icon[0].outerHTML;
      }

      var label = '';
      var $span = $a.children('span.hide-on-collapse');
      if ($span.length) {
        label = $span.text().trim();
      } else {
        $span = $a.children('span');
        if ($span.length) {
          label = $span.text().trim();
        } else {
          // Fallback: text content minus icon
          label = $a.clone().children('i').remove().end().text().trim();
        }
      }

      var href = $a.attr('href') || '#';
      var $childUl = $li.children('ul.dropdown-menu');
      var hasChildren = $childUl.length > 0;
      var extraClass = $a.attr('class') || '';
      // Preserve specific class flags
      var isNoAjax = extraClass.indexOf('no-ajax') !== -1;
      var isXhr = extraClass.indexOf('--xhr') !== -1;
      var isDanger = extraClass.indexOf('text-danger') !== -1;

      items.push({
        type: 'link',
        icon: icon,
        label: label,
        href: href,
        hasChildren: hasChildren,
        $childUl: hasChildren ? $childUl : null,
        isNoAjax: isNoAjax,
        isXhr: isXhr,
        isDanger: isDanger
      });
    });

    return items;
  }

  /**
   * Create and show a sub-sheet
   */
  function openSubSheet(title, items) {
    /* Backdrop (only one, shared) */
    var $backdrop = $('.sub-sheet-backdrop');
    if (!$backdrop.length) {
      $backdrop = $('<div class="sub-sheet-backdrop"></div>').appendTo('body');
    }

    /* Build list element */
    var $ul = $('<ul class="sub-sheet-list"></ul>');
    items.forEach(function (item) {
      if (item.type === 'divider') {
        $ul.append('<li><hr style="border-color:var(--bs-sheet-divider);margin:4px 16px"></li>');
        return;
      }

      var $a = $(document.createElement('a')).attr('href', item.href || '#');
      if (item.isNoAjax) $a.addClass('no-ajax');
      if (item.isXhr) $a.addClass('--xhr');
      if (item.isDanger) $a.addClass('text-danger');
      if (item.hasChildren) $a.attr('data-bs-submenu', 'true');

      if (item.icon) {
        $a.append($(item.icon));
      }
      $a.append($('<span></span>').text(item.label || ''));

      $ul.append($('<li></li>').append($a));
    });

    /* Panel */
    var $sheet = $(
      [
        '<div class="bottom-sub-sheet">',
        '<div class="sub-sheet-header" style="position:relative">',
        '<div class="drag-handle"></div>',
        '<button type="button" class="sub-sheet-back" aria-label="Back">&lsaquo;</button>',
        '<span class="sub-sheet-title"></span>',
        '</div>',
        '<div class="sub-sheet-body"></div>',
        '</div>'
      ].join('')
    );

    $sheet.find('.sub-sheet-title').text(title || '');
    $sheet.find('.sub-sheet-body').append($ul);

    $sheet.appendTo('body');

    /* Keep a reference to items for child navigation */
    $sheet.data('items', items);

    /* Push to stack and assign z-index based on open order */
    subSheetStack.push($sheet);
    var sheetZ = 1070 + subSheetStack.length * 2;
    $sheet.css('z-index', sheetZ);
    updateBackdropZ();

    /* Show with rAF for transition */
    requestAnimationFrame(function () {
      $backdrop.addClass('show');
      $sheet.addClass('show');
      $('body').addClass('bottom-sheet-open');
    });

    /* Bind events inside this sheet */
    $sheet.find('[data-bs-submenu]').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var idx = $(this).closest('li').index();
      // Find dividers before this index to adjust
      var realIdx = 0,
        count = 0;
      $sheet.find('.sub-sheet-list > li').each(function (i) {
        if (i === idx) {
          return false; // break
        }
        if (!$(this).find('hr').length) {
          count++;
        }
      });

      // Match with items array (skip dividers)
      var itemIndex = 0,
        nonDividerCount = 0;
      for (var i = 0; i < items.length; i++) {
        if (items[i].type === 'divider') continue;
        if (nonDividerCount === count) {
          itemIndex = i;
          break;
        }
        nonDividerCount++;
      }

      var item = items[itemIndex];
      if (item && item.hasChildren && item.$childUl) {
        var childItems = parseDropdownItems(item.$childUl);
        openSubSheet(item.label, childItems);
      }
    });

    /* Back button */
    $sheet.find('.sub-sheet-back').on('click', function () {
      closeTopSubSheet();
    });

    /* Backdrop tap closes all */
    $backdrop.off('click.subsheet').on('click.subsheet', function () {
      closeAllSubSheets();
    });
  }

  /**
   * Close the top-most sub-sheet
   */
  function closeTopSubSheet() {
    if (!subSheetStack.length) return;

    var $sheet = subSheetStack.pop();
    $sheet.removeClass('show');

    setTimeout(function () {
      $sheet.remove();
    }, 380);

    if (subSheetStack.length === 0) {
      $('.sub-sheet-backdrop').removeClass('show');
      $('body').removeClass('bottom-sheet-open');
    } else {
      updateBackdropZ();
    }
  }

  /**
   * Close all sub-sheets
   */
  function closeAllSubSheets() {
    subSheetStack.forEach(function ($s) {
      $s.removeClass('show');
      setTimeout(function () {
        $s.remove();
      }, 380);
    });
    subSheetStack = [];
    $('.sub-sheet-backdrop').removeClass('show');
    $('body').removeClass('bottom-sheet-open');
  }

  /**
   * Keep backdrop z-index just below the top-most sheet
   */
  function updateBackdropZ() {
    if (subSheetStack.length) {
      var topSheetZ = parseInt(subSheetStack[subSheetStack.length - 1].css('z-index'), 10) || 1058;
      $('.sub-sheet-backdrop').css('z-index', topSheetZ - 1);
    } else {
      $('.sub-sheet-backdrop').css('z-index', 1055);
    }
  }

  /**
   * Create and show a sub-sheet with AJAX-loaded content
   */
  function openAjaxSubSheet(title, url) {
    /* Backdrop */
    var $backdrop = $('.sub-sheet-backdrop');
    if (!$backdrop.length) {
      $backdrop = $('<div class="sub-sheet-backdrop"></div>').appendTo('body');
    }

    /* Panel with loading indicator */
    var $sheet = $(
      [
        '<div class="bottom-sub-sheet">',
        '<div class="sub-sheet-header" style="position:relative">',
        '<div class="drag-handle"></div>',
        '<button type="button" class="sub-sheet-back" aria-label="Back">&lsaquo;</button>',
        '<span class="sub-sheet-title"></span>',
        '</div>',
        '<div class="sub-sheet-body" style="padding:8px 0">',
        '<div style="text-align:center;padding:32px 16px;color:var(--bs-sheet-text-muted)">',
        '<i class="mdi mdi-loading mdi-spin" style="font-size:1.5rem"></i>',
        '</div>',
        '</div>',
        '</div>'
      ].join('')
    );

    $sheet.find('.sub-sheet-title').text(title || '');
    $sheet.appendTo('body');

    /* Push to stack and assign z-index */
    subSheetStack.push($sheet);
    var sheetZ = 1070 + subSheetStack.length * 2;
    $sheet.css('z-index', sheetZ);
    updateBackdropZ();

    /* Show */
    requestAnimationFrame(function () {
      $backdrop.addClass('show');
      $sheet.addClass('show');
      $('body').addClass('bottom-sheet-open');
    });

    /* Load content via AJAX (POST with prefer=dropdown) */
    $.ajax({
      url: url,
      type: 'POST',
      data: { prefer: 'dropdown' },
      dataType: 'json',
      success: function (data) {
        var $ul = $('<ul class="sub-sheet-list"></ul>');

        if (Array.isArray(data) && data.length) {
          data.forEach(function (item) {
            if (item.language && item.code) {
              var href = config.baseUrl + 'xhr/language/' + item.code;
              var $a = $(document.createElement('a')).attr('href', href).addClass('--xhr');
              $a.append('<i class="mdi mdi-translate" style="font-size:1.25rem;width:1.5rem;text-align:center;color:var(--bs-sheet-text-muted);flex-shrink:0"></i>');
              $a.append($('<span></span>').text(item.language));
              $ul.append($('<li></li>').append($a));
            } else if (item.user && item.text) {
              var nHref = item.url || '#';
              var $a = $(document.createElement('a')).attr('href', nHref).css('gap', '10px');
              if (item.avatar) {
                $('<img>')
                  .attr({ src: item.avatar, alt: item.user || '', loading: 'lazy', decoding: 'async' })
                  .css({ width: '36px', height: '36px', borderRadius: '50%', objectFit: 'cover', flexShrink: 0 })
                  .on('error', function () {
                    $(this).hide();
                  })
                  .appendTo($a);
              }
              var $spanCol = $('<span></span>').css({ display: 'flex', flexDirection: 'column', gap: '2px', lineHeight: '1.3' });
              $('<strong></strong>')
                .css('font-size', '0.9rem')
                .text(item.user || '')
                .appendTo($spanCol);
              $('<span></span>')
                .css({ fontSize: '0.82rem', color: 'var(--bs-sheet-text-muted)' })
                .text(item.text || '')
                .appendTo($spanCol);
              if (item.created_at || item.timestamp) {
                $('<span></span>')
                  .css({ fontSize: '0.75rem', color: 'var(--bs-sheet-accent)' })
                  .text(item.created_at || item.timestamp)
                  .appendTo($spanCol);
              }
              $spanCol.appendTo($a);
              $ul.append($('<li></li>').append($a));
            }
          });
        }

        if (!$ul.children().length) {
          $ul.append('<li style="text-align:center;padding:24px 16px;color:var(--bs-sheet-text-muted)"><i class="mdi mdi-emoticon-neutral-outline" style="font-size:1.5rem;display:block;margin-bottom:4px"></i> No items</li>');
        }

        $sheet.find('.sub-sheet-body').empty().append($ul);
      },
      error: function (xhr) {
        var html = xhr.responseText || '';
        try {
          var $content = $(html);
          var $ulList = $('<ul class="sub-sheet-list"></ul>');
          var found = false;

          var $items = $content.find('a, .nav-link, .dropdown-item');
          if (!$items.length) $items = $content.filter('a, .nav-link, .dropdown-item');

          if ($items.length) {
            found = true;
            $items.each(function () {
              var $el = $(this);
              var $icon = $el.find('i.mdi').first();
              var label = $el.clone().children('i').remove().end().text().trim();
              var elHref = $el.attr('href') || '#';

              var $a = $(document.createElement('a')).attr('href', elHref);
              if ($el.hasClass('no-ajax')) $a.addClass('no-ajax');
              if ($el.hasClass('--xhr')) $a.addClass('--xhr');

              if ($icon.length) {
                $a.append($icon.clone());
              }
              $a.append($('<span></span>').text(label));
              $ulList.append($('<li></li>').append($a));
            });
            $sheet.find('.sub-sheet-body').empty().append($ulList);
          }

          if (!found) {
            $sheet.find('.sub-sheet-body').empty().append($('<div></div>').css('padding', '12px 20px').html(html));
          }
        } catch (e) {
          $sheet
            .find('.sub-sheet-body')
            .empty()
            .append(
              $('<div></div>')
                .css({ textAlign: 'center', padding: '32px 16px', color: 'var(--bs-sheet-text-muted)' })
                .append('<i class="mdi mdi-alert-circle-outline" style="font-size:1.5rem"></i>')
                .append($('<p></p>').css('margin-top', '8px').text('Failed to load content'))
            );
        }
      }
    });

    /* Back button */
    $sheet.find('.sub-sheet-back').on('click', function () {
      closeTopSubSheet();
    });

    /* Backdrop tap closes all */
    $backdrop.off('click.subsheet').on('click.subsheet', function () {
      closeAllSubSheets();
    });
  }

  /**
   * Initialise: patch dropdown toggles inside offcanvas for mobile
   */
  function initMobileBottomSheet() {
    if (!isMobile()) return;

    var $offcanvas = $('#offcanvasNavbarDark');

    /* Find all dropdown-toggle links and mark them */
    $offcanvas.find('.nav-link.dropdown-toggle').each(function () {
      var $a = $(this);
      var $li = $a.closest('li');
      var $ul = $li.children('ul.dropdown-menu');

      /* Mark as submenu trigger — whether it has static children or will load via AJAX */
      $a.attr('data-bs-submenu', 'true');

      /* Store whether this is an AJAX dropdown (empty menu with a valid URL) */
      var hasStaticChildren = $ul.length && $ul.children('li').length > 0;
      var href = $a.attr('href') || '';
      var isAjax = !hasStaticChildren && href && href !== '#' && href !== 'javascript:void(0)';

      if (isAjax) {
        $a.attr('data-bs-submenu-ajax', href);
      }

      /* Prevent Bootstrap dropdown */
      $a.removeAttr('data-bs-toggle');
      $a.removeClass('dropdown-toggle');
    });

    /* Delegate click for submenu triggers */
    $offcanvas.off('click.bottomsheet').on('click.bottomsheet', '[data-bs-submenu]', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $a = $(this);
      var $li = $a.closest('li');
      var label = $a.find('span').first().text().trim() || $a.text().trim();

      /* Check if this is an AJAX sub-sheet */
      var ajaxUrl = $a.attr('data-bs-submenu-ajax');
      if (ajaxUrl) {
        openAjaxSubSheet(label, ajaxUrl);
        return;
      }

      /* Static children sub-sheet */
      var $ul = $li.children('ul.dropdown-menu');
      if (!$ul.length) return;

      var items = parseDropdownItems($ul);
      openSubSheet(label, items);
    });
  }

  /* Run on offcanvas show */
  var offcanvasEl = document.getElementById('offcanvasNavbarDark');
  if (offcanvasEl) {
    var mobileMenuToggle = document.querySelector('.mobile-menu-toggle[data-bs-target="#offcanvasNavbarDark"]');

    offcanvasEl.addEventListener('show.bs.offcanvas', function () {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.add('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
      }

      initMobileBottomSheet();
    });

    offcanvasEl.addEventListener('hide.bs.offcanvas', function () {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }
    });

    /* Clean up sub-sheets when offcanvas hides */
    offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }

      closeAllSubSheets();
    });
  }

  /* Also init on page load in case offcanvas is already visible */
  initMobileBottomSheet();

  /* Re-init on resize crossing the breakpoint */
  var wasDesktop = !isMobile();
  $(window).on('resize', function () {
    var nowDesktop = !isMobile();
    if (wasDesktop && !nowDesktop) {
      initMobileBottomSheet();
    }
    wasDesktop = nowDesktop;
  });
});
