<?php
/** @var int $id_event */
/** @var array $fields_inscribed_table */
/** @var array $selected_users */
?>
<div class="general-container selected">
    <h2><?php _e( 'Seleccionados evento', 'dcms-events-users' ) ?></h2>
    <hr>
    <section class="container-selected">

        <div class="header-info">
            <div class="user-event-info">
				<?= __( 'Total selected: ', 'dcms-events-users' ) ?><strong> <span
                            class="total-info"><?= count( $selected_users ) ?></span></strong>
            </div>
            <div>
                <a class="btn-export button button-primary"
                   href="<?= admin_url() ?>admin-post.php?action=process_export_list_customers&id_post=<?= $id_event ?>&only_selected=1"
                   target="_blank"><?php _e( 'Exportar seleccionados', 'dcms-events-users' ) ?></a>
            </div>
        </div>

        <div class="container-table">
            <table id="selected-users-table" class="dcms-table report-user-event" data-event-id="<?= $id_event ?>">
                <tr>
					<?php
					foreach ( $fields_inscribed_table as $field ) {
						echo "<th>" . $field . "</th>";
					}
					?>
                    <th>#Pedido</th>
                    <th>correo</th>
                </tr>

				<?php foreach ( $selected_users as $row ): ?>
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
                        <td>
							<?php if ( $row['id_order'] ) : ?>
                                <a href="/wp-admin/post.php?post=<?= $row['id_order'] ?>&action=edit">
									<?= $row['id_order'] ?>
                                </a>
							<?php endif; ?>
                        </td>
                        <td>
							<?php if ( empty( $row['parent'] ) || $row['parent'] === $row['identify'] ) : ?>
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

        <div class="footer-info">
            <div class="import-file">
                <form enctype="multipart/form-data" method="post" id="form-upload">
                    Selecciona algún archivo:
                    <div>
                        <input name="upload-file" id="upload-file" type="file"/>
                        <input class="button button-primary" type="submit" value="Importar seleccionados"/>
                    </div>
                    <div id="msg-upload" class="message"></div>
                </form>
            </div>
            <div class="save-notify">
                <a class="button button-primary">
					<?php _e( 'Guardar y notificar seleccionados', 'dcms-events-users' ) ?>
                </a>
                <div id="msg-save-import" class="message"></div>
            </div>
        </div>

    </section>
</div>