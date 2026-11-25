<article id="post-<?php the_ID(); ?>" <?php post_class('article-default'); ?>>
    <?php
    behub_post_thumbnail('post-thumbnail', true);
    ?>
    <div class="post-content">
        <?php
        /**
         * Functions hooked in to behub_loop_post action.
         *
         * @see behub_post_header          - 15
         * @see behub_post_content         - 30
         */
        do_action('behub_loop_post');
        ?>
    </div>

</article><!-- #post-## -->

