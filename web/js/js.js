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

/******* button onclicks *******/

function deleteOnclick(id) {

    // console.log(id);
    $.ajax({
    url: '/todo/delete/id='+id+'',
    type: 'POST',
    dataType: 'json',
    success: function(response) {
       
        var elementID = $("#"+response.id+"");
        elementID.remove();
     
        if (response.success) {
            
            if (response.messages.length > 0) {
                //set variable for div
                var messagesDiv = $('.messageDiv');

                // Clear existing messages
                messagesDiv.empty();

                // Add new message to message box div
                $.each(response.messages, function(index, message) {

                    console.log(message);
                    var messageElement = $('<div class="alert alert-success">').text(message);
                    messagesDiv.append(messageElement);

                });
            }
        } else {
            alert("Something went wrong. Please try again.")
        }

    }
    });

}

function completeOnClick(id) {

    $.ajax({
    url: '/todo/complete/id='+id+'',
    type: 'POST',
    dataType: 'json',
    success: function(response) {
       
        var elementID = $("#complete_"+response.id+"");
        elementID.remove();

        if (response.success) {
            // Display FlashBag messages
            if (response.messages.length > 0) {
                //set variable for div
                var messagesDiv = $('.messageDiv');

                // Clear existing messages
                messagesDiv.empty();

                // Add new message to message box div
                $.each(response.messages, function(index, message) {

                    console.log(message);
                    var messageElement = $('<div class="alert alert-success">').text(message);
                    messagesDiv.append(messageElement);
                });
            }
        } else {
            alert("Something went wrong. Please try again.")
        }
    }
    });

} 

/**************/ 