<style>
    .dcms-shortcode{
        background-color: #0073AA;
        border-radius:4px;
        Padding:10px 20px;
        color:white;
        margin-bottom:12px;
    }
</style>

<div class="wrap">
<h1><?php _e('Events Settings', DCMS_EVENT_DOMAIN) ?></h1>

<h2>Shortcodes</h2>
<hr>

<section class="dcms-shortcode">
    <span><?php _e('You can use this shortcode to show user account details: ') ?></span>
    <strong>[<?php echo DCMS_EVENT_ACCOUNT ?>]</strong>
</section>

<section class="dcms-shortcode">
    <span><?php _e('You can use this shortcode to show user sidebar: ') ?></span>
    <strong>[<?php echo DCMS_EVENT_SIDEBAR ?>]---[/<?php echo DCMS_EVENT_SIDEBAR ?>]</strong>
</section>

<section class="dcms-shortcode">
    <span><?php _e('You can use this shortcode to show list events for a user: ') ?></span>
    <strong>[<?php echo DCMS_EVENT_LIST ?>]</strong>
</section>

</div>