<?php
/** @var string $current_file */
/** @var bool $is_locked */

$message = '';
if ( $current_file ) {
	$message = "<p>Ya has subido un archivo SEPA:  ";
	$message .= " <a href='" . DCMS_SEPA_FILES_URL . $current_file . "'  target='_blank'>Ver archivo</a></p>";
}
?>
<section class="form-sepa-container">
    <?php if ( ! $is_locked ) : ?>
        <form action="" enctype="multipart/form-data" method="post" class="form-sepa-upload">
            <div>
                <label for="upload-sepa">Selecciona el archivo PDF a subir: </label>
                <input type="file" id="upload-file" name="upload-file"/>
            </div>
            <input type="submit" id="sepa-submit" class="fusion-button button-flat fusion-button button-flat fusion-button-default-size button-default" value="Enviar archivo" />
        </form>
    <?php endif; ?>

    <section class="message-sepa"><?= $message ?></section>
</section>