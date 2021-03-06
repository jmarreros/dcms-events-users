<?php
// $user
// $email
// $name
// $number
// $content

?>
<section class="container-user-sidebar">
    <div class="usidebar-email">
        <?= get_avatar($email); ?>
    </div>
    <span class="gravatar">
        Puedes cambiar tu imagen desde <a href="https://es.gravatar.com/" target="_blank">gravatar</a>
    </span>

    <div class="usidebar-name">
        <?php echo $name; ?>
    </div>
    <div class="usidebar-number">
        <?= __('Número de Abonado:', 'dcms-events-users'); ?>
        <span><?= $number ?></span>
    </div>

    <?php
        echo $content;
    ?>

    <!-- <a class="btn btn-logout" href="<?= wp_logout_url( home_url() ); ?>">
        <?= __('Salir', 'dcms-events-users') ?>
    </a> -->
</section>
