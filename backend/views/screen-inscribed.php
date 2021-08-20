<?php
// $aviable_events
// $id_event
// $suscribed_users
// $fields_table
?>

<div class="wrap">
<h1><?php _e('Incritos por evento', 'dcms-events-users') ?></h1>

<hr>

<section class="container-report">
    <header>
        <form method="post" id="frm-filter" class="frm-filter" action="">
            <section class="container-filter">
                <?php
                    echo "<select name='select_event' class='aviable-events'>";
                    foreach ($aviable_events as $event){
                        echo "<option value='".$event->ID."' ";
                        echo selected( $id_event, $event->ID ) . " >";
                        echo $event->post_title;
                        echo "</option>";
                    }
                    echo "</select>";
                ?>
                <button type="submit" id="btn-filter-event" class="btn-search button button-primary">Filtrar</button>
            </section>
        </form>
        <section class="buttons-export"></section>
    </header>

    <table class="dcms-table">
        <tr>
            <?php
            foreach($fields_table as $field) {
                echo "<th>" . $field . "</th>";
            }
            ?>
            <th></th>
        </tr>

        <?php foreach ($suscribed_users as $row):  ?>
        <tr>
            <td><?= $row['identify'] ?></td>
            <td><?= $row['pin'] ?></td>
            <td><?= $row['number'] ?></td>
            <td><?= $row['reference'] ?></td>
            <td><?= $row['nif'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['lastname'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['children'] ?></td>
            <td><?= $row['parent'] ?></td>
            <td><a class="resend"
                data-id="<?= $id_event ?>"
                data-email="<?= $row->email ?>"
            href="#">Reenviar</a></td>
        </tr>
        <?php endforeach; ?>


    </table>
</section>
</div>

<?php
// TODO:
// Datos del evento y datos del usuario, si tiene hijos
// send_email_join_event()
