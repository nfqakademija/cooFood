$(document).ready(function(){
    init();
});

$(document).resize(function(){

});

function init(){
    $('#peopleLimit').hide();

    $('#checkbox').change(function(){
        if(this.checked)
            $('#peopleLimit').fadeIn('medium');
        else
            $('#peopleLimit').fadeOut('medium');

    });

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

$('#coofood_Eventbundle_orderitem_idProduct').on('change', function() {
    jQuery.ajax({
        url: "/product/productajax/" + this.value,
        dataType: 'json',
        success: function (data) {
            console.log(data.status);
            console.log(data.image);
            console.log(data.description);
            $('.productimage').html('<img src="' + data.image + '" alt="product image" height="150">');
            $('.productdescription').html(data.description);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $.notify(textStatus, "error");
            $.notify(errorThrown, "error");
        }
    });
});