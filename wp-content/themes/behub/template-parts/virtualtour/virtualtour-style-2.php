<div class="column-item virtualtour virtualtour-style-2">
    <div class="virtualtour-inner">
        <div class="virtualtour-post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <i class="behub-icon-virtual"></i>
                <h5 class="entry-title"><?php the_title(); ?></h5>
            </a>
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('behub-post-grid'); ?>
            <?php endif; ?>
        </div><!-- .post-thumbnail -->
    </div>
</div>