<?php

/**
 * file: footer.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

?>

    <div class="clear"></div>
    <footer id="colophon" class="clearfix" role="contentinfo">
        <nav id="sub-nav">
            <?php gironimo_footer_links(); ?>
        </nav>
        <p class="attribution">
            <span class="license">Alle Inhalte sind, sofern nicht anders angegeben, lizenziert unter der <a href="http://creativecommons.org/licenses/by-sa/3.0/de/" rel="nofollow">Creative Commons BY-SA 3.0 (German)</a>.</span>
            &copy; <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>" rel="home"><?php bloginfo('name'); ?></a>. Alle Rechte vorbehalten.
        </p>
    </footer>
    <?php wp_footer(); ?>
</body>
</html>
