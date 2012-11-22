<?php

/**
 * file: sidebar.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

?>

<div id="secondary" class="sidebar widget-area" role="complementary">
    <?php if (is_active_sidebar('secondary')): ?>
        <?php dynamic_sidebar('secondary'); ?>
    <?php else: ?>
        <aside class="help">
            <p>Aktivier ein paar Widgets.</p>
        </aside>
    <?php endif; ?>
</div>
