(function( $ ) {
	'use strict';

    // $('#form-upload').submit(function(e){
    //     e.preventDefault();
    //
    //     const fd = new FormData();
    //     const files = $('#file')[0].files;
    //
    //     if (files.length <= 0 ) {
    //         alert('Tienes que seleccionar algún archivo');
    //         return;
    //     }
    //
    //     const size = (files[0].size / 1024 / 1024 ).toFixed(2);
    //     if ( size > 2){
    //         alert(`Tu archivo pesa ${size}MB. No puedes subir archivos mayores a 2MB`);
    //         return;
    //     }
    //
    //     fd.append('file',files[0]);
    //     fd.append('action', 'dcms_ajax_add_file');
    //     fd.append('nonce', dcmsUpload.nonce);
    //
    //     $.ajax({
    //         url: dcmsUpload.ajaxurl,
    //         type: 'post',
    //         dataType: 'json',
    //         data: fd,
    //         contentType: false,
    //         processData: false,
    //         beforeSend: function(){
    //             $('#message').text('Enviando...');
    //             $('#message').show();
    //         },
    //         success: function(res){
    //             $('#message').text(res.message);
    //         }
    //     });
    // });

    // Resend functionality
    $('.container-inscribed .resend').click( function(e){
        e.preventDefault();
        resend_mail(this, 'dcms_ajax_resend_mail_join_event');
    });

    $('.container-selected .resend').click( function(e){
        e.preventDefault();
        resend_mail(this, 'dcms_ajax_resend_mail_join_event');
    });


    // General function to resend email
    function resend_mail(obj, action){
        const userID = $(obj).data('user-id');
        const eventID = $(obj).data('event-id');
        const userName = $(obj).data('user-name');
        const email = $(obj).data('email');

        const confirmation = confirm("¿Reenviar correo a: " + email + "?");

        if ( confirmation ){
            $.ajax({
                url : dcms_inscribed_selected.ajaxurl,
                type: 'post',
                data: {
                    action,
                    nonce : dcms_inscribed_selected.nonce,
                    userID,
                    eventID,
                    userName,
                    email
                },
            })
                .done( function(res) {
                    res = JSON.parse(res);
                    if (res.status === 0){
                        alert('Hubo algún error al enviar el correo');
                    }else {
                        $(obj).text('✅');
                    }
                });
        }
    }

})( jQuery );

