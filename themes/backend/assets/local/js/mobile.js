/**
 * Aksara Mobile Bottom Sheet & Stacked Sub-Sheet Handler
 * Handles touch drag-to-close, unlimited stacked sub-sheets, full-height rounded removal, and AJAX sub-menus.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 */
(function () {
  'use strict';

  function initBottomSheetDrag(sheetEl) {
    if (!sheetEl || sheetEl.dataset.dragToCloseReady === '1') return;

    const header = sheetEl.querySelector('.offcanvas-header, .sub-sheet-header');
    if (!header) return;

    let startY = 0;
    let startTime = 0;
    let isDragging = false;

    function resetDrag() {
      isDragging = false;
      sheetEl.classList.remove('is-dragging');
      sheetEl.style.removeProperty('--sheet-drag-y');
    }

    function closeSheet() {
      sheetEl.style.setProperty('--sheet-drag-y', '100vh');
      setTimeout(() => {
        if (sheetEl.classList.contains('bottom-sub-sheet')) {
          if (typeof window.closeTopSubSheet === 'function') {
            window.closeTopSubSheet();
          } else {
            sheetEl.remove();
          }
        } else {
          const inst = typeof bootstrap !== 'undefined' && bootstrap.Offcanvas ? bootstrap.Offcanvas.getInstance(sheetEl) : null;
          if (inst) inst.hide();
        }
      }, 300);
    }

    header.addEventListener(
      'touchstart',
      (e) => {
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
      (e) => {
        if (!isDragging || !e.touches || !e.touches.length) return;
        let dy = e.touches[0].clientY - startY;
        if (dy < 0) dy = 0;
        sheetEl.style.setProperty('--sheet-drag-y', dy + 'px');
      },
      { passive: true }
    );

    function onEnd() {
      if (!isDragging) return;

      const dy = parseFloat(sheetEl.style.getPropertyValue('--sheet-drag-y')) || 0;
      const dt = Math.max(1, Date.now() - startTime);
      const velocity = dy / dt;

      isDragging = false;
      sheetEl.classList.remove('is-dragging');

      if (velocity > 0.4 || dy > sheetEl.offsetHeight * 0.35) {
        closeSheet();
      } else {
        sheetEl.style.setProperty('--sheet-drag-y', '0px');
        setTimeout(() => {
          sheetEl.style.removeProperty('--sheet-drag-y');
        }, 300);
      }
    }

    header.addEventListener('touchend', onEnd);
    header.addEventListener('touchcancel', onEnd);
    if (sheetEl.classList.contains('offcanvas')) {
      sheetEl.addEventListener('hidden.bs.offcanvas', resetDrag);
    }
    sheetEl.dataset.dragToCloseReady = '1';
  }

  function initAllBottomSheetDrag(root) {
    const scope = root || document;
    if (scope.matches && (scope.matches('.offcanvas.offcanvas-bottom') || scope.matches('.bottom-sub-sheet'))) {
      initBottomSheetDrag(scope);
    }
    scope.querySelectorAll('.offcanvas.offcanvas-bottom, .bottom-sub-sheet').forEach(initBottomSheetDrag);
  }

  window.initBottomSheetDrag = initBottomSheetDrag;
  window.initAllBottomSheetDrag = initAllBottomSheetDrag;

  document.addEventListener('DOMContentLoaded', () => {
    initAllBottomSheetDrag(document);
  });

  document.addEventListener('show.bs.offcanvas', (e) => {
    initBottomSheetDrag(e.target);
  });
})();

$(document).ready(() => {
  const isMobile = () => window.innerWidth < 992;
  let subSheetStack = [];

  function checkSheetFullHeight(sheetEl) {
    if (!sheetEl) return;
    const vh = window.innerHeight || document.documentElement.clientHeight;
    if (sheetEl.offsetHeight >= vh - 6) {
      sheetEl.classList.add('is-full-height');
    } else {
      sheetEl.classList.remove('is-full-height');
    }
  }

  function updateBackdropZ() {
    if (subSheetStack.length) {
      const topSheetZ = parseInt(subSheetStack[subSheetStack.length - 1].css('z-index'), 10) || 1058;
      $('.sub-sheet-backdrop').css('z-index', topSheetZ - 1);
    } else {
      $('.sub-sheet-backdrop').css('z-index', 1055);
    }
  }

  function closeTopSubSheet() {
    if (!subSheetStack.length) return;
    const $sheet = subSheetStack.pop();
    $sheet.removeClass('show');
    setTimeout(() => {
      $sheet.remove();
    }, 380);

    if (subSheetStack.length === 0) {
      $('.sub-sheet-backdrop').removeClass('show');
      $('body').removeClass('bottom-sheet-open');
    } else {
      updateBackdropZ();
    }
  }

  window.closeTopSubSheet = closeTopSubSheet;

  function closeAllSubSheets() {
    subSheetStack.forEach(($s) => {
      $s.removeClass('show');
      setTimeout(() => {
        $s.remove();
      }, 380);
    });
    subSheetStack = [];
    $('.sub-sheet-backdrop').removeClass('show');
    $('body').removeClass('bottom-sheet-open');
  }

  function createSubSheetSkeleton(title) {
    let $backdrop = $('.sub-sheet-backdrop');
    if (!$backdrop.length) {
      $backdrop = $('<div class="sub-sheet-backdrop"></div>').appendTo('body');
    }

    const $sheet = $(
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
    $sheet.appendTo('body');

    subSheetStack.push($sheet);
    const sheetZ = 1070 + subSheetStack.length * 2;
    $sheet.css('z-index', sheetZ);
    updateBackdropZ();

    requestAnimationFrame(() => {
      $backdrop.addClass('show');
      $sheet.addClass('show');
      $('body').addClass('bottom-sheet-open');
      if (typeof window.initBottomSheetDrag === 'function') {
        window.initBottomSheetDrag($sheet[0]);
      }
      checkSheetFullHeight($sheet[0]);
    });

    $sheet.find('.sub-sheet-back').on('click', () => {
      closeTopSubSheet();
    });

    $backdrop.off('click.subsheet').on('click.subsheet', () => {
      closeTopSubSheet();
    });

    return $sheet;
  }

  function parseDropdownItems($ul) {
    const items = [];
    $ul.children('li').each(function () {
      const $li = $(this);
      const $a = $li.children('a').first();
      const $hr = $li.children('hr');

      if ($hr.length) {
        items.push({ type: 'divider' });
        return;
      }

      if (!$a.length) return;

      let icon = '';
      const $icon = $a.children('i.mdi');
      if ($icon.length) {
        icon = $icon[0].outerHTML;
      }

      let label = '';
      let $span = $a.children('span.hide-on-collapse');
      if ($span.length) {
        label = $span.text().trim();
      } else {
        $span = $a.children('span');
        if ($span.length) {
          label = $span.text().trim();
        } else {
          label = $a.clone().children('i').remove().end().text().trim();
        }
      }

      const href = $a.attr('href') || '#';
      const $childUl = $li.children('ul.dropdown-menu, ul.collapse, ul');
      const hasChildren = $childUl.length > 0;
      const extraClass = $a.attr('class') || '';
      const isNoAjax = extraClass.indexOf('no-ajax') !== -1;
      const isXhr = extraClass.indexOf('--xhr') !== -1;
      const isDanger = extraClass.indexOf('text-danger') !== -1;

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

  function openSubSheet(title, items) {
    const $sheet = createSubSheetSkeleton(title);
    const $ul = $('<ul class="sub-sheet-list"></ul>');

    items.forEach((item) => {
      if (item.type === 'divider') {
        $ul.append('<li><hr style="border-color:var(--bs-sheet-divider);margin:4px 16px"></li>');
        return;
      }

      const $a = $(document.createElement('a')).attr('href', item.href || '#');
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

    $sheet.find('.sub-sheet-body').append($ul);
    $sheet.data('items', items);

    $sheet.find('[data-bs-submenu]').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const idx = $(this).closest('li').index();
      let count = 0;
      $sheet.find('.sub-sheet-list > li').each(function (i) {
        if (i === idx) return false;
        if (!$(this).find('hr').length) count++;
      });

      let itemIndex = 0;
      let nonDividerCount = 0;
      for (let i = 0; i < items.length; i++) {
        if (items[i].type === 'divider') continue;
        if (nonDividerCount === count) {
          itemIndex = i;
          break;
        }
        nonDividerCount++;
      }

      const item = items[itemIndex];
      if (item && item.hasChildren && item.$childUl) {
        const childItems = parseDropdownItems(item.$childUl);
        openSubSheet(item.label, childItems);
      }
    });
  }

  function openAjaxSubSheet(title, url) {
    const $sheet = createSubSheetSkeleton(title);
    const $body = $sheet.find('.sub-sheet-body');
    $body.css('padding', '8px 0').html('<div style="text-align:center;padding:32px 16px;color:var(--bs-sheet-text-muted)"><i class="mdi mdi-loading mdi-spin" style="font-size:1.5rem"></i></div>');

    $.ajax({
      url: url,
      type: 'POST',
      data: { prefer: 'dropdown' },
      dataType: 'json',
      success: (data) => {
        const $ul = $('<ul class="sub-sheet-list"></ul>');
        let itemsArr = [];

        if (Array.isArray(data)) {
          itemsArr = data;
        } else if (data && typeof data === 'object') {
          if (Array.isArray(data.data)) {
            itemsArr = data.data;
          } else {
            Object.keys(data).forEach((key) => {
              const val = data[key];
              if (typeof val === 'string') {
                itemsArr.push({ language: val, code: key });
              } else if (val && typeof val === 'object') {
                if (!val.code) val.code = key;
                itemsArr.push(val);
              }
            });
          }
        }

        if (itemsArr.length) {
          itemsArr.forEach((item) => {
            const langName = item.language || item.label || item.name || item.title;
            const langCode = item.code || item.id || item.key;
            if (langName && langCode) {
              const href = (typeof config !== 'undefined' && config.baseUrl ? config.baseUrl : '/') + 'xhr/language/' + langCode;
              const $a = $(document.createElement('a')).attr('href', href).addClass('--xhr');
              $a.append('<i class="mdi mdi-translate" style="font-size:1.25rem;width:1.5rem;text-align:center;color:var(--bs-sheet-text-muted);flex-shrink:0"></i>');
              $a.append($('<span></span>').text(langName));
              $ul.append($('<li></li>').append($a));
            } else if (item.user && item.text) {
              const nHref = item.url || '#';
              const $a = $(document.createElement('a')).attr('href', nHref).css('gap', '10px');
              if (item.avatar) {
                $('<img>')
                  .attr({ src: item.avatar, alt: item.user || '', loading: 'lazy', decoding: 'async' })
                  .css({ width: '36px', height: '36px', borderRadius: '50%', objectFit: 'cover', flexShrink: 0 })
                  .on('error', function () {
                    $(this).hide();
                  })
                  .appendTo($a);
              }
              const $spanCol = $('<span></span>').css({ display: 'flex', flexDirection: 'column', gap: '2px', lineHeight: '1.3' });
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
            } else if (item.label || item.title || item.text) {
              const elHref = item.url || item.href || '#';
              const $a = $(document.createElement('a')).attr('href', elHref);
              if (item.icon) {
                $a.append('<i class="' + item.icon + '" style="font-size:1.25rem;width:1.5rem;text-align:center;color:var(--bs-sheet-text-muted);flex-shrink:0"></i>');
              }
              $a.append($('<span></span>').text(item.label || item.title || item.text));
              $ul.append($('<li></li>').append($a));
            }
          });
        }

        if (!$ul.children().length) {
          $ul.append('<li style="text-align:center;padding:24px 16px;color:var(--bs-sheet-text-muted)"><i class="mdi mdi-emoticon-neutral-outline" style="font-size:1.5rem;display:block;margin-bottom:4px"></i> No items</li>');
        }

        $body.empty().append($ul);
      },
      error: (xhr) => {
        const html = xhr.responseText || '';
        try {
          const $content = $(html);
          const $ulList = $('<ul class="sub-sheet-list"></ul>');
          let found = false;

          let $items = $content.find('a, .nav-link, .dropdown-item');
          if (!$items.length) $items = $content.filter('a, .nav-link, .dropdown-item');

          if ($items.length) {
            found = true;
            $items.each(function () {
              const $el = $(this);
              const $icon = $el.find('i.mdi').first();
              const label = $el.clone().children('i').remove().end().text().trim();
              const elHref = $el.attr('href') || '#';

              const $a = $(document.createElement('a')).attr('href', elHref);
              if ($el.hasClass('no-ajax')) $a.addClass('no-ajax');
              if ($el.hasClass('--xhr')) $a.addClass('--xhr');

              if ($icon.length) {
                $a.append($icon.clone());
              }
              $a.append($('<span></span>').text(label));
              $ulList.append($('<li></li>').append($a));
            });
            $body.empty().append($ulList);
          }

          if (!found) {
            $body.empty().append($('<div></div>').css('padding', '12px 20px').html(html));
          }
        } catch (e) {
          $body
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
  }

  function initMobileBottomSheet() {
    if (!isMobile()) return;

    const $offcanvas = $('#offcanvasBottomSheet');

    $offcanvas.find('.nav-link.dropdown-toggle').each(function () {
      const $a = $(this);
      const $li = $a.closest('li');
      const $ul = $li.children('ul.dropdown-menu, ul.collapse, ul');
      $a.attr('data-bs-submenu', 'true');

      const hasStaticChildren = $ul.length && $ul.children('li').length > 0;
      const href = $a.attr('href') || '';
      const isAjax = !hasStaticChildren && href && href !== '#' && href !== 'javascript:void(0)';

      if (isAjax) {
        $a.attr('data-bs-submenu-ajax', href);
      }

      $a.removeAttr('data-bs-toggle');
      $a.removeClass('dropdown-toggle');
    });

    $offcanvas.off('click.bottomsheet').on('click.bottomsheet', '[data-bs-submenu]', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const $a = $(this);
      const $li = $a.closest('li');
      const label = $a.find('span').first().text().trim() || $a.text().trim();

      const ajaxUrl = $a.attr('data-bs-submenu-ajax');
      if (ajaxUrl) {
        openAjaxSubSheet(label, ajaxUrl);
        return;
      }

      const $ul = $li.children('ul.dropdown-menu, ul.collapse, ul');
      if (!$ul.length) return;

      const items = parseDropdownItems($ul);
      openSubSheet(label, items);
      if (subSheetStack.length) {
        checkSheetFullHeight(subSheetStack[subSheetStack.length - 1][0]);
      }
    });
  }

  const offcanvasEl = document.getElementById('offcanvasBottomSheet');
  if (offcanvasEl) {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle[data-bs-target="#offcanvasBottomSheet"]');

    offcanvasEl.addEventListener('show.bs.offcanvas', () => {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.add('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
      }
      initMobileBottomSheet();
      setTimeout(() => {
        checkSheetFullHeight(offcanvasEl);
      }, 50);
    });

    offcanvasEl.addEventListener('hide.bs.offcanvas', () => {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }
    });

    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
      if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }
      closeAllSubSheets();
    });
  }

  initMobileBottomSheet();

  let wasDesktop = !isMobile();
  $(window).on('resize.sheetheight', () => {
    const nowDesktop = !isMobile();
    if (wasDesktop && !nowDesktop) {
      initMobileBottomSheet();
    }
    wasDesktop = nowDesktop;

    if (offcanvasEl && offcanvasEl.classList.contains('show')) {
      checkSheetFullHeight(offcanvasEl);
    }
    subSheetStack.forEach(($s) => {
      if ($s.length) checkSheetFullHeight($s[0]);
    });
  });
});
