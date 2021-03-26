(function($){

    $(".btn-filter").click(function(e){
        e.preventDefault();

        let numbers = [ $('#from-number').val(), $('#to-number').val() ];


        console.log(numbers);

		$.ajax({
			url : dcms_vars.ajaxurl,
			type: 'post',
			data: {
				action : 'dcms_ajax_filter',
                nonce : dcms_vars.nonce,
                numbers : numbers
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

