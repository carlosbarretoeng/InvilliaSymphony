let browserHasNecessaryFeatures = () => {
    let element = document.createElement('div')
    return (('draggable' in element) || ('ondragstart' in element && 'ondrop' in element)) && 'FormData' in window && 'FileReader' in window
}

let clearInput = () => {
    $input.val('')
    $form.find('button').addClass('disabled')
    $form.find('label').html('<div><strong>Choose a file</strong><span class="dragAndDrop">&nbsp;or drag it here</span>.</div>')
}

let clearMessage = () => {
    $(".messageDiv").find('div > div').attr('class', '')
    $(".messageDiv").find('span').text('')
}

let showMessage = (type, message = '') => {
    let $messageDiv = $(".messageDiv")
    switch (type){
        case "error":
            $messageDiv.find('div > div').attr('class', 'red column')
            $messageDiv.find('span').text(message)
            clearInput()
            break;
        case "uploading":
            $messageDiv.find('div > div').attr('class', 'blue column')
            $messageDiv.find('span').html("<div class=' animate__animated animate__flash animate__infinite'>Uploading</div>")
            break;
        case "success":
            $messageDiv.find('div > div').attr('class', 'green column')
            $messageDiv.find('span').text(message)
            break;
        default:
            $messageDiv.find('div > div').attr('class', 'violet column')
            $messageDiv.find('span').text(message)
    }
}

let processFiles = (files) => {
    clearMessage()
    if (!validateFiles(files)) {
        return
    }
    showFileNamesInLabel(files)
}

let validateFiles = (files) => {
    let fileSize = 0;
    $.each(files, (idx, file) => {
        fileSize += file.size
        if(file.type !== 'text/xml'){
            showMessage('error', 'File extension not allowed')
            return false;
        }
    })
    if(fileSize > $maxFileSize){
        showMessage('error', 'The Limit file size exceeded. Please select fewer or small files')
        return false;
    }

    $form.find('button').removeClass('disabled')
    return true;
}

let showFileNamesInLabel = (files) => {
    let text = []
    $.each(files, (idx, file) => {
        text.push(file.name)
    })
    if (text.length !== 0) $('form').find('label').text(text.join(", "));
}

let $maxFileSize = $('input[type="hidden"]').val()

let $form = $('form')
let $input = $('input[type="file"]')
let $errorMessage = $('.errorMessage span')
let droppedFiles = null

if (browserHasNecessaryFeatures) {
    $form.addClass('hasNecessaryFeatures')
    $form.on('drag dragstart dragend dragover dragenter dragleave drop', (evt) => {
        evt.preventDefault()
        evt.stopPropagation()
    }).on('dragover dragenter', () => {
        $form.addClass('isDragover')
    }).on('dragleave dragend drop', () => {
        $form.removeClass('isDragover')
    }).on('drop', (evt) => {
        droppedFiles = evt.originalEvent.dataTransfer.files
        processFiles(droppedFiles)
    })
}

$input.on('change', (evt) => {
    droppedFiles = evt.target.files
    processFiles(droppedFiles)
})

$form.on('submit', (evt) => {
    if ($form.hasClass('isUploading')) return false;
    showMessage('uploading')
    if (browserHasNecessaryFeatures) {
        evt.preventDefault()
        let ajaxData = new FormData($form.get(0))
        if (droppedFiles !== null) {
            $.each(droppedFiles, function (idx, file) {
                ajaxData.append($input.attr('name'), file)
            })
        }
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: ajaxData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            complete: () => {
                setTimeout(() => {
                    clearInput()
                    clearMessage()
                }, 5000)
            },
            success: (data) => {
                console.log(data)
                if (!!!data.success) {
                    showMessage('error', data.error || 'Unknown error')
                    return
                }
                showMessage('success', 'File Uploaded and Processed')
            },
            error: (err) => {
                showMessage('error', err.statusText)
            }
        })
    } else {
        console.log('B')
        evt.preventDefault()
    }
})