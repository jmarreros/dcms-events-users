(function($){

    // Account Details
    $("#frm-account-details").submit(function(e){
        e.preventDefault();

        const data = {
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
                $('#send.button').val('Enviando ...').prop('disabled', true);
                $('section.message').hide();
            }
        })
        .done( function(res) {
            res = JSON.parse(res);
            show_message(res);
        })
        .always( function() {
            $('.lds-ring').hide();
            $('#send.button').val('Enviar').prop('disabled', false);
        });

	});


    // Update Events User
    $('.container-list-events .btn-join').click(function(e){
        e.preventDefault();

        const id_post = $(this).data('id');
        let joined = $(this).data('joined');

        const data = {
            action : 'dcms_ajax_update_join',
            nonce : dcms_vars.nevent,
            id_post,
            joined
        }

        $.ajax({
			url : dcms_vars.ajaxurl,
			type: 'post',
            data,
        beforeSend: function(){
                ($('.top-list .lds-ring').clone().show()).insertAfter($(e.target));

                $('section.message').hide();
                $(e.target).addClass('disabled');

            }
        })
        .done( function(res) {
            res = JSON.parse(res);

            if ( res.status ){
                joined = res.joined??joined; //0 or 1

                const text = joined ? dcms_vars.nojoin : dcms_vars.join;
                $(e.target).data('joined', joined);
                $(e.target).text(text);
                $(e.target).toggleClass('join').toggleClass('nojoin');
            }

            show_message(res);
        })
        .always( function() {
            $(e.target).parent().find('.lds-ring').remove();
            $(e.target).removeClass('disabled');
            $('.lds-ring').hide();
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