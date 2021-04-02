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
    <div class="usidebar-name">
        <?= $name ?>
    </div>
    <div class="usidebar-number">
        <?= __('Socio Number:', DCMS_EVENT_DOMAIN); ?>
        <span><?= $number ?></span>
    </div>

    <?php
        echo $content;
    ?>

    <a class="btn btn-logout" href="<?= wp_logout_url( home_url() ); ?>">
        <?= __('Logout', DCMS_EVENT_DOMAIN) ?>
    </a>
</section>
