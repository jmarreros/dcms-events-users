<?php
// $aviable_events
// $id_event
// $suscribed_users
// $fields_table
?>

    <div class="wrap">
        <header>
            <form method="post" id="frm-filter" class="frm-filter" action="">
                <div class="container-filter">
                    <label for="select_event">Seleccionar evento: </label>
			        <?php if ( count( $aviable_events ) ) : ?>
				        <?php
				        echo "<select name='select_event' id='select_event'>";
				        foreach ( $aviable_events as $event ) {
					        echo "<option value='" . $event->ID . "' ";
					        echo selected( $id_event, $event->ID ) . " >";
					        echo $event->post_title;
					        echo "</option>";
				        }
				        echo "</select>";
				        ?>
                        <button type="submit" id="btn-filter-event" class="btn-search button button-primary">
                            Filtrar
                        </button>
			        <?php else : ?>
                        <strong>No hay eventos activos</strong>
			        <?php endif; ?>
                </div>
            </form>
        </header>

        <div class="general-container inscribed">
            <h2><?php _e( 'Inscritos por evento', 'dcms-events-users' ) ?></h2>

            <section class="container-inscribed">
                <div class="header-info">
                        <div class="user-event-info">
					        <?= __( 'Total Joined: ', 'dcms-events-users' ) ?><strong> <span
                                        class="total-info"><?= count( $suscribed_users ) ?></span> </strong>
                        </div>
                        <div class="butons-user-event">
                            <a class="btn-export button button-primary"
                               href="<?= admin_url() ?>admin-post.php?action=process_export_list_customers&id_post=<?= $id_event ?>&only_joined=1"
                               target="_blank"><?php _e( 'Export Joined', 'dcms-events-users' ) ?></a>
                        </div>
                    </div>


                <div class="container-table">
                    <table class="dcms-table report-user-event">
                        <tr>
					        <?php
					        foreach ( $fields_table as $field ) {
						        echo "<th>" . $field . "</th>";
					        }
					        ?>
                            <th>Correo</th>
                        </tr>

				        <?php foreach ( $suscribed_users as $row ): ?>
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
                                       data-event-id="<?= $id_event ?>"
                                       data-user-id="<?= $row['user_id'] ?>"
                                       data-user-name="<?= $row['name'] ?>"
                                       data-email="<?= $row['email'] ?>"
                                       href="#">Reenviar</a></td>
                            </tr>
				        <?php endforeach; ?>
                    </table>
                </div>

            </section>
        </div>


        <div class="general-container selected">
            <h2><?php _e( 'Seleccionados evento', 'dcms-events-users' ) ?></h2>

            <section class="container-selected">
            </section>
        </div>


    </div>

<?php
