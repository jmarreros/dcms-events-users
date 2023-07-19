(function( $ ) {
    'use strict';

    console.log('Desde SEPA');

    $('.locked-sepa').on('click', function(e){
        const user_id = $(this).val();
        const checked = $(this).is(':checked');


        $.ajax({
            url : dcms_sepa.ajaxurl,
            type: 'post',
            data: {
                action : 'dcms_ajax_locked_sepa',
                nonce : dcms_sepa.nonce,
                checked,
                user_id
            },
            beforeSend: function(){
                $(e.target).prop('disabled', true);
            },
            success: function(res){
                if ( res.status ) {
                    $(e.target).prop('disabled', false);
                } else {
                    alert('Ocurrió algún error al actualizar el estado del usuario');
                }
            }
        });

    });
})( jQuery );