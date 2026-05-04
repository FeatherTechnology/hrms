$(document).ready(function () {
    $(document).on('click', '#add_agent, #back_btn', function () {
        swapTableAndCreation();
        getAgentCode();

    });
    $('#submit_agent_creation').click(function (event) {
        event.preventDefault();
        //Validation
        let agent_code = $('#agent_code').val(); let agent_name = $('#agent_name').val(); let mobile1 = $('#mobile1').val(); let mobile2 = $('#mobile2').val(); let area = $('#area').val(); let occupation = $('#occupation').val(); let agent_id = $('#agent_id').val();
        //validateField(agent_name, '#agent_name');
        var data = [ 'agent_code','agent_name','mobile1']
        
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#'+entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

         if (isValid) {
            $.post('api/agent_creation/submit_agent_creation.php', { agent_code, agent_name, mobile1, mobile2, area, occupation, agent_id }, function (response) {
                if (response == '2') {
                    swalSuccess('Success', 'Agent Added Successfully!');
                } else if (response == '1') {
                    swalSuccess('Success', 'Agent Updated Successfully!');
                } else {
                    swalError('Error', 'Error Occurred!');
                }
                $('#agent_id').val('');
                $('#agent_creation').trigger('reset');
                getAgentTable();
                swapTableAndCreation();//to change to div to table content.

            });

        }
    });
    $('#mobile1, #mobile2').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });
    $(document).on('click', '.agentActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/agent_creation/agent_creation_data.php', { id: id }, function (response) {
            swapTableAndCreation();
            $('#agent_id').val(id);
            $('#agent_code').val(response[0].agent_code);
            $('#agent_name').val(response[0].agent_name);
            $('#mobile1').val(response[0].mobile1);
            $('#mobile2').val(response[0].mobile2);
            $('#area').val(response[0].area);
            $('#occupation').val(response[0].occupation);

        }, 'json');
    });
    $(document).on('click', '.agentDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Agent Details?', getAgentDelete, id);
        return;
    });

})
$(function () {
    getAgentTable()
});
function getAgentTable() {
    $.post('api/agent_creation/agent_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'agent_code',
            'agent_name',
            'area',
            'occupation',
            'mobile1',
            'action'
        ];
        appendDataToTable('#agent_create', response, columnMapping);
        setdtable('#agent_create', "Agent Creation List");

    }, 'json')
}
function swapTableAndCreation() {
    if ($('.agent_table_content').is(':visible')) {
        $('.agent_table_content').hide();
        $('#add_agent').hide();
        $('#agent_creation_content').show();
        $('#back_btn').show();

    } else {
        $('.agent_table_content').show();
        $('#add_agent').show();
        $('#agent_creation_content').hide();
        $('#back_btn').hide();
    }
}
function getAgentCode() {
    $.ajax({
        url: 'api/agent_creation/getAgentCode.php',
        type: "post",
        dataType: "json",
        data: {},
        cache: false,
        success: function (response) {
            var agent_code = response;
            $('#agent_code').val(agent_code);
        }
    })
}
function getAgentDelete(id) {
    $.post('api/agent_creation/delete_agent_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Agent Deleted Successfully!');
            getAgentTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Used in Loan Calculation');
        } else {
            swalError('Error', 'Failed to Delete Agent: ' + response);
        }
    }, 'json');
}
$('button[type="reset"], #back_btn').click(function (event) {
    event.preventDefault();
    $('input').each(function () {
        $(this).val('');
    });
    $('input').css('border', '1px solid #cecece');
});


