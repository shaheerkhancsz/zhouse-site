<?php
$price = get_post_meta(get_the_ID(), 'workspace_price', true);
?>

<div class="column-item workspace-style-4">
    <article <?php post_class('workspace'); ?>>
        <div class="workspace-inner">
            <div class="workspace-post-thumbnail">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('behub-workspace-grid'); ?>
                <?php endif; ?>
            </div><!-- .post-thumbnail -->
            <div class="workspace-content">
                <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <?php if($price): ?>
                    <div class="price">
                        <label><?php echo esc_html__('from', 'behub'); ?></label>
                        <span><?php echo esc_html($price); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article><!-- #post-## -->
</div>