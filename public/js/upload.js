$(document).ready(function () {
    var csrfName = $('#csrfName').val(),
        csrfValue = $('#csrfValue').val(),
        file,
        originalName,
        fileName,
        loading = false

    // automatic form submission when a file is selected
    $('#file').on('change', $("#file"), function (e) {
        e.preventDefault();

        file = $("#file")[0].files[0];
        originalName = file['name'];
        upload();
    });

    // drag and drop file
    var dropZone = $('#drop-zone'),
        dropZoneHover = $('#drop-zone-hover'),
        dropText = $('#drop-text'),
        clipboardText = $('#clipboard-text'),
        dragCounter = 0;

    if (typeof(window.FileReader) === 'undefined') {
        dropText.css('display', 'none');
    }

    dropZone.on('dragenter', function () {
        if (loading === true) {
            return false;
        }
        dragCounter++;
        dropZoneHover.css('display', 'block');
        return false;
    });

    dropZone.on('dragover', function () {
        if (loading === true) {
            return false;
        }
        dropZoneHover.css('display', 'block');
        return false;
    });

    dropZone.on('dragleave', function () {
        if (loading === true) {
            return false;
        }
        dragCounter--;
        if (dragCounter === 0) {
            dropZoneHover.css('display', 'none');
        }
        return false;
    });

    dropZone.on('drop', function (e) {
        e.preventDefault();

        if (loading === true) {
            return false;
        }

        dropZoneHover.css('display', 'none');
        file = e.originalEvent.dataTransfer.files[0];
        originalName = file['name'];
        upload();
    });

    // image in clipboard
    if (window.Clipboard) {
        clipboardText.css('display', 'none');
    }

    $(document).bind('paste', function (e) {
        e.preventDefault();

        if (loading === true) {
            return false;
        }

        var item = (e.clipboardData || e.originalEvent.clipboardData).items[0] || null;
        if (item && item.kind === "file") {
            file = item.getAsFile();
            originalName = file['name'];
            upload();
        }
    });

    function upload() {
        loading = true;
        $('#main-upload').addClass('animated bounceOutLeft');
        setTimeout(function () {
            $('#main-upload').css('display', 'none');
            $('#file-name').text(originalName);
            $('#main-load').css('display', 'block');
            $('#main-load').addClass('animated bounceInRight');
        }, 500);
        getNameFutureFileAndUpload();
    }

    function getNameFutureFileAndUpload() {
        $.ajax({
            url: '/',
            method: 'post',
            data: {
                csrf_name: csrfName,
                csrf_value: csrfValue,
                preUploadFile: true
            },
            success: function (data) {
                data = JSON.parse(data);
                if (file.size > data.maxFileSize) {
                    showFileLoadingError("Превышен максимально допустимый размер файла");
                    return false;
                }
                fileName = data.fileName
                $('#file-link').val(window.location.hostname + '/file/' + fileName);
                $('#update-file-data_').attr('id', 'update-file-data_' + fileName);
                uploadFile();
            },
            error: function () {
                showFileLoadingError();
            }
        });
    }

    var uploaded = false;
    function uploadFile() {
        var formData = new FormData();
        formData.append('csrf_name', csrfName);
        formData.append('csrf_value', csrfValue);
        formData.append('uploadFile', true);
        formData.append('file', file);

        $.ajax({
            url: '/',
            method: 'post',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                var startedAt = new Date();
                xhr.upload.onprogress = function (e) {
                    var percent = Math.round(e.loaded / e.total * 100);
                    $('#progress-bar').attr('value', percent);

                    var secondsElapsed = (new Date().getTime() - startedAt.getTime())/1000;
                    var bytesPerSecond = secondsElapsed ? e.loaded / secondsElapsed : 0;
                    var kbytesPerSecond = bytesPerSecond / 1000 ;
                    $('#progress-text').text(e.loaded + " of " + e.total + "KB (" + Math.round(kbytesPerSecond) + "KB/sec)");
                }
                return xhr;
            },
            success: function (errors) {
                errors = JSON.parse(errors);
                if (errors.length === 0) {
                    uploaded = true;
                    $('#load-file').css('display', 'none');
                    $('#load-file-success').css('display', 'block');
                    $('#form-update-data').attr('action', '/update/' + fileName);
                    $('#update-file-data-cancel').attr('href', '/file/' + fileName);
                    $('#go-to-file').attr('href', '/file/' + fileName);
                    $("#file-link").focus(function () {
                        this.select();
                    });
                } else {
                    if ("maxFileSize" in errors) {
                        showFileLoadingError("Превышен максимально допустимый размер файла");
                    } else {
                        showFileLoadingError();
                    }
                }
            },
            error: function () {
                showFileLoadingError();
            }
        });
    }

    $('#form-update-data').submit(function (e) {
        if (uploaded === false) {
            e.preventDefault();

            $('#update-file-data-submit').attr('disabled', 'disabled');

            var formData = new FormData($('#form-update-data')[0]);
            formData.append('updateFileData', 'true');

            $.ajax({
                url: '/',
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function (errors) {
                    errors = JSON.parse(errors);
                    if (errors.length === 0) {
                        var id = '#update-file-data_' + fileName;
                        $(id).addClass('animated bounceOutLeft');
                    } else {
                        $('#update-file-data-submit').removeAttr('disabled')
                        if ("description" in errors) {
                            $('#description-error-text').text(errors.description);
                        }
                    }
                },
                error: function () {
                    showFileLoadingError();
                }
            });
        }
    });
    
    function showFileLoadingError(errorText) {
        var def = "При загрузке файла произошка ошибка. Попробуйте ещё раз";
        if (errorText !== undefined) {
            $('#load-file-error__text').text(errorText);
        } else {
            $('#load-file-error__text').text(def);
        }
        $('#load-file-error').css('display', 'block');
        $('#load-file-success').css('display', 'none');
        $('#load-file').css('display', 'none');
    }

    $('#update-file-data-cancel').click(function (e) {
        if (uploaded === false) {
            e.preventDefault();

            var id = '#update-file-data_' + fileName;
            $(id).addClass('animated bounceOutLeft');
        }
    });
});