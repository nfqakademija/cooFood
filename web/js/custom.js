$(document).ready(function(){
    init();
});

$(document).resize(function(){

});

function init(){
    $('#orderForm').on('submit', function () {
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        }).done(function (data) {
            $('#page').html(data['html']);
            init();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });

        return false;
    });

    $('.delete-order').on('submit', function(){
        if (confirm('Patvirtinkite')) {
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize()
            }).done(function (data) {
                $('#page').html(data['html']);
                init();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            });
        }

        return false;
    });

    $('#join-shared-order').on('submit', function () {
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        }).done(function (data) {
            $('#page').html(data['html']);
            init();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });

        return false;
    });
}