<?php

class RecentPosts_Widget extends WP_Widget
{
    
    public function __construct()
    {
        parent::__construct(
            'recentposts_widget', // Base ID
            'RecentPosts_Widget', // Name
            array('description' => __('Zeige die letzten Einträge mit Thumbnails, Datum und Kommentaren an.', 'gironimo' ),) // Args
        );
    }
    
    public function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        
        $articles = new WP_Query('showposts=5');
        if (!$articles)
        {
            echo __('Keine Artikel', 'gironimo');
        }
        else
        {
            echo '<ul>';
            while ($articles->have_posts())
            {
                $articles->the_post();
                ?>
                <li class="clearfix">
                    <?php
                        if (function_exists('get_the_post_thumbnail'))
                        {
                    ?>
                    <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php echo esc_attr(the_title()); ?>"><?php echo get_the_post_thumbnail(get_the_ID(), array(60,60)); ?></a>
                    <?php
                        }
                    ?>
                    <span class="recentposts-content">
                        <span class="title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(the_title()); ?>" rel="bookmark"><?php the_title() ?></a></span>
                        <span class="meta"><?php the_time(__('j. M Y', 'gironimo')); ?> | <a href="<?php the_permalink(); ?>#comments"><?php comments_number(__('Keine Kommentare', 'gironimo'), __('1 Kommentar', 'gironimo'), __('% Kommentare', 'gironimo') ); ?></a></span>
                    </span>
                </li>
            <?php
            }
            echo '</ul>';
        }
        echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    public function form($instance)
    {
        if (isset($instance['title']))
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __('Neuer Titel', 'gironimo');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titel:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }

}
add_action('widgets_init', create_function('', 'register_widget("recentposts_widget");'));
