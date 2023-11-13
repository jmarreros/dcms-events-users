<?php
/** @var array $available_events */
/** @var int $id_event */
/** @var array $fields_inscribed_table */
/** @var array $subscribed_users */
?>
<div class="general-container inscribed">
    <h2><?php _e( 'Inscritos evento', 'dcms-events-users' ) ?></h2>
    <hr>
    <section class="container-inscribed">
        <div class="header-info">
            <div class="user-event-info">
				<?= __( 'Total Joined: ', 'dcms-events-users' ) ?><strong> <span
                            class="total-info"><?= count( $subscribed_users ) ?></span> </strong>
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
					foreach ( $fields_inscribed_table as $field ) {
						echo "<th>" . $field . "</th>";
					}
					?>
                    <th>Fecha Máxima</th>
                    <th>Correo</th>
                </tr>

				<?php foreach ( $subscribed_users as $row ): ?>
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
                        <td><?= $row['maximum_date'] ?></td>
                        <td>
							<?php if ( empty($row['parent']) || $row['parent'] === $row['identify'] ) : ?>
                                <a class="resend"
                                   data-event-id="<?= $id_event ?>"
                                   data-user-id="<?= $row['user_id'] ?>"
                                   data-user-name="<?= $row['name'] ?>"
                                   data-email="<?= $row['email'] ?>"
                                   href="#">Reenviar</a>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
            </table>
        </div>

    </section>
</div>