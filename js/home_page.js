$(document).ready(function () {

    
    $('#submit_upload').click(function() {

        // validation
        let fileInput = $('#home_upload')[0].files[0];
        if (!fileInput) {
            $("#home_uploadcheck").show();
            return false;
        } else {
            $("#home_uploadcheck").hide();
        }

        var fd = new FormData();
        fd.append('home_upload', fileInput);

        $.ajax({
            url: 'api/common_files/home_upload.php',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.trim() == "success") {
                    $('.uploadModal').modal('hide');
                    window.location.href = "home_page";
                } else {
                    alert("Error: " + res);
                }
            }
        });

    });
    

});

// close Modal
    function closeChartsModal() {
        $('.uploadModal').modal('hide');

    }
