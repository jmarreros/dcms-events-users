(function( $ ) {
	'use strict';

    $('.report-user-event .resend').click( function(e){

        e.preventDefault();

        const userID = $(this).data('user-id');
        const eventID = $(this).data('event-id');
        const userName = $(this).data('user-name');
        const email = $(this).data('email');

        const confirmation = confirm("¿Reenviar correo a: " + email + "?");

        if ( confirmation ){
            $.ajax({
                url : dcms_report_event.ajaxurl,
                type: 'post',
                data: {
                    action : 'dcms_ajax_resend_mail',
                    nonce : dcms_report_event.nonce,
                    userID,
                    eventID,
                    userName,
                    email
                    },
                })
                .done( function(res) {
                    res = JSON.parse(res);
                    if (res.status == 0){
                        alert('Hubo algún error al enviar el correo');
                    }else {
                        $(e.target).text('✅');
                    }
                });
        }
    });

})( jQuery );