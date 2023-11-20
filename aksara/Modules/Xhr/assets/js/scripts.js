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
     * Fetch comment replies
     */
    $('body').off('click', '.--fetch-replies'),
    $('body').on('click', '.--fetch-replies', function(e) {
        e.preventDefault();

        let context = $(this);
        let container = context.closest('#comment-reply');

        xhr = $.ajax({
            url: context.attr('href'),
            method: 'POST',
            data: {
                fetch: 'replies'
            }
        })
        .done(function(response) {
            container.html('');

            if (response.length > 0) {
                response.forEach(function(val, key) {
                    container.append(
                        '<div class="row g-0 mb-2">' +
                            '<div class="col-1 pt-1">' +
                                '<a href="' + config.base_url + 'user/' + val.username + '" class="--xhr">' +
                                    '<img src="' + val.photo + '" class="img-fluid rounded-circle" />' +
                                '</a>' +
                            '</div>' +
                            '<div class="col-11 ps-3">' +
                                '<div class="position-relative">' +
                                    '<div class="dropdown position-absolute end-0">' +
                                        '<button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' + key + '" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">' +
                                            '<i class="mdi mdi-format-list-checks"></i>' +
                                        '</button>' +
                                        '<ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="dropdownMenuButton' + key + '">' +
                                            (val.links.update_url ? 
                                            '<li>' +
                                                '<a class="dropdown-item --modal" href="' + val.links.update_url + '">' +
                                                    phrase('Update') +
                                                '</a>' +
                                            '</li>' : '') +
                                            (val.links.hide_url ? 
                                            '<li>' +
                                                '<a class="dropdown-item --modal" href="' + val.links.hide_url + '">' +
                                                    phrase('Visibility') +
                                                '</a>' +
                                            '</li>' : '') +
                                            (val.links.report_url ? 
                                            '<li>' +
                                                '<a class="dropdown-item --modal" href="' + val.links.report_url + '">' +
                                                    phrase('Report') +
                                                '</a>' +
                                            '</li>' : '') +
                                        '</ul>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="' + (val.highlight ? 'bg-warning' : 'bg-light') + ' rounded-4 py-2 px-3 d-inline-block">' +
                                    '<a href="' + config.base_url + 'user/' + val.username + '" class="--xhr">' +
                                        '<b id="comment-author-' + key + '">' +
                                            val.first_name + ' ' + val.last_name +
                                        '</b>' +
                                    '</a>' +
                                    '<br />' +
                                    '<div id="comment-text-' + key + '">' +
                                        (typeof val.mention !== 'undefined' ? 
                                            '<div class="alert alert-warning border-0 border-start border-3 p-2 mb-2">' +
                                                phrase('Replying to') + ' <b>' + val.mention.user + '</b>' +
                                                '<br />' +

                                                val.mention.comment +
                                            '</div>'
                                        : '') +

                                        (val.status ? 
                                            val.comments +

                                            ( val.attachment ? 
                                                '<div class="mt-2">' +
                                                    '<a href="' + val.attachment.original + '" target="_blank">' +
                                                        '<img src="' + val.attachment.thumbnail + '" class="img-fluid rounded" alt="..." />' +
                                                    '</a>' +
                                                '</div>'
                                            : '')
                                        : '<i class="text-muted">' + phrase('Comment is hidden') + '</i>') +
                                    '</div>' +
                                '</div>' +
                                '<div class="py-1 ps-3">' +
                                    '<a href="' + val.upvote_url + '" class="text-sm --modify">' +
                                        '<b class="text-secondary" id="comment-upvote-' + key + '">' +
                                            (val.upvotes > 0 ? val.upvotes : '') +
                                        '</b> ' +
                                        '<b>' +
                                            phrase('Upvote') +
                                        '</b>' +
                                    '</a>' +
                                    ' &middot; ' +
                                    '<a href="' + val.reply_url + '" class="text-sm --reply" data-profile-photo="' + val.user_photo + '" data-mention="' + val.first_name + ' ' + val.last_name + '">' +
                                        '<b>' +
                                            phrase('Reply') +
                                        '</b>' +
                                    '</a>' +
                                    ' &middot; ' +
                                    '<span class="text-muted text-sm">' +
                                        val.timestamp +
                                    '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    )
                })
            }
        })
    });
    
    /**
     * Append reply form
     */
    $('body').off('click', '.--reply'),
    $('body').on('click', '.--reply', function(e) {
        e.preventDefault();
        
        $(this).closest('#comment-container').find('form').remove();

        $(
            '<form action="' + $(this).attr('href') + '" method="POST" enctype="multipart/form-data" class="--validate-form">' +
                '<div class="row g-0">' +
                    '<div class="col-11 offset-1 ps-3 text-sm">' +
                        (typeof phrase.replying_to !== 'undefined' ? phrase.replying_to : 'Replying to') + ' <b>' + $(this).attr('data-mention') + '</b>' +
                    '</div>' +
                '</div>' +
                '<div class="form-group mb-3">' +
                    '<div class="row g-0 align-items-center">' +
                        '<div class="col-1 pt-1">' +
                            '<img src="' + $(this).attr('data-profile-photo') + '" class="img-fluid rounded-circle" />' +
                        '</div>' +
                        '<div class="col-11 ps-3">' +
                            '<div class="position-relative">' +
                                '<textarea name="comments" class="form-control" placeholder="' + (typeof phrase.type_a_reply !== 'undefined' ? phrase.type_a_reply : 'Type a reply') + '" rows="1"></textarea>' +
                                '<div class="btn-group position-absolute bottom-0 end-0">' +
                                    '<button type="button" class="btn btn-link" data-bs-toggle="tooltip" title="' + (typeof phrase.attach_photo !== 'undefined' ? phrase.attach_photo : 'Attach photo') + '" onclick="jExec($(this).closest(\'form\').find(\'.fileupload\').removeClass(\'d-none\').find(\'input[type=file]\').trigger(\'click\'))">' +
                                        '<i class="mdi mdi-camera"></i>' +
                                    '</button>' +
                                '</div>' +
                            '</div>' +
                            '<div data-provides="fileupload" class="fileupload fileupload-new d-none">' +
                                '<span class="btn btn-file" style="width:80px">' +
                                    '<input type="file" name="attachment" accept=".jpg,.png,.gif" role="image-upload" id="attachment_input" />' +
                                    '<div class="fileupload-new text-center">' +
                                        '<img class="img-fluid upload_preview" src="' + config.base_url + 'uploads/widget/icons/placeholder.png' + '" alt="..." />' +
                                    '</div>' +
                                    '<button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.btn-file\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.btn-file\').find(\'img\').attr(\'src\', config.base_url + \'uploads/widget/icons/placeholder.png\'), $(this).closest(\'.fileupload\').addClass(\'d-none\'))">' +
                                        '<i class="mdi mdi-window-close"></i>' +
                                    '</button>' +
                                '</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="row g-0 align-items-center">' +
                    '<div class="col-11 offset-1 ps-3">' +
                        '<div role="validation-callback"></div>' +
                    '</div>' +
                '</div>' +
            '</form>'
        )
        .appendTo(($(this).closest('#comment-reply').length ? $(this).closest('#comment-reply') : $(this).closest('.comment-item').find('#comment-reply')));
        
        if($(this).closest('#comment-reply').length) {
            $(this).closest('#comment-reply').find('form').find('textarea').trigger('focus')
        } else {
            $(this).closest('.comment-item').find('#comment-reply').find('form').find('textarea').trigger('focus')
        }
        
        $('[data-bs-toggle=tooltip]').tooltip()
    });
});
