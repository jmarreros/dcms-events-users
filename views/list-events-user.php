<?php
// Web get this variables
// -----------------------
// $id_user
// $events

?>
<section class="container-list-events">

        <section class="top-list">
            <div class="lds-ring" style="display:none;"><div></div><div></div><div></div><div></div></div><!--spinner-->
            <section class="message" style="display:none;">
            </section>
        </section>

        <section class="body-list">
            <?php if ($events) :?>
                <ul class="list-events">
                <?php foreach ($events as $event):?>
                    <?php $joined = $event->joined ? 'not-join': 'join'; ?>
                    <li class="item-event">

                        <h3><?= $event->post_title ?></h3>

                        <a href="#"
                            class="button btn-join <?= $joined ?>"
                            data-id="<?= $event->id_post ?>"
                            data-joined="<?= $event->joined ?>"
                        >
                            <?php if ( $event->joined ) : ?>
                                <?= __('Not join', DCMS_EVENT_DOMAIN) ?>
                            <?php else: ?>
                                <?= __('Join', DCMS_EVENT_DOMAIN) ?>
                            <?php endif; ?>
                        </a>

                        <div><?= $event->post_content ?></div>

                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
</section>
