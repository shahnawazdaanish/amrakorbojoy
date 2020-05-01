var sz = jQuery.noConflict();
var $ = jQuery.noConflict();

sz(document).ready(function(){

    function validURL(str) {
        // var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        //     '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        //     '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        //     '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        //     '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        //     '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        var pattern = new RegExp('[(http(s)?):\\/\\/(www\\.)?a-zA-Z0-9@:%._\\+~#=]{2,256}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%_\\+.~#?&//=]*)','ig'); // fragment locator
        return !!pattern.test(str);
    }

    var slider_amount = 0;
    $('.shaz-donate-form #country').parent().append('<ul class="list-item" id="newcountry" name="country"></ul>');
    $('.shaz-donate-form #country option').each(function(){
        $('#newcountry').append('<li data-value="' + $(this).val() + '">'+$(this).text()+'</li>');
    });
    $('.shaz-donate-form #country').remove();
    $('#newcountry').attr('id', 'country');
    $('.shaz-donate-form #country li').first().addClass('init');
    $(".shaz-donate-form #country").on("click", ".init", function() {
        $(this).closest("#country").children('li:not(.init)').toggle();
    });

    var allOptions = $(".shaz-donate-form #country").children('li:not(.init)');
    $(".shaz-donate-form #country").on("click", "li:not(.init)", function() {
        allOptions.removeClass('selected');
        $(this).addClass('selected');
        $("#country").children('.init').html($(this).html());
        allOptions.toggle();
    });

    var marginSlider = document.getElementById('slider-margin');
    if (marginSlider != undefined) {
        noUiSlider.create(marginSlider, {
            start: [500],
            step: 50,
            connect: [true, false],
            tooltips: [true],
            range: {
                'min': 0,
                'max': 50000
            },
            format: wNumb({
                decimals: 0,
                thousand: ',',
                prefix: '৳ ',
            })
        });
    }
    document.getElementById('slider-margin').noUiSlider.on('update', function (values, handle, unencoded) {
        // unencoded contains the raw value
        slider_amount = parseInt(unencoded);
    });


    $('#reset').on('click', function(){
        $('#register-form').reset();
    });
    /*document.getElementsByClassName('price_slider')[0].noUiSlider.on('update', function (values, handle, unencoded) {
        // unencoded contains the raw value
        alert(unencoded);
    });*/

    $(".shaz-donate-form #register-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        var country = $('#country li.selected').data('value');

        $.ajax({
            type: "POST",
            url: WPURLS.adminurl,
            dataType: 'application/json',
            data: form.serialize() + '&action=donation_submit&amount='+slider_amount+'&country='+country, // serializes the form's elements.
            complete : function(data){
                var response = JSON.parse(data.responseText);
                if(response.status !== undefined) {
                    if(response.status === 'success') {
                        alert(response.data);
                        if (validURL(response.data)) {
                            window.location.href = response.data;
                        } else {
                            // error
                        }
                    } else {

                    }
                } else {

                }
            }
        });
    });

    $('.shaz-donate-form #register-form').validate({
        rules : {
            name : {
                required: true,
            },
            email : {
                required: true,
                email : true
            },
            phone_number : {
                required: true,
            }
        },
        onfocusout: function(element) {
            $(element).valid();
        },
    });

    jQuery.extend(jQuery.validator.messages, {
        required: "",
        remote: "",
        email: "",
        url: "",
        date: "",
        dateISO: "",
        number: "",
        digits: "",
        creditcard: "",
        equalTo: ""
    });
});


(function($) {


})(jQuery);