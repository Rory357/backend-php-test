$(document).ready(function () {
    $('#paginationTable').DataTable();
    $('.dataTables_length').addClass('bs-select');
});

$(document).on('click', '.modalPopUpButton', function(e) {
    e.preventDefault();

    // Get the target modal and the data from the clicked element
    var targetModal = $(this).data('target');
    var todoId = $(this).data('id');

    console.log(targetModal);
    console.log(todoId);

    $.ajax({
        url: '/todo/view/id='+todoId+'',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
           
            var jsonStr = JSON.stringify(response);

            $(targetModal).find('#todoId').text(jsonStr);

    
        }
    });

    // Set the retrieved data in the modal content
    $(targetModal).find('#todoId').text(todoId);

    // Open the modal
    $(targetModal).modal('show');
});