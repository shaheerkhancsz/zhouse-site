<div class="column-item virtualtour">
    <div class="virtualtour-inner">
        <div class="virtualtour-post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <i class="behub-icon-virtual"></i>
            </a>
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('behub-post-grid'); ?>
            <?php endif; ?>
        </div><!-- .post-thumbnail -->
        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    </div>
</div>