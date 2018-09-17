function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}

if (getURLParameter('updateFileData') !== null) {
    showUpdateFileData(getURLParameter('updateFileData'));
}

function showUpdateFileData(name) {
    $('#file_' + name).css('display', 'none');
    $('#update-file-data_' + name).css('display', 'block');
}

function showOrHideTableFileInfo(name) {
    var obj = $('#table-file-info_' + name);
    if (obj.css('display') == 'none') {
        obj.css('display', 'block');
    } else {
        obj.css('display', 'none');
    }
}

if (document.getElementById('description')) {
    $("#char-count").text($("#description").val().length);
    $(document).on('input', $("#description"), function () {
        $("#char-count").text($("#description").val().length);
    });
}

function logOut(e) {
    e.preventDefault();
    $('#form-logout').submit();
}

function increaseNumOfDownloads(fileName) {
    $('#downloads-count-' + fileName).text(+$('#downloads-count-' + fileName).text() + 1);
}

function replyComment(fileName, commentId, commentUserName) {
    $('#comment-reply-' + commentId).prop('checked', true);
    $('#comment-text-' + fileName).select();
    $('html, body').animate({
        scrollTop: $('#comment-text-' + fileName).offset().top - ($(window).height() / 2)
    }, 300);
    $('#reply-user-name-' + fileName).css('display', 'table-cell').text(commentUserName.length !== 0 ? commentUserName : "Аноним");
}

function submitComment(e, fileName) {
    e.preventDefault();

    $('#comment-error-' + fileName).css('display', 'none');
    $('#comment-submit-' + fileName).attr("disabled", "disabled");

    var formData = new FormData($('#form-comments-' + fileName)[0]);

    $.ajax({
        url: '/comment/' + fileName,
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            data = JSON.parse(data);
            if (data.status === "success") {
                showComment(data.comment, fileName);
                $('#comment-text-' + fileName).val("");
                $("[name=reply-comment-id]").prop('checked', false);
                $('#reply-user-name-' + fileName).css('display', 'none');
                $('#comments-count-' + fileName).text(+$('#comments-count-' + fileName).text() + 1);
            } else {
                $('#comment-error-' + fileName).css('display', 'block').text(data.errors[0]);
            }
            $('#comment-submit-' + fileName).removeAttr("disabled");
        },
        error: function () {
            alert("Произошла ошибка. Обновите страницу и попробуйте ещё раз");
            $('#comment-submit-' + fileName).removeAttr("disabled");
        }
    })
}

function getAllComments(e, fileName) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('csrf_name', $('.csrfName').val());
    formData.append('csrf_value', $('.csrfValue').val());

    $.ajax({
        url: '/getAllComments/' + fileName,
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            data = JSON.parse(data);
            if (data.status === "success") {
                $('#comments-' + fileName).empty();
                for (i = 0; i < data.comments.length; i++) {
                    showComment(data.comments[i], fileName);
                }
            } else {
                getAllCommentsError(fileName);
            }
        },
        error: function () {
            getAllCommentsError(fileName);
        }
    })
}

function getAllCommentsError(fileName) {
    $('#show-all-comments-' + fileName).css('background-color', '#ffaeae').text('Произошла ошибка. Обновите страницу и попробуйте ещё раз');
}

function showComment(comment, fileName) {
    var newComment = $('#comment-template').clone(),
        newCommentUserAvatar = newComment.find('#comment-template__user-avatar'),
        newCommentUserName = newComment.find('#comment-template__user-name'),
        newCommentDepth = newComment.find('#comment-template__depth'),
        newCommentText = newComment.find('#comment-template__text'),
        newCommentDate = newComment.find('#comment-template__date'),
        newCommentReplyBtn = newComment.find('#comment-template__reply-btn'),
        newCommentReplyInput = newComment.find('#comment-template__reply-input'),
        defaultUserAvatar = '/image/avatar_default.jpg',
        defaultUserName = 'Аноним';
    newComment.attr('id', 'comment-' + comment.id);
    newComment.attr('data-parent-id', comment.parentId);
    newComment.removeClass('comment-template');
    comment.userAvatar !== null ? newCommentUserAvatar.attr('src', comment.userAvatar) : newCommentUserAvatar.attr('src', defaultUserAvatar);
    comment.userName !== null ? newCommentUserName.text(comment.userName) : newCommentUserName.text(defaultUserName);
    newCommentDepth.attr('class', 'comment__depth-' + comment.depth);
    newCommentText.text(comment.text);
    newCommentDate.text(comment.date);
    // https://stackoverflow.com/a/1207393 add events to dynamically created items
    newComment.on('click', newCommentReplyBtn, function () {
        replyComment(fileName, comment.id, comment.userName);
    });
    newCommentReplyInput.attr('id', 'comment-reply-' + comment.id);
    newCommentReplyInput.attr('value', comment.id);
    if (comment.parentId === null) {
        newComment.appendTo('#comments-' + fileName);
    } else {
        if ($('div[data-parent-id="' + comment.parentId + '"]').length > 0) {
            newComment.insertAfter($('div[data-parent-id="' + comment.parentId + '"]').last());
        } else {
            newComment.insertAfter('#comment-' + comment.parentId);
        }
    }
}

function removeFile(e, name) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('csrf_name', $('.csrfName').val());
    formData.append('csrf_value', $('.csrfValue').val());

    $.ajax({
        url: '/remove/' + name,
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            data = JSON.parse(data);
            window.location.replace('/');
            if (data.status === 'success') {
                alert('Файл успешно удален');
            }
        },
        error: function () {
            alert('Произошла ошибка. Обновите страницу и попробуйте ещё раз');
        }
    })
}
