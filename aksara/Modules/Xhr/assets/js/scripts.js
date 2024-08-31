$(document).ready(function() {
    /**
     * Trigger submit on enter, except holding the shift key
     */
    $('body').off('keypress.comment keydown.comment', 'textarea[name=comments]'),
    $('body').on('keypress.comment keydown.comment', 'textarea[name=comments]', function(e) {
        if(e.keyCode == 13 && ! e.shiftKey &&  ! $(this).closest('form').find('button[type=submit]').length) {
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
    $('body').on('click', '.--upvote', function(e)
    {
        e.preventDefault();
        
        xhr = $.ajax({
            url: $(this).data('href'),
            method: 'POST',
            context: this,
            beforeSend: function() {
                $(this).prop('disabled', true);
                $('[data-bs-toggle=tooltip]').tooltip('dispose')
            },
            complete: function() {
                $(this).prop('disabled', false)
            },
            statusCode: {
                403: function(response, status, error) {
                    if (config.action_sound) {
                        warningBuzzer.play()
                    }

                    if (typeof response.responseJSON !== 'undefined') {
                        response = response.responseJSON;
                        
                        throw_exception(response.status, response.message)
                    }
                }
            }
        })
        .done(function(response) {
            if (typeof response.element !== 'undefined' && response.content !== 'undefined') {
                $(response.element).html(response.content)
            }
        })
        .fail(function(response, status, error) {
            if (response.statusText == 'abort') {
                return;
            }
        })
    });
    
    /**
     * Fetch comment comments
     */
    $('body').off('click', '.--fetch-comments'),
    $('body').on('click', '.--fetch-comments', function(e) {
        e.preventDefault();

        let context = $(this);
        let is_reply = (typeof context.data('is-reply') !== 'undefined' ? context.data('is-reply') : '');
        let container = (is_reply ? context.closest('#comment-reply') : $('#comment-container'));

        xhr = $.ajax({
            url: context.data('href'),
            method: 'POST',
            data: {
                fetch: 'comments'
            },
            beforeSend: function() {
                $(`<div class="text-${ (is_reply ? 'start' : 'center') } spinner"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <span role="status">${ phrase('Loading...') }</span></span></div>`).appendTo(container)
            }
        })
        .done(function(response) {
            if (context.closest('.load-more-container').length) {
                context.closest('.load-more-container').remove()
            } else {
                context.remove()
            }

            container.find('.spinner').remove();

            if (response.total <= 0) return;

            response.comments.forEach(function(val, key) {
                $(`
                    <div class="comment-item">
                        <div class="row g-0 mb-2">
                            <div class="col-1 pt-1">
                                <a href="${ val.links.profile_url }" class="--xhr">
                                    <img src="${ val.user_photo }" class="img-fluid rounded-circle" />
                                </a>
                            </div>
                            <div class="col-11 ps-3">
                                <div class="position-relative">
                                    <div class="dropdown position-absolute end-0">
                                        <button class="btn btn-light btn-sm rounded-pill dropdown-toggle" type="button" id="dropdownMenuButton${ val.comment_id }" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-format-list-checks"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="dropdownMenuButton${ val.comment_id }">
                                            ` + (val.links.update_url ? `
                                                <li>
                                                    <a class="dropdown-item --modal" href="${ val.links.update_url }">
                                                        ${ phrase('Update') }
                                                    </a>
                                                </li>
                                            ` : `
                                                <li>
                                                    <a class="dropdown-item --modal" href="${ val.links.report_url }">
                                                        ${ phrase('Report') }
                                                    </a>
                                                </li>
                                            `) +

                                            (val.links.hide_url ? `
                                                <li>
                                                    <a class="dropdown-item --modal" href="${ val.links.hide_url }">
                                                        ${ phrase('Visibility') }
                                                    </a>
                                                </li>
                                            ` : ``) + `
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-light rounded-4 py-2 px-3 d-inline-block ${ (val.highlight ? 'border border-warning' : '') }">
                                    <a href="${ val.links.profile_url }" class="--xhr">
                                        <b id="comment-author-${ val.comment_id }">
                                            ${ val.first_name } ${ val.last_name }
                                        </b>
                                    </a>
                                    <br />
                                    <div id="comment-text-${ val.comment_id }">
                                        ` + (typeof val.mention !== 'undefined' ? `
                                            <div class="alert alert-warning callout p-2 mb-2">
                                                ${ phrase('Replying to') } <b> ${ val.mention.user } </b>
                                                <br />
                                                ${ val.mention.comment }
                                            </div>
                                        ` : ``) +

                                        (val.status > 0 ? `
                                            ${ val.comments }
                                            ` + (val.attachment.length ? `
                                                <div class="mt-3">
                                                    <a href="${ val.attachment.original }" target="_blank">
                                                        <img src="${ val.attachment.thumbnail }" class="img-fluid rounded-5" alt="..." />
                                                    </a>
                                                </div>
                                            ` : ``) + `
                                        ` : `
                                            <i class="text-muted">${ phrase('Comment is hidden') }</i>
                                        `) + `
                                    </div>
                                </div>
                                <div class="py-1 ps-3">
                                    <a href="javascript:void(0)" data-href="${ val.links.upvote_url }" class="text-sm --upvote">
                                        <b class="text-secondary" id="comment-upvote-${ val.comment_id }">
                                            ${ (val.upvotes > 0 ? val.upvotes : '') }
                                        </b>
                                        <b>
                                            ${ phrase('Upvote') }
                                        </b>
                                    </a>
                                    &middot;
                                    <a href="javascript:void(0)" data-href="${ val.links.reply_url }" class="text-sm --reply" data-profile-photo="${ val.user_photo }" data-mention="${ val.first_name } ${ val.last_name }">
                                        <b>
                                            ${ phrase('Reply') }
                                        </b>
                                    </a>
                                    &middot;
                                    <span class="text-muted text-sm">
                                        ${ val.timestamp }
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col-12 offset-0 col-sm-11 offset-sm-1 ps-0 ps-sm-3">
                                <div id="comment-reply">
                                    ` + (val.replies > 0 ? `
                                        <div class="load-more-container row g-0">
                                            <div class="col-11 offset-1 col-sm-12 offset-sm-0">
                                                <div class="ps-3 mb-4">
                                                    <a href="javascript:void(0)" data-href="${ val.links.replies_url }" data-is-reply="1" class="load-more --fetch-comments text-dark fw-bold">
                                                        <i class="mdi mdi-chevron-down"></i>
                                                        ${ val.replies } ${ (val.replies ? (val.replies > 1 ? phrase('Replies') : 'Reply') : '') }
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    ` : ``) + `
                                </div>
                            </div>
                        </div>
                    </div>
                `).appendTo(container)
            });

            if (response.total === response.limit) {
                $(`
                    <div class="load-more-container row g-0">
                        <div class="col-11 offset-1 ps-3">
                            <div class="ps-3">
                                <p class="text-${ (is_reply ? 'start' : 'center') }">
                                    <a href="javascript:void(0)" data-href="${ response.next_page }" data-is-reply="${ is_reply }" class="load-more --fetch-comments">
                                        <b>${ (is_reply ? phrase('Load more replies') : phrase('Load more comments')) }</b>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                `).appendTo(container)
            }
        })
        .fail(function(response, status, error) {
            if (response.statusText == 'abort') {
                return;
            }
            
            container.find('.spinner').remove();
        })
    });
    
    /**
     * Append reply form
     */
    $('body').off('click', '.--reply'),
    $('body').on('click', '.--reply', function(e) {
        e.preventDefault();

        xhr = $.ajax({
            url: $(this).data('href'),
            method: 'POST',
            context: this,
            data: {
                fetch: 'token'
            },
            beforeSend: function() {
                $(this).closest('#comment-container').find('form').remove();

                if (! $(this).closest('.comment-item').find('#comment-reply').find('.comment-item').length) {
                    $(this).closest('.comment-item').find('.--fetch-comments').trigger('click')
                }
            }
        })
        .done(function(response) {
            $(`
                <form action="${ $(this).data('href') }" method="POST" enctype="multipart/form-data" class="--validate-form">
                    <div class="row g-0">
                        <div class="col-11 offset-1 ps-3 text-sm">
                            ${ (typeof phrase.replying_to !== 'undefined' ? phrase.replying_to : 'Replying to') } <b> ${ $(this).attr('data-mention') }</b>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-1 pt-1">
                                <img src="${ $(this).attr('data-profile-photo') }" class="img-fluid rounded-circle" />
                            </div>
                            <div class="col-11 ps-3">
                                <div class="position-relative">
                                    <textarea name="comments" class="form-control" placeholder="${ (typeof phrase.type_a_reply !== 'undefined' ? phrase.type_a_reply : 'Type a reply') }" rows="1"></textarea>
                                    <div class="btn-group position-absolute bottom-0 end-0">
                                        <button type="button" class="btn btn-link" data-bs-toggle="tooltip" title="${ (typeof phrase.attach_photo !== 'undefined' ? phrase.attach_photo : 'Attach photo') }" onclick="jExec($(this).closest('form').find('.fileupload').removeClass('d-none').find('input[type=file]').trigger('click'))">
                                            <i class="mdi mdi-camera"></i>
                                        </button>
                                    </div>
                                </div>
                                <div data-provides="fileupload" class="fileupload fileupload-new d-none">
                                    <span class="btn btn-file" style="width:80px">
                                        <input type="file" name="attachment" accept=".jpg,.png,.gif" role="image-upload" id="attachment_input" />
                                        <div class="fileupload-new text-center">
                                            <img class="img-fluid upload_preview" src="${ config.base_url + 'uploads/placeholder_icon.png' }" alt="..." />
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.btn-file').find('input[type=file]').val(''), $(this).closest('.btn-file').find('img').attr('src', config.base_url + 'uploads/placeholder_icon.png'), $(this).closest('.fileupload').addClass('d-none'))">
                                            <i class="mdi mdi-window-close"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 align-items-center">
                        <div class="col-11 offset-1 ps-3">
                            <div role="validation-callback"></div>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="${ response.token }" /> 
                </form>
            `)
            .appendTo($(this).parents('.comment-item').find('#comment-reply').first());
            
            if($(this).closest('#comment-reply').length) {
                $(this).closest('#comment-reply').find('form').find('textarea').trigger('focus')
            } else {
                $(this).closest('.comment-item').find('#comment-reply').find('form').find('textarea').trigger('focus')
            }
            
            $('[data-bs-toggle=tooltip]').tooltip();

            $('textarea').each(function() {
                $(this).css({
                    height: (this.scrollHeight > $(this).actual('outerHeight') ? (this.scrollHeight + 2) : $(this).actual('outerHeight')),
                    overflowY: 'hidden'
                });
            })
            .on('input', function() {
                if (! $(this).hasClass('no-resize')) {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight + 2) + 'px';
                }
            });
        })
        .fail(function(response, status, error) {
            if (response.statusText == 'abort') {
                return;
            }
        })
    })
});
