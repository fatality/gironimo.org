<?php

/**
 * file: comments.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

?>

<aside id="comments" class="clearfix">

<?php
    if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
        die ('Please do not load this page directly. Thanks!');
    
    if (post_password_required()) {
        $string = '<div class="help"><p class="nocomments">Dieser Artikel ist passwortgeschützt. Gib dein Passwort ein um die Kommentare zu lesen.</p></div>';
        echo $string;
        return;
    }
?>

<?php if (have_comments()): ?>
    <h3 id="comments-title">
        <?php printf(_n('Ein Kommentar', '%1$s Kommentare', get_comments_number(), 'gironimo'), number_format_i18n(get_comments_number())); ?>
    </h3>
    <p class="write-comment-link"><a href="#respond" class="button button-info"><?php _e('Schreib einen Kommentar &rarr;', 'gironimo'); ?></a></p>
    <ol class="commentlist">
        <?php wp_list_comments(array('callback' => 'gironimo_comments')); ?>
    </ol>
    <nav class="comment-nav">
        <ul class="clearfix">
            <li><?php previous_comments_link() ?></li>
            <li><?php next_comments_link() ?></li>
        </ul>
    </nav>
<?php else: ?>
    <?php if (comments_open()): ?>
        
    <?php else: ?>
        <?php if (!comments_open() && !is_page()): ?>
            <p class="nocomments">Kommentare geschlossen</p>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php
    comment_form(
        array(
            'cancel_reply_link' => __('<span class="button button-warning">Antworten abbrechen</span>', 'gironimo'),
            'comment_notes_before' => __('<p class="comment-notes">Benötigte Felder werden mit einem <span class="required">*</span> markiert.</p>', 'gironimo'),
            'comment_field' => '<div class="comment-form-comment"><label for="comment">' . _x('Nachricht <span class="required">*</span>', 'noun', 'gironimo') . '</label><textarea id="comment" class="user-input" name="comment" rows="8"></textarea></div>'
        )
    );
?>
</aside>
