(function( $ ) {
    'use strict';

    console.log('Desde SEPA');

    $('.locked-sepa').on('click', function(e){
        const user_id = $(this).val();
        // console.log($(this).is(':checked'));

        $.ajax({
            url : dcms_sepa.ajaxurl,
            type: 'post',
            data: {
                action : 'dcms_ajax_locked_sepa',
                nonce : dcms_sepa.nonce,
                user_id
            },
            beforeSend: function(){
                // console.log('Antes de enviar');
            },
            success: function(res){
              console.log(res);
            }
        });

    });
})( jQuery );