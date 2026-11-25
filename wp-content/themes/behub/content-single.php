<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="single-content">
            <?php
            /**
             * Functions hooked in to behub_single_post_top action
             * @see behub_post_header         - 5
             * @see behub_post_thumbnail        - 10
             */
            do_action('behub_single_post_top');

            /**
             * Functions hooked in to behub_single_post action
             *
             * @see behub_post_content         - 30
             */
            do_action('behub_single_post');

            /**
             * Functions hooked in to behub_single_post_bottom action
             *
             * @see behub_post_taxonomy      - 5
             * @see behub_post_nav            - 10
             * @see behub_display_comments    - 20
             */
            do_action('behub_single_post_bottom');
            ?>

    </div>

</article><!-- #post-## -->
