<?php
$description = get_post_meta(get_the_ID(), 'workspace_description', true);
$team = get_post_meta(get_the_ID(), 'workspace_team', true);
$price = get_post_meta(get_the_ID(), 'workspace_price', true);
$sale = get_post_meta(get_the_ID(), 'workspace_sale', true);
$gallery = get_post_meta(get_the_ID(), 'workspace_gallery', true);
?>

<div id ="tab-hover-content-<?php echo esc_attr($count); ?>" class="workspace-style-5 workspace-tab-content-item">
    <article <?php post_class('workspace'); ?>>
        <div class="workspace-inner">

            <?php if (has_post_thumbnail() && empty($gallery)) : ?>
                <div class="workspace-post-thumbnail">
                    <?php the_post_thumbnail('behub-workspace-grid'); ?>
                </div><!-- .post-thumbnail -->
            <?php endif; ?>

            <?php if (has_post_thumbnail() && ($gallery)) : ?>
                <div class="workspace-slideshow">
                    <div class="inner behub-carousel">
                        <div class="gallery-wrap">
                            <?php the_post_thumbnail('behub-workspace-grid'); ?>
                        </div>
                        <?php foreach ($gallery as $attachment_id => $attachment_url){ ?>
                            <div class="gallery-wrap">
                                <?php echo wp_get_attachment_image($attachment_id,'behub-workspace-grid'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="workspace-content">
                <div class="top">
                    <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if($price): ?>
                        <div class="price">
                            <label><?php echo esc_html__('from', 'behub'); ?></label>
                            <span><?php echo esc_html($price); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="bottom">
                    <?php if($description): ?>
                        <div class="description">
                            <?php echo esc_html($description); ?>
                        </div>
                    <?php endif; ?>
                    <div class="more-link-wrap">
                        <a class="more-link" href="<?php the_permalink() ?>">
                            <span><?php echo esc_html__('learn more', 'behub'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article><!-- #post-## -->
</div>