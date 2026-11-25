<?php
$description = get_post_meta(get_the_ID(), 'workspace_description', true);
$team = get_post_meta(get_the_ID(), 'workspace_team', true);
$price = get_post_meta(get_the_ID(), 'workspace_price', true);
$sale = get_post_meta(get_the_ID(), 'workspace_sale', true);
?>

<div class="column-item workspace-style-2">
    <article <?php post_class('workspace'); ?>>
        <div class="workspace-inner">
            <div class="workspace-post-thumbnail">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('behub-workspace-grid'); ?>
                <?php endif; ?>
            </div><!-- .post-thumbnail -->
            <div class="workspace-content">
                <?php if($sale): ?>
                    <div class="sale">
                        <?php echo esc_html($sale); ?>
                    </div>
                <?php endif; ?>
                <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <?php if($team): ?>
                    <div class="team">
                        <i class="behub-icon-team-size"></i><span><?php echo esc_html__('Ideal for team size ', 'behub') . esc_html($team); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($description): ?>
                    <div class="description">
                        <?php echo esc_html($description); ?>
                    </div>
                <?php endif; ?>
                <?php if($price): ?>
                    <div class="price">
                        <label><?php echo esc_html__('from', 'behub'); ?></label>
                        <span><?php echo esc_html($price); ?></span>
                    </div>
                <?php endif; ?>
                <div class="workspace-button">
                    <a class="button-more-link" href="<?php the_permalink() ?>">
                        <span><?php echo esc_html__('learn more', 'behub'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </article><!-- #post-## -->
</div>