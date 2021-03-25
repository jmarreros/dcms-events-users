(function($){


    $("#frm-account-details").submit(function(e){
        e.preventDefault();

        let data = {
            action : 'dcms_ajax_save_account',
            nonce : dcms_vars.naccount
        }

        // Dynamic data editable fields
        $('table.dcms-user-details input').each(function(){
            data[$(this).attr('id')] = $(this).val();
        });

		$.ajax({
			url : dcms_vars.ajaxurl,
			type: 'post',
            data,
            beforeSend: function(){
                $('.lds-ring').show();
                $('#send.button').val('Enviando ...').prop('disabled', true);;
                $('section.message').hide();
            }
        })
        .done( function(res) {
            res = JSON.parse(res);
            show_message(res);
        })
        .always( function() {
            $('.lds-ring').hide();
            $('#send.button').val('Enviar').prop('disabled', false);;
        });

	});

    // Aux function to show message
    function show_message(res){
        if (res.status == 0 ) {
            $('section.message').addClass('error');
        } else {
            $('section.message').removeClass('error');
        }

        $('section.message').show().html(res.message);
    }

})(jQuery);