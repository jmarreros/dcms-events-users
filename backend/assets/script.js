(function($){

    // to fill string data
    let str = '';

    // Clear button
    $(".btn-clear").click( function(e) {
        e.preventDefault();
        $('.tbl-results tr').not(':first').remove();
        $('.efilter input[type=number]').val('');
        $('.efilter input:checkbox').removeAttr('checked');
    });


    // Filter button
    $(".btn-filter").click(function(e){
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

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
                $('.lds-ring').show();
                $('.btn-filter').addClass('disabled');
            }
        })
        .done( function(res) {
            res = JSON.parse(res);
            fill_table_filter(res);
        })
        .always( function() {
            $('.lds-ring').hide();
            $('.btn-filter').removeClass('disabled');
        });

	});

    // Build the result table
    function fill_table_filter(res){

        $('.tbl-results tr').not(':first').remove();

        str = '';
        for(let i = 0; i < res.length; i++){
            if ( res[i].number ){
                str += `
                    <tr>
                        <td>${res[i].user_id}</td>
                        <td>${res[i].number}</td>
                        <td>${res[i].name}</td>
                        <td>${res[i].lastname}</td>
                        <td>${res[i].sub_type}</td>
                        <td>${res[i].soc_type}</td>
                        <td></td>
                    </tr>
                    `;
            }
        }
        $('.tbl-results tr').first().after(str);
    }


    // Fill tbl-users-event
    $('.btn-select-all').click(function(e){
        e.preventDefault();
        $('.tbl-users-event tr').not(':first').remove();
        $('.tbl-users-event tr').first().after(str);
    });



    // Modal
    $('#open-add-customers').click(function(){
        $('.modal-filter').show();
    })

    $('#cancel-add-customers').click(function(){
        $('.modal-filter').hide();
    })


})(jQuery);

