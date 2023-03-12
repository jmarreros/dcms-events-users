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
            <span><?= __('Number', 'dcms-events-users')?></span>

            <label><?= __('From', 'dcms-events-users') ?>
                <input id="from-number" type ="number" />
            </label>

            <label><?= __('To', 'dcms-events-users') ?>
                <input id="to-number" type ="number" />
            </label>
        </div>
        <!-- Abonado type -->
        <div class="efilter abonado-type">
            <span><?= __('Abonado type', 'dcms-events-users')?></span>

            <?php
                $i = 0;
                foreach ($abonado_type as $key => $value): ?>
                <label><input type="checkbox" id="<?= $key ?>" value="<?= $key ?>"><?= $value ?></label>
            <?php
                $i++;
                if ( $i%2 == 0 ) echo "<br/>";
                endforeach;
            ?>
        </div>
        <!-- Abonado type -->
        <div class="efilter socio-type">
            <span><?= __('Socio type', 'dcms-events-users')?></span>

            <?php foreach ($socio_type as $key => $value) { ?>
                <label><input type="checkbox" id="<?= $key ?>" value="<?= $key ?>"><?= $value ?></label>
            <?php }?>
        </div>
        <!-- Events before -->
        <div class="efilter">
            <span><?= __('Number events before', 'dcms-events-users')?></span>

            <label><?= __('Less than or equal to:', 'dcms-events-users') ?>
                <input type="number" id="events-before" value="" min="0" max="100000" />
            </label>
        </div>

        <!-- Buttons -->
        <section class="top-ebuttons">
            <a class="btn-clear" href="#"><?php _e('Clear', 'dcms-events-users') ?></a>
            <a class="btn-filter button button-primary" href="#"><?php _e('Filter', 'dcms-events-users') ?>
                <div class="lds-ring" style="display:none"><div></div><div></div><div></div><div></div></div>
            </a>

        </section>

    </section>


    <!-- list results -->
    <section class="eresults">
        <table class="tbl-results">
            <tr>
                <?php
                    echo "<th></th>";
                    $fields = Helper::get_filter_fields();
                    foreach ($fields as $field) {
                        echo "<th> $field </th>";
                    }
                ?>
                <?php ?>
            </tr>
        </table>
    </section>

    <section class="footer-info">
        Total: <strong> <span class="total-info"></span> </strong>
    </section>

    <!-- Buttom buttons -->
    <section class="fotter-ebuttons">
        <a id="cancel-add-customers" class="btn-cancel button button-secondary" href="#"><?php _e('Cancel', 'dcms-events-users') ?></a>
        <a class="btn-select-all button button-primary" href="#"><?php _e('Select all', 'dcms-events-users') ?>
            <div class="lds-ring" style="display:none"><div></div><div></div><div></div><div></div></div>
        </a>
    </section>
</div>

</div>
