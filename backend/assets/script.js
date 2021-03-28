(function($){

    // to fill string data
    let str = '';
    let str_ids = '';
    let count = 0;

    // Clear button
    $(".btn-clear").click( function(e) {
        e.preventDefault();
        $('.tbl-results tr').not(':first').remove();
        $('.efilter input[type=number]').val('');
        $('.efilter input:checkbox').removeAttr('checked');
        $('.modal-filter .total-info').text('');
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
                $('.btn-filter .lds-ring').show();
                $('.btn-filter').addClass('disabled');
            }
        })
        .done( function(res) {
            res = JSON.parse(res);
            fill_table_filter(res);
        })
        .always( function() {
            $('.btn-filter .lds-ring').hide();
            $('.btn-filter').removeClass('disabled');
        });

	});

    // Build the result table
    function fill_table_filter(res){

        $('.tbl-results tr').not(':first').remove();

        str = '';
        count = 0;
        str_ids = '';
        for(let i = 0; i < res.length; i++){
            if ( res[i].number ){
                count++;
                str_ids += res[i].user_id + ',';
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
        $('.modal-filter .total-info').text(count);
    }

    // Fill tbl-users-event
    $('.btn-select-all').click(function(e){
        e.preventDefault();
        if ( str.length > 0){
            $('.tbl-users-event tr').not(':first').remove();
            $('.tbl-users-event tr').first().after(str);
            $('#id_user_event').val(str_ids.slice(0, -1));
            $('.modal-filter').hide();
            $('.user-event-info .total-info').text(count);
        } else{
            alert('No hay registros para agregar');
        }
    });

    // Modal
    $('#open-add-customers').click(function(){
        $('.modal-filter').show();
    })

    $('#cancel-add-customers').click(function(){
        $('.modal-filter').hide();
    })


})(jQuery);

