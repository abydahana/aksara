if (typeof MAX_COMMENT_DEPTH === 'undefined') {
  var MAX_COMMENT_DEPTH = 3;
}

$(document).ready(function () {
  if (!$('#comment-dropdown-style').length) {
    $('head').append(`
      <style id="comment-dropdown-style">
        @media (min-width: 992px) {
          .comment-bubble .comment-dropdown {
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s ease-in-out;
          }

          .comment-bubble:hover .comment-dropdown,
          .comment-bubble:focus-within .comment-dropdown {
            opacity: 1;
            pointer-events: auto;
          }
        }
      </style>
    `);
  }

  /**
   * Trigger submit on enter, except holding the shift key
   */
  $('body').off('keypress.comment keydown.comment', 'textarea[name=comments]');
  $('body').on('keypress.comment keydown.comment', 'textarea[name=comments]', function (e) {
    if (e.keyCode == 13 && !e.shiftKey && !$(this).closest('form').find('button[type=submit]').length) {
      e.preventDefault();

      $(this).closest('form').trigger('submit');
      $(this).closest('form').trigger('reset');
      $(this).closest('form').find('.btn-danger').trigger('click');
      $(this).closest('form').find('.fileupload').addClass('d-none');
      $(this).blur();
      $(this).css('height', 'auto');
    }
  });

  /**
   * Simple request and modify
   */
  $('body').on('click', '.--upvote', function (e) {
    e.preventDefault();

    xhr = $.ajax({
      url: $(this).data('href'),
      method: 'POST',
      context: this,
      beforeSend: function () {
        $(this).prop('disabled', true);
        $('[data-bs-toggle=tooltip]').tooltip('dispose');
      },
      complete: function () {
        $(this).prop('disabled', false);
      },
      statusCode: {
        403: function (response) {
          if (config.actionSound) {
            warningBuzzer.play();
          }

          if (typeof response.responseJSON !== 'undefined') {
            response = response.responseJSON;

            throw_exception(response.status, response.message);
          }
        }
      }
    })
      .done(function (response) {
        if (typeof response !== 'undefined' && typeof response.element !== 'undefined' && typeof response.content !== 'undefined') {
          $(response.element).html(response.content);
        }
      })
      .fail(function (response) {
        if (response.statusText == 'abort') {
          return;
        }
      });
  });

  /**
   * Fetch comment comments
   */
  $('body').off('click', '.--fetch-comments');
  $('body').on('click', '.--fetch-comments', function (e) {
    e.preventDefault();

    const context = $(this);
    const is_reply = typeof context.data('is-reply') !== 'undefined' ? context.data('is-reply') : '';
    const container = is_reply ? context.closest('#comment-reply') : $('#comment-container');

    xhr = $.ajax({
      url: context.data('href'),
      method: 'POST',
      data: {
        fetch: 'comments'
      },
      beforeSend: function () {
        $(`<div class="text-${is_reply ? 'start' : 'center'} spinner"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <span role="status">${phrase('Loading...')}</span></div>`).appendTo(container);
      }
    })
      .done(function (response) {
        if (context.closest('.load-more-container').length) {
          context.closest('.load-more-container').remove();
        } else {
          context.remove();
        }

        container.find('.spinner').remove();

        if (!response || !Array.isArray(response.comments) || !response.comments.length) return;

        response.comments.forEach(function (val) {
          if (!val) return;

          const mentionHtml =
            typeof val.mention !== 'undefined' && val.mention
              ? `<div class="alert alert-warning callout p-2 mb-2">
                ${phrase('Replying to')} <b>${htmlspecialchars(val.mention.user || '')}</b>
                <br />
                ${htmlspecialchars(val.mention.comment || '')}
              </div>`
              : '';

          const attachmentHtml =
            val.attachment && typeof val.attachment === 'object' && Object.keys(val.attachment).length
              ? `<div class="my-2">
                <a href="${htmlspecialchars(val.attachment.original || '#')}" class="d-block" target="_blank">
                  <img src="${htmlspecialchars(val.attachment.thumbnail || '')}" class="img-fluid rounded-4" style="max-width:320px" alt="${phrase('Attachment')}" loading="lazy" decoding="async" />
                </a>
              </div>`
              : '';

          const commentContentHtml = val.status > 0 ? `${val.comments || ''}${attachmentHtml}` : `<i class="text-muted">${phrase('Comment is hidden')}</i>`;

          const links = val.links || {};

          const menuItemsHtml = links.update_url
            ? `<li>
                <a class="dropdown-item --modal" href="${htmlspecialchars(links.update_url)}">
                  ${phrase('Update')}
                </a>
              </li>`
            : links.report_url
              ? `<li>
                <a class="dropdown-item --modal" href="${htmlspecialchars(links.report_url)}">
                  ${phrase('Report')}
                </a>
              </li>`
              : '';

          const hideItemHtml = links.hide_url
            ? `<li>
                <a class="dropdown-item --modal" href="${htmlspecialchars(links.hide_url)}">
                  ${phrase('Visibility')}
                </a>
              </li>`
            : '';

          const repliesHtml =
            val.replies > 0
              ? `<div class="load-more-container row g-0">
                <div class="col-12">
                  <div class="mb-3">
                    <a href="${htmlspecialchars(links.replies_url || window.location.href)}" data-href="${htmlspecialchars(links.replies_url || '')}" data-is-reply="1" class="load-more --fetch-comments text-body fw-bold">
                      <i class="mdi mdi-chevron-down"></i>
                      ${val.replies} ${val.replies > 1 ? phrase('Replies') : phrase('Reply')}
                    </a>
                  </div>
                </div>
              </div>`
              : '';

          const fullName = `${val.first_name || ''} ${val.last_name || ''}`.trim();
          const escapedFullName = htmlspecialchars(fullName);
          const escapedCommentId = htmlspecialchars(val.comment_id || '');

          const itemHtml = `
            <div class="comment-item">
              <div class="d-flex">
                <div class="flex-grow-0 pt-1">
                  <a href="${htmlspecialchars(links.profile_url || '#')}" class="--xhr">
                    <img src="${htmlspecialchars(val.photo || '')}" class="img-fluid rounded-circle" width="48" loading="lazy" decoding="async" />
                  </a>
                </div>
                <div class="flex-grow-1 ps-3">
                  <div class="d-flex align-items-center gap-1 comment-bubble">
                    <div class="bg-body-tertiary rounded-4 py-2 px-3 d-inline-block ${val.highlight ? 'border border-warning' : ''}">
                      <div class="comment-header">
                        <a href="${htmlspecialchars(links.profile_url || '#')}" class="text-body --xhr">
                          <b id="comment-author-${escapedCommentId}">
                            ${escapedFullName}
                          </b>
                        </a>
                        &middot;
                        <span class="text-muted">
                          ${htmlspecialchars(val.created_at || '')}
                        </span>
                      </div>
                      <div id="comment-text-${escapedCommentId}">
                        ${mentionHtml}
                        ${commentContentHtml}
                      </div>
                    </div>
                    <div class="dropdown comment-dropdown flex-shrink-0">
                      <button class="btn btn-link btn-sm text-body-secondary p-0" type="button" id="dropdownMenuButton${escapedCommentId}" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-horizontal fs-5"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="dropdownMenuButton${escapedCommentId}">
                        ${menuItemsHtml}
                        ${hideItemHtml}
                      </ul>
                    </div>
                  </div>
                  <div class="py-1 ps-3">
                    <a href="${htmlspecialchars(links.upvote_url || window.location.href)}" data-href="${htmlspecialchars(links.upvote_url || '')}" class="small text-body --upvote">
                      <b><span id="comment-upvote-${escapedCommentId}">${val.upvotes > 0 ? val.upvotes : ''}</span> ${phrase('Upvote')}</b>
                    </a>
                    &middot;
                    <a href="${htmlspecialchars(links.reply_url || '#')}" class="small text-body --reply" data-profile-photo="${htmlspecialchars(val.user_photo || '')}" data-mention="${escapedFullName}">
                      <b>${phrase('Reply')}</b>
                    </a>
                  </div>
                </div>
              </div>
              <div class="d-flex">
                <div class="flex-grow-0 pt-1">
                  <span class="d-block" style="width:48px">&nbsp;</span>
                </div>
                <div class="flex-grow-1 ps-3" id="comment-reply">
                  ${repliesHtml}
                </div>
              </div>
            </div>
          `;

          $(itemHtml).appendTo(container);
        });

        if (response.total === response.limit) {
          const loadMoreHtml = `
            <div class="load-more-container row g-0">
              <div class="col-12">
                <div class="mb-3">
                  <p class="text-${is_reply ? 'start' : 'center'}">
                    <a href="${htmlspecialchars(response.next_page || window.location.href)}" data-href="${htmlspecialchars(response.next_page || '')}" data-is-reply="${is_reply}" class="load-more --fetch-comments">
                      <b>${is_reply ? phrase('Load more replies') : phrase('Load more comments')}</b>
                    </a>
                  </p>
                </div>
              </div>
            </div>
          `;
          $(loadMoreHtml).appendTo(container);
        }
      })
      .fail(function (response) {
        if (response.statusText == 'abort') {
          return;
        }

        container.find('.spinner').remove();
      });
  });

  /**
   * Append reply form
   */
  $('body').off('click', '.--reply');
  $('body').on('click', '.--reply', function (e) {
    e.preventDefault();

    xhr = $.ajax({
      url: $(this).attr('href'),
      method: 'POST',
      context: this,
      data: {
        fetch: 'token'
      },
      beforeSend: function () {
        $(this).closest('#comment-container').find('form').remove();

        if (!$(this).closest('.comment-item').find('#comment-reply').find('.comment-item').length) {
          $(this).closest('.comment-item').find('.--fetch-comments').trigger('click');
        }
      }
    })
      .done(function (response) {
        const parents = $(this).parents('.comment-item');
        const currentDepth = parents.length;
        let targetContainer;

        if (currentDepth < MAX_COMMENT_DEPTH) {
          targetContainer = $(this).closest('.comment-item').find('#comment-reply').first();
        } else {
          const maxIndex = currentDepth - (MAX_COMMENT_DEPTH - 1);
          targetContainer = parents
            .eq(maxIndex > 0 ? maxIndex : 0)
            .find('#comment-reply')
            .first();
        }

        const replyFormHtml = `
          <form action="${htmlspecialchars($(this).attr('href'))}" method="POST" enctype="multipart/form-data" class="--validate-form">
            <div class="row g-0">
              <div class="col-11 offset-1 ps-3 text-sm">
                ${phrase('Replying to')} <b>${htmlspecialchars($(this).attr('data-mention'))}</b>
              </div>
            </div>
            <div class="form-group mb-3">
              <div class="row g-0 align-items-center">
                <div class="col-1 pt-1">
                  <img src="${htmlspecialchars($(this).attr('data-profile-photo'))}" class="img-fluid rounded-circle" />
                </div>
                <div class="col-11 ps-3">
                  <div class="position-relative">
                    <textarea name="comments" class="form-control" placeholder="${phrase('Type a reply')}" rows="1"></textarea>
                    <div class="btn-group position-absolute bottom-0 end-0">
                      <button type="button" class="btn btn-link" data-bs-toggle="tooltip" title="${phrase('Attach photo')}" onclick="jExec($(this).closest('form').find('.fileupload').removeClass('d-none').find('input[type=file]').trigger('click'))">
                        <i class="mdi mdi-camera text-body"></i>
                      </button>
                    </div>
                  </div>
                  <div data-provides="fileupload" class="fileupload fileupload-new d-none">
                    <span class="btn btn-file" style="width:80px">
                      <input type="file" name="attachment" accept=".jpg,.png,.gif" data-role="image-upload" id="attachment_input" />
                      <div class="fileupload-new text-center">
                        <img class="img-fluid upload_preview" src="${config.baseUrl + 'uploads/placeholder_icon.png'}" alt="${phrase('Preview')}" />
                      </div>
                      <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.btn-file').find('input[type=file]').val(''), $(this).closest('.btn-file').find('img').attr('src', config.baseUrl + 'uploads/placeholder_icon.png'), $(this).closest('.fileupload').addClass('d-none'))">
                        <i class="mdi mdi-window-close"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row g-0 align-items-center">
              <div class="col-11 offset-1 ps-3">
                <div data-role="validation-callback"></div>
              </div>
            </div>
            <input type="hidden" name="_token" value="${response.token}" />
          </form>
        `;

        $(replyFormHtml).appendTo(targetContainer);

        targetContainer.find('form').find('textarea').trigger('focus');

        $('[data-bs-toggle=tooltip]').tooltip();

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
      })
      .fail(function (response) {
        if (response.statusText == 'abort') {
          return;
        }
      });
  });
});
