<?php
/** @var string $current_file */

$message = '';
if ( $current_file ) {
	$message = "<p>Ya has subido un archivo, si quieres cambiarlo, sube uno nuevo. ";
	$message .= " <a href='" . DCMS_SEPA_FILES_URL . $current_file . "'  target='_blank'>Ver archivo</a></p>";
}

?>
<section class="form-sepa-container">
    <form action="" enctype="multipart/form-data" method="post" class="form-sepa-upload">
        <div>
            <label for="upload-sepa">Selecciona el archivo PDF a subir: </label>
            <input type="file" id="upload-file" name="upload-file"/>
        </div>
        <input type="submit" id="sepa-submit" class="fusion-button button-flat fusion-button button-flat fusion-button-default-size button-default" value="Enviar archivo" />
    </form>

    <section class="message-sepa"><?= $message ?></section>
</section>