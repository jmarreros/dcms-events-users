<?php
/** @var array $rows */
?>
<div class="wrap">
    <div class="general-container sepa">
        <h2><?php _e( 'Usuarios que enviaron información SEPA', 'dcms-events-users' ) ?></h2>
        <hr>
        <section class="container-sepa">

            <div class="header-info">
                <div class="user-event-info">
					<?= __( 'Total: ', 'dcms-events-users' ) ?><strong> <span
                                class="total-info"><?= count( $rows ) ?></span></strong>
                </div>
                <div>
                    <a class="btn-export button button-primary"
                       href="<?= admin_url() ?>admin-post.php?action=process_export_users_sepa"
                       target="_blank"><?php _e( 'Exportar', 'dcms-events-users' ) ?></a>
                </div>
            </div>

            <div class="container-table">
                <table id="sepa-users-table" class="dcms-table report-user-event">
                    <tr>
                        <th>Identificativo</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Archivo</th>
                        <th>Enviado</th>
                    </tr>

					<?php foreach ( $rows as $row ): ?>
                        <tr>
                            <td><?= $row['identify'] ?></td>
                            <td><?= $row['first_name'] ?></td>
                            <td><?= $row['last_name'] ?></td>
                            <td>
                                <a href="<?= $row['sepa_file_url'] ?>"
                                   target="_blank"><?= $row['sepa_file'] ?></a>
                            </td>
                            <td><?= $row['time'] ?></td>
                        </tr>
					<?php endforeach; ?>
                </table>
            </div>
        </section>
    </div>
</div>