
<section class="dcms-fases">

<label>
    <input type="checkbox" id="enable-fases" name="<?= DCMS_ENABLE_FASES ?>" <?php checked($enable_fases, 1) ?> > Habilitar Fases para el evento
</label>

<br>

<div class="dcms-limit-dates">
    <label>Límite Fase 1:
    <input type="date" id="fase1" name="<?= DCMS_FASE_1 ?>"
        <?php if ($fase1) echo "value='$fase1'"; ?>
    >
    </label>

    <label for="start">Límite Fase 2:
    <input type="date" id="fase2" name="<?= DCMS_FASE_2 ?>"
        <?php if ($fase2) echo "value='$fase2'"; ?>
    >
    </label>
</div>

</section>