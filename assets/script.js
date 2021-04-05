(function($){

    // Account Details
    $("#frm-account-details").submit(function(e){
        e.preventDefault();
        const sbutton = '.container-account-details #send.button';
        const smessage = '.container-account-details section.message';
        const sspinner = '.container-account-details .lds-ring';
        const scontainer = '.container-account-details';

        const data = {
            action : 'dcms_ajax_save_account',
            nonce : dcms_uaccount.naccount
        }

        // Dynamic data editable fields
        $('table.dcms-user-details input').each(function(){
            data[$(this).attr('id')] = $(this).val();
        });

		$.ajax({
			url : dcms_uaccount.ajaxurl,
			type: 'post',
            data,
            beforeSend: function(){
                $(sspinner).show();
                $(sbutton).val('Enviando ...').prop('disabled', true);
                $(smessage).hide();
            }
        })
        .done( function(res) {
            res = JSON.parse(res);
            show_message(res, scontainer);
        })
        .always( function() {
            $(sspinner).hide();
            $(sbutton).val('Enviar').prop('disabled', false);
        });

	});


    // Update Events User
    $('.container-list-events .btn-join').click(function(e){
        e.preventDefault();

        const smessage = '.container-list-events section.message';
        const sspinner = '.container-list-events .top-list .lds-ring';
        const scontainer = '.container-list-events';

        const id_post = $(this).data('id');
        let joined = $(this).data('joined');

        const data = {
            action : 'dcms_ajax_update_join',
            nonce : dcms_uevents.nevent,
            id_post,
            joined
        }

        $.ajax({
			url : dcms_uevents.ajaxurl,
			type: 'post',
            data,
        beforeSend: function(){
                ($(sspinner).clone().show()).insertAfter($(e.target));

                $(smessage).hide();
                $(e.target).addClass('disabled');

            }
        })
        .done( function(res) {
            res = JSON.parse(res);

            if ( res.status ){
                joined = res.joined??joined; //0 or 1

                const text = joined ? dcms_uevents.nojoin : dcms_uevents.join;
                $(e.target).data('joined', joined);
                $(e.target).text(text);
                $(e.target).toggleClass('join').toggleClass('nojoin');
            }

            show_message(res, scontainer);
        })
        .always( function() {
            $(e.target).parent().find('.lds-ring').remove();
            $(e.target).removeClass('disabled');
            $(sspinner).hide();
        });

    });

    // Aux function to show message
    function show_message(res, container){
        container = container + ' section.message';

        if (res.status == 0 ) {
            $(container).addClass('error');
        } else {
            $(container).removeClass('error');
        }

        $(container).show().html(res.message);
    }

})(jQuery);