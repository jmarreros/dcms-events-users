(function($){

    // to fill string data
    let str_ids = '';
    let count = 0;

    // To show/hide wp-admin editor
    $( document ).ready(function() {
        $( "#page_template" ).trigger( "change" );
    });
        
    $('#page_template').change(function(){
        if ( $(this).val() === 'default' ){
            $('body.wp-admin ').removeClass('hide');
        } else {
            $('body.wp-admin ').addClass('hide');
        }
    });


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

        let exclude_observation_person = [];
        $('.efilter.observation_person input:checked').each( function(){
            exclude_observation_person.push($(this).val());
        } );

		$.ajax({
			url : dcms_vars.ajaxurl,
			type: 'post',
			data: {
				action : 'dcms_ajax_filter',
                nonce : dcms_vars.nonce,
                numbers,
                abonado_types,
                socio_types,
                exclude_observation_person
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
        const condition_count_event = parseInt( $('.efilter #events-before').val() );

        str = '';
        count = 0;
        str_ids = '';
        let events_user;
        let r = Array();
        let j = -1;
        for(let i = 0; i < res.length; i++){

                // User events en base a la columna manual observation7
                events_user = parseInt(res[i].observation7??0);

                if ( condition_count_event >= 0 ){
                    if ( condition_count_event < events_user ){
                        continue;
                    }
                }

                count++;
                str_ids += res[i].user_id + ',';

                // Optimization
                r[++j] = "<tr>";
                r[++j] = "<td><input type='checkbox' value='";
                r[++j] = res[i].user_id;
                r[++j] = "' /></td>";
                r[++j] = "<td>";
                r[++j] = res[i].number;
                r[++j] = "</td>";
                r[++j] = "<td>";
                r[++j] = res[i].name;
                r[++j] = "</td>";
                r[++j] = "<td>";
                r[++j] = res[i].lastname;
                r[++j] = "</td>";
                r[++j] = "<td>";
                r[++j] = res[i].sub_type;
                r[++j] = "</td>";
                r[++j] = "<td>";
                r[++j] = res[i].soc_type;
                r[++j] = "</td>";
                r[++j] = "<td>";
                r[++j] = events_user;
                r[++j] = "</td></tr>";
        }


        $('.tbl-results tr').first().after(r.join(''));

        $('.modal-filter .total-info').text(count);
    }

    // Fill tbl-users-event
    $('.btn-select-all').click(function(e){
        e.preventDefault();
        if ( count > 0){
            $('.tbl-users-event tr').not(':first').remove();
            $('.tbl-results tr').not(':first').clone().insertAfter($('.tbl-users-event tr').first());
            $('#id_user_event').val(str_ids.slice(0, -1)); // hidden field
            $('.modal-filter').hide();
            $('.top-user-event .total-info').text(count);
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


    // Checkbox to remove row
    $('#remove-customers').click(function(){
        let str_ids = '';
        $('.tbl-users-event tr').each(function(){
            const check = $(this).find('td:first-child input');

            if ( $(check).prop('checked') ){
                $(this).addClass('row-remove');
                str_ids = str_ids + check.val() + ',';
            } else {
                $(this).removeClass('row-remove');
            }
        });

        if ( str_ids.length > 1 ) str_ids = str_ids.slice(0, -1);
        $('#id_user_event_remove').val(str_ids);

    });

    // Checked
    $('.tbl-users-event tr td:first-child input').change(function(){
        if ( ! $(this).prop('checked') ){
            $(this).closest('tr').removeClass('row-remove');
            $('#remove-customers').trigger('click');
        }
    });

})(jQuery);

