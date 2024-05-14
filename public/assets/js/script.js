jQuery(document).ready(function($) {
    var modal = $('#signatureModal');
    var openModalBtn = $('#w_agree_openModal_Btn');
    var closeModalBtn = $('.close');

    $('.signatureDiv').hide();

    openModalBtn.on('click', function() {
        modal.css('display', 'block');
    });

    closeModalBtn.on('click', function() {
        modal.css('display', 'none');
    });

    $(window).on('click', function(event) {
        if (event.target == modal[0]) {
            modal.css('display', 'none');
        }
    });

    var canvas = $('#canvas')[0];
    canvas.setAttribute('name', 'signer_canvas');

    var context = canvas.getContext('2d');
    var isDrawing = false;
    var lastX = 0;
    var lastY = 0;
    var lineOpacity = 1; // Adjust opacity here (0-1)
    var lineWidth = 3; // Adjust line thickness here

    function startDrawing(e) {
        isDrawing = true;
        [lastX, lastY] = [e.offsetX, e.offsetY];
    }

    function draw(e) {
        if (!isDrawing) return;
        context.beginPath();
        context.moveTo(lastX, lastY);
        context.lineTo(e.offsetX, e.offsetY);
        context.globalAlpha = lineOpacity; // Set opacity
        context.lineWidth = lineWidth; // Set line thickness
        context.stroke();
        [lastX, lastY] = [e.offsetX, e.offsetY];
    }

    function stopDrawing() {
        isDrawing = false;
    }

    $(canvas).on('mousedown', startDrawing);
    $(canvas).on('mousemove', draw);
    $(canvas).on('mouseup', stopDrawing);
    $(canvas).on('mouseout', stopDrawing);

    var clearBtn = $('#clearBtn');
    clearBtn.on('click', function () {
        context.clearRect(0, 0, canvas.width, canvas.height);
    });

    var acceptBtn = $('#acceptBtn');
    acceptBtn.on('click', function () {
        var signerNameInput = $('#signerName');
        var signerName = signerNameInput.val();
        var regexval = new RegExp("^[a-zA-Z ]+$");

        var isSignatureEmpty = isCanvasEmpty(canvas);

        if (signerName === '' && isSignatureEmpty) {
            signerNameInput.addClass('invalid');
            $('#signatureError').text('Please enter the signer name and provide a signature.');
            return;
        } else if (signerName === '') {
            signerNameInput.addClass('invalid');
            $('#signatureError').text('Please enter the signer name.');
            return;
        } else if (!regexval.test(signerName)) {
            $('#signatureError').text('Only letters are allowed.');
            return;
        } else if (isSignatureEmpty) {
            $('#signatureError').text('Please provide a signature.');
            return;
        }

        signerNameInput.removeClass('invalid');
        $('#signatureError').empty();

        var signatureImage = $('#signatureImage');
        signatureImage.empty();

        var imgData;
        if (isSignatureEmpty) {
            var canvasTmp = document.createElement('canvas');
            canvasTmp.width = canvas.width;
            canvasTmp.height = canvas.height;
            var contextTmp = canvasTmp.getContext('2d');
            contextTmp.font = '18px Arial';
            contextTmp.fillText(signerName, 10, 50);
            imgData = canvasTmp.toDataURL();
        }
        else {
            imgData = canvas.toDataURL();
        }

        var img = $('<img>');
        img.attr('src', imgData);
        signatureImage.append(img);

        jQuery('.signer_canvas').val(imgData);

        // Download signature image
        var downloadLink = $('#download_sign');
        
        downloadLink.attr('href', imgData);
        downloadLink.attr('download', 'signature.png');
        downloadLink.text('Download');
        signatureImage.append(downloadLink);

        modal.css('display', 'none');

        // Update post_meta canvas value
        var signer_name = $("#signerName").val();
        var productID = $("#wp_agree_product_id").attr("data-product")

        $.ajax({
            url: wc_checkout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'update_canvas_value',
                signer_name: signer_name,
                signer_canvas: imgData,
                productID:productID
            },
            success: function (response) {
                // Handle the response if needed
                if (response.data.success == true) {
                    $('.signer_value').html(response.data.signer_name);
                    $('.signatureImage').attr("src", response.data.signer_canvas);
                    $('.signatureDiv').show();
                }
            },
            error: function (xhr, status, error) {
                console.log('Error updating canvas value: ' + error);
            }
        });
    });

    function isCanvasEmpty(canvas) {
        var pixelData = context.getImageData(0, 0, canvas.width, canvas.height).data;

        for (var i = 0; i < pixelData.length; i += 4) {
            if (pixelData[i] !== 0 || pixelData[i + 1] !== 0 || pixelData[i + 2] !== 0 || pixelData[i + 3] !== 0) {
                return false;
            }
        }

        return true;
    }
});
