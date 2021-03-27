(function($){

    $(".btn-filter").click(function(e){
        e.preventDefault();

        // Fill values to send
        let numbers = [ $('#from-number').val(), $('#to-number').val() ];

        let abonado_types = [];
        $('.efilter.abonado-type input:checked').each( function(){
            abonado_types.push($(this).val());
        } );

        let socio_types = [];
        $('.efilter.socio-type input:checked').each( function(){
            socio_types.push($(this).val());
        } );


		$.ajax({
			url : dcms_vars.ajaxurl,
			type: 'post',
			data: {
				action : 'dcms_ajax_filter',
                nonce : dcms_vars.nonce,
                numbers,
                abonado_types,
                socio_types
			},
            beforeSend: function(){
                // $('.lds-ring').show();
                // $('#send.button').val('Enviando ...').prop('disabled', true);;
                // $('section.message').hide();
            }
        })
        .done( function(res) {
            // res = JSON.parse(res);
            console.log(res);
            // show_message(res);
        })
        .always( function() {
            // $('.lds-ring').hide();
            // $('#send.button').val('Enviar').prop('disabled', false);;
        });

	});


    // Modal
    $('#open-add-customers').click(function(){
        $('.modal-filter').show();
    })

    $('#cancel-add-customers').click(function(){
        $('.modal-filter').hide();
    })

})(jQuery);

