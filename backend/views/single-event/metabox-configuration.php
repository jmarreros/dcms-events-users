<?php
/** @var int $product_id */
/** @var array $products */
?>
<section class="dcms-convivientes">

<p>
<label>
    <input type="checkbox" id="enable-convivientes" name="<?= DCMS_ENABLE_CONVIVIENTES ?>" <?php checked($enable_convivientes, 1) ?> > Habilitar Ingreso de Convivientes
</label>
</p>
<p>
<label>
    <input type="checkbox" id="lock-inscriptions" name="<?= DCMS_LOCK_INSCRIPTIONS ?>" <?php checked($lock_inscriptions, 1) ?> > Bloquear inscripciones y ediciones
</label>
</p>

</section>
<hr>
<section>
    <p>
        <label for="product-event">Producto asociado</label>
        <br>
        <select name="event-product-id" id="event-product-id">
            <option value="0" <?php selected( $product_id, 0 ) ?> >
	            - Ninguno -
            </option>
            <?php  foreach ( $products as $product ) : ?>
                <option value="<?= $product['id'] ?>" <?php selected( $product_id, $product['id'] ) ?> >
                    <?= $product['name'] . ' : ' . $product['price'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
</section>