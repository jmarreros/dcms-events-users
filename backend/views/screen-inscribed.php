<?php
    use dcms\event\backend\Report;

    $report = new Report();
    $aviable_events = $report->get_aviable_events();
?>

<div class="wrap">
<h1><?php _e('Incritos por evento', 'dcms-events-users') ?></h1>

<hr>

<section class="container-report">
    <header>
        <form method="post" id="frm-search" class="frm-search" action="">
            <section class="filter">
                <?php
                    echo "<select name='options[event]' class='aviable-events'>";
                    foreach ($aviable_events as $event){
                        echo "<option value='".$event->ID."' ";
                        echo selected( $options['event'], $event->ID ) . " >";
                        echo $event->post_title;
                        echo "</option>";
                    }
                    echo "</select>";
                ?>
                <button type="button" id="btn-filter-event" class="btn-search button button-primary">Filtrar</button>
            </section>
        </form>
        <section class="buttons-export"></section>
    </header>
</section>


</div>


