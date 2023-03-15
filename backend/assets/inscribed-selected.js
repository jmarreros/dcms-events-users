(function ($) {
    'use strict';

    $('.container-selected .save-notify a').click(function (e) {
        e.preventDefault();
        let rows = $("#selected-users-table").find("tr:not(:first)").find("td:first");
        const event_id = $("#selected-users-table").data('event-id');

        let identifies = [];
        for (let i = 0; i < rows.length; i++) {
            const identify = $(rows[i]).text();
            if (identify) {
                identifies.push(identify);
            }
        }

        $.ajax({
            url: dcms_inscribed_selected.ajaxurl,
            type: 'post',
            data: {
                action: 'dcms_ajax_import_selected',
                nonce: dcms_inscribed_selected.nonce,
                identifies,
                event_id
            },
            beforeSend: function () {
                $('.save-notify .button').addClass('disabled-link');
                $('#msg-save-import').text('Enviando...').show();
            },
        })
            .done(function (res) {
                $('.save-notify .button').removeClass('disabled-link');
                $('#msg-save-import').text(res.message).show();
            });

    });


    $('#form-upload').submit(function (e) {
        e.preventDefault();

        const files = $('#upload-file')[0].files;

        if (files.length <= 0) {
            alert('Tienes que seleccionar algún archivo');
            return;
        }

        const size = (files[0].size / 1024 / 1024).toFixed(2);
        if (size > 4) {
            alert(`Tu archivo pesa ${size}MB. No puedes subir archivos mayores a 4MB`);
            return;
        }

        let filename = files[0].name;
        let extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
        if (extension === '.XLS' || extension === '.XLSX') {
            excelFileToJSON(files[0]);
        } else {
            alert("Please select a valid excel file.");
        }

    });

    function excelFileToJSON(file) {
        try {
            let reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = function (e) {

                let data = e.target.result;
                let workbook = XLSX.read(data, {
                    type: 'binary'
                });

                let firstSheetName = workbook.SheetNames[0];
                //reading only first sheet data
                let jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheetName]);

                //displaying the json result into HTML table
                displayJsonToHtmlTable(jsonData);
            }
        } catch (e) {
            console.error(e);
        }
    }

    function displayJsonToHtmlTable(jsonData) {
        $("#selected-users-table").find("tr:not(:first)").remove();
        $(".container-selected .total-info").text(0);
        $("#msg-upload").text('');

        let b = [];
        let j = 0;

        // Add body
        if (jsonData.length > 0) {
            for (let i = 1; i < jsonData.length; i++) {
                b[j++] = "<tr>";
                b[j++] = `<td> ${jsonData[i]["Identificativo"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["PIN"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Numero"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Referencia"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["N.I.F."] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Nombre"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Apellidos"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["E-MAIL"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Cant Convivientes"] ?? ''}</td>`;
                b[j++] = `<td> ${jsonData[i]["Inscrito por"] ?? ''}</td>`;
                b[j++] = `<td></td>`;
                b[j++] = `<td></td>`;
                b[j++] = "</tr>";
            }

            $(".container-selected .total-info").text(jsonData.length);
            $("#selected-users-table tr").first().after(b.join(''));
            $("#msg-upload").text('Archivo cargado correctamente');
        }
    }

    // Resend functionality
    $('.container-inscribed .resend').click(function (e) {
        e.preventDefault();
        resend_mail(this, 'dcms_ajax_resend_mail_join_event');
    });

    $('.container-selected .resend').click(function (e) {
        e.preventDefault();
        resend_mail(this, 'dcms_ajax_resend_mail_join_event');
    });


    // General function to resend email
    function resend_mail(obj, action) {
        const userID = $(obj).data('user-id');
        const eventID = $(obj).data('event-id');
        const userName = $(obj).data('user-name');
        const email = $(obj).data('email');

        const confirmation = confirm("¿Reenviar correo a: " + email + "?");

        if (confirmation) {
            $.ajax({
                url: dcms_inscribed_selected.ajaxurl,
                type: 'post',
                data: {
                    action,
                    nonce: dcms_inscribed_selected.nonce,
                    userID,
                    eventID,
                    userName,
                    email
                },
            })
                .done(function (res) {
                    res = JSON.parse(res);
                    if (res.status === 0) {
                        alert('Hubo algún error al enviar el correo');
                    } else {
                        $(obj).text('✅');
                    }
                });
        }
    }

})(jQuery);


// $('#form-upload').submit(function(e){
//     e.preventDefault();
//
//     const fd = new FormData();
//     const files = $('#upload-file')[0].files;
//
//     if (files.length <= 0 ) {
//         alert('Tienes que seleccionar algún archivo');
//         return;
//     }
//
//     const size = (files[0].size / 1024 / 1024 ).toFixed(2);
//     if ( size > 4){
//         alert(`Tu archivo pesa ${size}MB. No puedes subir archivos mayores a 4MB`);
//         return;
//     }
//
//     fd.append('file',files[0]);
//     fd.append('action', 'dcms_ajax_add_file_import_selected');
//     fd.append('nonce', dcms_inscribed_selected.nonce);
//
//     $.ajax({
//         url: dcms_inscribed_selected.ajaxurl,
//         type: 'post',
//         dataType: 'json',
//         data: fd,
//         contentType: false,
//         processData: false,
//         beforeSend: function(){
//             $('#msg-upload').text('Enviando...').show();
//         },
//         success: function(res){
//             $('#msg-upload').text(res.message);
//         }
//     });
// });