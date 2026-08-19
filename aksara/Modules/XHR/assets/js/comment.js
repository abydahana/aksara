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

          const profileUrl = val.links && val.links.profile_url ? val.links.profile_url : '#';
          const fullName = `${val.first_name || ''} ${val.last_name || ''}`.trim();

          const $item = $('<div class="comment-item"></div>');
          const $dFlexMain = $('<div class="d-flex"></div>');

          const $avatarWrapper = $('<div class="flex-grow-0 pt-1"></div>');
          const $avatarLink = $('<a class="--xhr"></a>').attr('href', profileUrl);
          const $avatarImg = $('<img class="img-fluid rounded-circle" width="48" loading="lazy" decoding="async" />').attr('src', val.photo || '');
          $avatarWrapper.append($avatarLink.append($avatarImg));

          const $contentWrapper = $('<div class="flex-grow-1 ps-3"></div>');
          const $bubbleRow = $('<div class="d-flex align-items-center gap-1 comment-bubble"></div>');

          const $bubble = $('<div class="bg-body-tertiary rounded-4 py-2 px-3 d-inline-block"></div>');
          if (val.highlight) {
            $bubble.addClass('border border-warning');
          }

          const $header = $('<div class="comment-header"></div>');
          const $authorLink = $('<a class="text-body --xhr"></a>').attr('href', profileUrl);
          const $authorB = $('<b></b>')
            .attr('id', `comment-author-${val.comment_id || ''}`)
            .text(fullName);
          $authorLink.append($authorB);

          const $timeSpan = $('<span class="text-muted"></span>').text(val.created_at || '');
          $header.append($authorLink).append(' &middot; ').append($timeSpan);

          const $textDiv = $('<div></div>').attr('id', `comment-text-${val.comment_id || ''}`);

          if (val.mention && typeof val.mention === 'object') {
            const $mentionAlert = $('<div class="alert alert-warning callout p-2 mb-2"></div>');
            const $mentionUser = $('<b></b>').text(val.mention.user || '');
            $mentionAlert
              .append(phrase('Replying to') + ' ')
              .append($mentionUser)
              .append('<br />')
              .append(document.createTextNode(val.mention.comment || ''));
            $textDiv.append($mentionAlert);
          }

          if (val.status > 0) {
            const safeCommentsHtml = htmlspecialchars(val.comments || '').replace(/\r?\n/g, '<br />');
            const $commentsBody = $('<span></span>').html(safeCommentsHtml);
            $textDiv.append($commentsBody);

            if (val.attachment && typeof val.attachment === 'object' && Object.keys(val.attachment).length) {
              const $attachDiv = $('<div class="my-2"></div>');
              const $attachLink = $('<a class="d-block" target="_blank"></a>').attr('href', val.attachment.original || '#');
              const $attachImg = $('<img class="img-fluid rounded-4" style="max-width:320px" loading="lazy" decoding="async" />')
                .attr('src', val.attachment.thumbnail || '')
                .attr('alt', phrase('Attachment'));
              $attachDiv.append($attachLink.append($attachImg));
              $textDiv.append($attachDiv);
            }
          } else {
            $textDiv.append($('<i class="text-muted"></i>').text(phrase('Comment is hidden')));
          }

          $bubble.append($header).append($textDiv);
          $bubbleRow.append($bubble);

          const links = val.links || {};
          const $dropdown = $('<div class="dropdown comment-dropdown flex-shrink-0"></div>');
          const $dropdownBtn = $('<button class="btn btn-link btn-sm text-body-secondary p-0" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal fs-5"></i></button>').attr(
            'id',
            `dropdownMenuButton${val.comment_id || ''}`
          );
          const $dropdownMenu = $('<ul class="dropdown-menu dropdown-menu-end dropdown-menu-right"></ul>').attr('aria-labelledby', `dropdownMenuButton${val.comment_id || ''}`);

          if (links.update_url) {
            const $li = $('<li></li>');
            const $a = $('<a class="dropdown-item --modal"></a>').attr('href', links.update_url).text(phrase('Update'));
            $dropdownMenu.append($li.append($a));
          } else if (links.report_url) {
            const $li = $('<li></li>');
            const $a = $('<a class="dropdown-item --modal"></a>').attr('href', links.report_url).text(phrase('Report'));
            $dropdownMenu.append($li.append($a));
          }

          if (links.hide_url) {
            const $li = $('<li></li>');
            const $a = $('<a class="dropdown-item --modal"></a>').attr('href', links.hide_url).text(phrase('Visibility'));
            $dropdownMenu.append($li.append($a));
          }

          if ($dropdownMenu.children().length) {
            $dropdown.append($dropdownBtn).append($dropdownMenu);
            $bubbleRow.append($dropdown);
          }

          const $actionsDiv = $('<div class="py-1 ps-3"></div>');

          const $upvoteLink = $('<a class="small text-body --upvote"></a>')
            .attr('href', links.upvote_url || window.location.href)
            .attr('data-href', links.upvote_url || '');
          const $upvoteB = $('<b></b>');
          const $upvoteSpan = $('<span></span>')
            .attr('id', `comment-upvote-${val.comment_id || ''}`)
            .text(val.upvotes > 0 ? val.upvotes : '');
          $upvoteB.append($upvoteSpan).append(' ' + phrase('Upvote'));
          $upvoteLink.append($upvoteB);

          const $replyLink = $('<a class="small text-body --reply"></a>')
            .attr('href', links.reply_url || '#')
            .attr('data-profile-photo', val.user_photo || '')
            .attr('data-mention', fullName);
          const $replyB = $('<b></b>').text(phrase('Reply'));
          $replyLink.append($replyB);

          $actionsDiv.append($upvoteLink).append(' &middot; ').append($replyLink);

          $contentWrapper.append($bubbleRow).append($actionsDiv);
          $dFlexMain.append($avatarWrapper).append($contentWrapper);
          $item.append($dFlexMain);

          const $dFlexReply = $('<div class="d-flex"></div>');
          const $replySpacer = $('<div class="flex-grow-0 pt-1" style="width:48px"></div>');
          const $replyCol = $('<div class="flex-grow-1 ps-3" id="comment-reply"></div>');

          if (val.replies > 0) {
            const $loadMoreContainer = $('<div class="load-more-container row g-0"><div class="col-12"><div class="mb-3"></div></div></div>');
            const $repliesLink = $('<a class="load-more --fetch-comments text-body fw-bold" data-is-reply="1"><i class="mdi mdi-chevron-down"></i> </a>')
              .attr('href', links.replies_url || window.location.href)
              .attr('data-href', links.replies_url || '');
            const replyLabel = val.replies + ' ' + (val.replies > 1 ? phrase('Replies') : phrase('Reply'));
            $repliesLink.append(document.createTextNode(replyLabel));
            $loadMoreContainer.find('.mb-3').append($repliesLink);
            $replyCol.append($loadMoreContainer);
          }

          $dFlexReply.append($replySpacer).append($replyCol);
          $item.append($dFlexReply);

          $item.appendTo(container);
        });

        if (response.total === response.limit) {
          const $loadMore = $('<div class="load-more-container row g-0"><div class="col-12"><div class="mb-3"><p></p></div></div></div>');
          $loadMore.find('p').addClass(`text-${is_reply ? 'start' : 'center'}`);
          const $link = $('<a class="load-more --fetch-comments"><b></b></a>')
            .attr('href', response.next_page || window.location.href)
            .attr('data-href', response.next_page || '')
            .attr('data-is-reply', is_reply);
          $link.find('b').text(is_reply ? phrase('Load more replies') : phrase('Load more comments'));
          $loadMore.find('p').append($link);
          $loadMore.appendTo(container);
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

        const formAction = $(this).attr('href') || '';
        const profilePhoto = $(this).attr('data-profile-photo') || '';

        const $form = $('<form method="POST" enctype="multipart/form-data" class="--validate-form"></form>').attr('action', formAction);

        const $headerRow = $('<div class="row g-0"><div class="col-11 offset-1 ps-3 text-sm"></div></div>');
        $headerRow
          .find('.text-sm')
          .text(phrase('Replying to') + ' ')
          .append($('<b></b>').text($(this).attr('data-mention') || ''));

        const $formGroup = $(`
          <div class="form-group mb-3">
            <div class="d-flex align-items-center">
              <div class="flex-grow-0 pt-1">
                <img class="img-fluid rounded-circle" width="48" />
              </div>
              <div class="flex-grow-1 ps-3">
                <div class="position-relative">
                  <textarea name="comments" class="form-control" rows="1"></textarea>
                  <div class="btn-group position-absolute bottom-0 end-0">
                    <button type="button" class="btn btn-link" onclick="jExec($(this).closest(\'form\').find(\'.fileupload\').removeClass(\'d-none\').find(\'input[type=file]\').trigger(\'click\'))"><i class="mdi mdi-camera text-body"></i></button>
                  </div>
                </div>
                <div data-provides="fileupload" class="fileupload fileupload-new d-none">
                  <span class="btn btn-file" style="width:80px">
                    <input type="file" name="attachment" accept=".jpg,.png,.gif" data-role="image-upload" id="attachment_input" />
                    <div class="fileupload-new text-center"><img class="img-fluid upload_preview" /></div>
                    <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.btn-file\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.btn-file\').find(\'img\').attr(\'src\', config.baseUrl + \'uploads/placeholder_icon.png\'), $(this).closest(\'.fileupload\').addClass(\'d-none\'))"><i class="mdi mdi-window-close"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        `);

        $formGroup.find('img.rounded-circle').attr('src', profilePhoto);
        $formGroup.find('textarea').attr('placeholder', phrase('Type a reply'));
        $formGroup.find('button[type=button]').first().attr('data-bs-toggle', 'tooltip').attr('title', phrase('Attach photo'));
        $formGroup
          .find('img.upload_preview')
          .attr('src', config.baseUrl + 'uploads/placeholder_icon.png')
          .attr('alt', phrase('Preview'));

        const $valRow = $('<div class="d-flex align-items-center"><div class="flex-grow-0 pt-1" style="width:48px"></div><div class="flex-grow-1 ps-3"><div data-role="validation-callback"></div></div></div>');
        const $tokenInput = $('<input type="hidden" name="_token" />').attr('value', response.token || '');

        $form.append($headerRow).append($formGroup).append($valRow).append($tokenInput);
        $form.appendTo(targetContainer);

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
