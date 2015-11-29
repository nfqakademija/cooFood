$(document).ready(function () {
        $("#orderForm").on('submit', (handleSubmit)
        ),
            $("#delete-order").on('submit', (handleSubmit)
            ),
            $("#join-shared-order").on('submit', (handleSubmit)
            )}
)

function handleSubmit(e)
{
    e.preventDefault();
    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: $(this).serialize()
    })
        .done(function (data) {
            $('#page').html(data['html']);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
        });
}
