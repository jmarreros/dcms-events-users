(function ($) {

    // Account Details
    $("#frm-account-details").submit(function (e) {
        e.preventDefault();
        const sbutton = '.container-account-details #send.button';
        const smessage = '.container-account-details section.message';
        const sspinner = '.container-account-details .lds-ring';
        const scontainer = '.container-account-details';

        const data = {
            action: 'dcms_ajax_save_account',
            nonce: dcms_uaccount.naccount
        }

        // Dynamic data editable fields
        $('table.dcms-user-details input').each(function () {
            data[$(this).attr('id')] = $(this).val();
        });

        $.ajax({
            url: dcms_uaccount.ajaxurl,
            type: 'post',
            data,
            beforeSend: function () {
                $(sspinner).show();
                $(sbutton).val('Enviando ...').prop('disabled', true);
                $(smessage).hide();
            }
        })
            .done(function (res) {
                res = JSON.parse(res);
                show_message(res, scontainer);
            })
            .always(function () {
                $(sspinner).hide();
                $(sbutton).val('Actualizar datos').prop('disabled', false);
            });

    });

    // Accept terms and conditions
    // $('.container-list-events .event-conditions').click(function(){
    //     const button = $(this).closest('.item-event').find('.btn-join.join');
    //     const container_event = $(this).closest('.item-event').find('.inscription-container');
    //     const container_children = $(this).closest('.item-event').find('.container-children');
    //     const allow_children = $(this).closest('.item-event').find('.container-question input');
    //     const container_question = $(this).closest('.item-event').find('.container-question');

    //     if ( $(this).prop('checked') ){
    //         button.prop("disabled", false);
    //         allow_children.prop("disabled", false);
    //         container_event.addClass('accept');
    //         container_question.addClass('mark');
    //     } else {
    //         button.prop("disabled", true);
    //         container_children.hide();
    //         allow_children.prop("disabled", true);
    //         allow_children.prop( "checked", false );
    //         container_event.removeClass('accept');
    //         container_question.removeClass('mark');
    //     }
    // });

    // Allow children
    $('.container-question .question-children').click(function (e) {
        const container_children = $(this).closest('.item-event').find('.container-children');
        const btn_join = $(this).closest('.item-event').find('.button.btn-join');

        if ($(this).prop('checked')) {
            container_children.show();
            btn_join.hide();
        } else {
            container_children.hide();
            btn_join.show();
        }
    });

    // Join individual user event
    $('.container-list-events .btn-join').click(function (e) {
        e.preventDefault();

        // Confirmation menssage
        // if ( $(this).siblings('.container-question').length ){
        //     if ( ! $(this).siblings('.container-question').prop('checked') ) {
        //         const confirmation = confirm('¿Estas seguro de continuar sin agregar acompañante?');
        //         if ( !confirmation ) return;
        //     }
        // }

        const smessage = $('.container-list-events section.message-join-event');
        const sspinner = '.container-list-events .top-list .lds-ring';

        const id_post = $(this).data('id');
        let joined = $(this).data('joined');

        const data = {
            action: 'dcms_ajax_update_join',
            nonce: dcms_uevents.nevent,
            id_post,
            joined
        }

        $.ajax({
            url: dcms_uevents.ajaxurl,
            type: 'post',
            data,
            beforeSend: function () {
                ($(sspinner).clone().show()).insertAfter($(e.target));

                $(smessage).hide();
                $(e.target).prop("disabled", true);
            }
        })
            .done(function (res) {
                res = JSON.parse(res);

                $(e.target).prop("disabled", false);

                if (res.status) {
                    joined = res.joined ?? joined; //0 or 1

                    const text = joined ? dcms_uevents.nojoin : dcms_uevents.join;

                    $(e.target).data('joined', joined);
                    $(e.target).text(text);
                    $(e.target).removeClass('join').addClass('nojoin');
                    $(e.target).prop("disabled", true);
                    $(e.target).closest('.item-event').find('.select-children input').prop("disabled", true);
                    $(e.target).closest('.item-event').find('.terms-conditions').remove();
                }

                show_message(res, smessage);
            })
            .always(function () {
                $(e.target).parent().find('.lds-ring').remove();
                $(sspinner).hide();
            });

    });

    // Validate children (acompañante)
    $('.list-children li .cvalidate').click(function (e) {
        e.preventDefault();

        const cspinner = $('.container-list-events .top-list .lds-ring');
        const id_post = $(this).closest('.inscription-container').find('.btn-join').data('id');
        let cmessage = $(this).closest('li').find('.message');
        let identify = $(this).closest('li').find('.cidentify').val();
        let pin = $(this).closest('li').find('.cpin').val();

        // Validate inputs
        if (!validate_empty_inputs(identify, pin, cmessage)) return;

        const data = {
            action: 'dcms_ajax_validate_children',
            nonce: dcms_echildren.nchildren,
            id_post,
            identify,
            pin
        }

        $.ajax({
            url: dcms_uevents.ajaxurl,
            type: 'post',
            data,
            beforeSend: function () {
                (cspinner.clone().show()).insertAfter($(e.target));
                $(cmessage).hide();
                $(e.target).prop("disabled", true);
            }
        })
            .done(function (res) {
                res = JSON.parse(res);

                if (res.status) {
                    $(e.target).closest('li').data('identify', res.identify);
                    $(e.target).closest('li').data('id', res.id_user);
                    $(e.target).hide();
                    $(e.target).closest('li').find('.cclear').show();
                    $(e.target).closest('li').find('.cinputs').hide();
                    $(e.target).closest('li').find('.cdata').html('➜' + res.name).show();
                }

                show_message(res, cmessage);
            })
            .always(function () {
                $(e.target).prop("disabled", false);
                $(e.target).parent().find('.lds-ring').remove();
            });

    });

    // Clear children
    $('.list-children li .cclear').click(function (e) {
        e.preventDefault();

        // Remove from database
        if ($(this).hasClass('child_db')) {

            $(this).removeClass('child_db');
            const id_user = $(e.target).data('uid');
            const id_event = $(e.target).data('eid');

            const data = {
                action: 'dcms_ajax_remove_child',
                nonce: dcms_echildren.nchildren,
                id_user,
                id_event
            }

            $.ajax({
                url: dcms_uevents.ajaxurl,
                type: 'post',
                data
            })
                .done(function (res) {
                    res = JSON.parse(res);
                    console.log(res);
                });
        }

        // Process other controls
        $(e.target).hide();
        $(e.target).closest('li').data('identify', '');
        $(e.target).closest('li').data('id', '');
        $(e.target).closest('li').find('.cvalidate').show();
        $(e.target).closest('li').find('.cinputs').show();
        $(e.target).closest('li').find('.cinputs input').val('');
        $(e.target).closest('li').find('.cdata').html('').hide();
        $(e.target).closest('li').find('.message').html('').hide();

    });

    // Add and save children
    $('.container-children .btn-add-children').click(function (e) {
        e.preventDefault();
        ;
        const items = $(e.target).closest('.container-children').find('.list-children li');
        const cmessage = $(e.target).closest('.container-children').find('.add-children.message');
        const cspinner = $('.container-list-events .top-list .lds-ring');
        const id_post = $(this).closest('.inscription-container').find('.btn-join').data('id');

        let children_data = [];

        $.each(items, function (index, item) {
            const id = $(item).data('id');
            if (id) children_data.push(id);
        });

        // Validate empty values
        if (children_data.length == 0) {
            const res = {
                status: 0,
                message: 'No hay acompañantes a agregar'
            }
            show_message(res, cmessage);
            return;
        }

        const data = {
            action: 'dcms_ajax_add_children',
            nonce: dcms_echildren.nchildren,
            id_post,
            children_data,
        }

        $.ajax({
            url: dcms_uevents.ajaxurl,
            type: 'post',
            data,
            beforeSend: function () {
                (cspinner.clone().show()).insertAfter($(e.target));
                $(cmessage).hide();
                $(e.target).prop("disabled", true);
            }
        })
            .done(function (res) {

                // user inscription
                // button_join.trigger('click');

                res = JSON.parse(res);

                if (res.status) {
                    // Revisar clases
                    $(e.target).closest('.item-event').find('.terms-conditions').remove();
                    $(e.target).closest('.item-event').find('.btn-join').removeClass('join').addClass('nojoin').prop('disabled', true).text(dcms_uevents.join);
                    $(e.target).closest('.item-event').find('.container-question').remove();
                    $(e.target).closest('.item-event').find('.list-children .message').remove();
                    $(e.target).closest('.item-event').find('.list-children .cactions').remove();
                    $(e.target).closest('.item-event').find('.list-children .cinputs').remove();
                    $(e.target).closest('.item-event').find('.list-children').addClass('blocked');
                    $(e.target).closest('.item-event').find('.container-children .lds-ring').remove();
                    $(e.target).closest('.item-event').find('.container-children .btn-add-children').remove();
                }

                show_message(res, cmessage);
            })
            .always(function () {
                $(e.target).prop("disabled", false);
                $(e.target).parent().find('.lds-ring').remove();
            });

    });


    // Aux validate empty strings in inputs
    function validate_empty_inputs(identify, pin, container) {
        if (identify.length == 0 || pin.length == 0) {
            let res = {
                status: 0,
                message: 'Ingresa algún identificador y PIN'
            }
            show_message(res, container);
            return false;
        }
        return true;
    }


    // Aux function to show message
    function show_message(res, container) {

        if (typeof container == 'string') {
            container = container + ' section.message';
        }

        if (res.status == 0) {
            $(container).addClass('error');
        } else {
            $(container).removeClass('error');
        }

        $(container).show().html(res.message);
    }


    // Setting purchase screen
    $('.setting-purchase .user-child .button').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('remove')) {
            $(this).parent().addClass('not-selected');
        }
        if ($(this).hasClass('add')) {
            $(this).parent().removeClass('not-selected');
        }
    });

    $('.setting-purchase .buttons-container .button').click(function (e) {
        e.preventDefault();

        let children = [];
        $('.setting-purchase .user-child:not(.not-selected)').each(function () {
            children.push($(this).data('child'));
        });

        const data = {
            action: 'dcms_ajax_continue_with_payment',
            nonce: dcms_uevents.nevent,
            id_user: $(this).data('user'),
            id_event: $(this).data('event'),
            children
        }

        $.ajax({
            url: dcms_uevents.ajaxurl,
            type: 'post',
            data,
            beforeSend: function () {
                // $('.setting-purchase .buttons-container .button').addClass('disabled');
                $('.setting-purchase .buttons-container .message').text('');
                $('.setting-purchase .buttons-container .lds-ring').show();
            }
        })
            .done(function (res) {
                $('.setting-purchase .buttons-container .message').text(res.message);
                $('.setting-purchase .buttons-container .lds-ring').hide();
                if (res.status === 1) {
                    location.href = res.url;
                }
            })


        console.log(children);
    })


})(jQuery);