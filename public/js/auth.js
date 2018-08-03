function submitAuth(e, action) {
    e.preventDefault();

    $('#auth-submit').attr("disabled", "disabled");
    var formData = new FormData($('#auth-form')[0]);

    $.ajax({
        url: action,
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            data = JSON.parse(data);
            if (data.status === 'success') {
                window.location.replace('/');
            } else {
                var keys = Object.keys(data.errors);
                if ('all' in data.errors) {
                    $('#all-error').text(data.errors['all'])
                } else {
                    for (i = 0; i < keys.length; i++) {
                        $('#' + keys[i] + '-error').text(data.errors[keys[i]]);
                    }
                }
            }
            $('#auth-submit').removeAttr("disabled");
        },
        error: function () {
            $('#auth-submit').removeAttr("disabled");
        }
    })
}