<?php
use dcms\event\helpers\Helper;

$abonado_type = Helper::get_abonado_type();
$socio_type = Helper::get_socio_type();

?>

<div id="modal-filter" class="modal-filter">

<div class="modal-content">

    <!-- filters-->
    <section class="efilters">
        <!-- Number -->
        <div class="efilter">
            <span><?= __('Number', DCMS_EVENT_DOMAIN)?></span>

            <label><?= __('From', DCMS_EVENT_DOMAIN) ?>
                <input id="from-number" type ="number" />
            </label>

            <label><?= __('To', DCMS_EVENT_DOMAIN) ?>
                <input id="to-number" type ="number" />
            </label>
        </div>
        <!-- Abonado type -->
        <div class="efilter">
            <span><?= __('Abonado type', DCMS_EVENT_DOMAIN)?></span>

            <?php foreach ($abonado_type as $key => $value) { ?>
                <label><input type="checkbox" id="<?= $key ?>" value="<?= $key ?>"><?= $value ?></label>
            <?php }?>
        </div>
        <!-- Abonado type -->
        <div class="efilter">
            <span><?= __('Socio type', DCMS_EVENT_DOMAIN)?></span>

            <?php foreach ($socio_type as $key => $value) { ?>
                <label><input type="checkbox" id="<?= $key ?>" value="<?= $key ?>"><?= $value ?></label>
            <?php }?>
        </div>
        <!-- Events before -->
        <div class="efilter">
            <span><?= __('Number events before', DCMS_EVENT_DOMAIN)?></span>

            <label><?= __('Less than or equal to:', DCMS_EVENT_DOMAIN) ?>
                <input type="number" id="events-before" value="" />
            </label>
        </div>
    </section>

    <!-- Buttons -->
    <section class="top-ebuttons">
        <a class="btn-clear" href="#"><?php _e('Clear', DCMS_EVENT_DOMAIN) ?></a>
        <a class="btn-filter button button-primary" href="#"><?php _e('Filter', DCMS_EVENT_DOMAIN) ?></a>
    </section>

    <!-- list results -->
    <section class="eresults">

    </section>

    <!-- Buttom buttons -->
    <section class="fotter-ebuttons">
        <a class="btn-cancel button button-secondary" href="#"><?php _e('Cancel', DCMS_EVENT_DOMAIN) ?></a>
        <a class="btn-add button button-primary" href="#"><?php _e('Select users', DCMS_EVENT_DOMAIN) ?></a>
    </section>
</div>

</div>


<!-- <span class="close" onclick="closeModal()">&times;</span> -->