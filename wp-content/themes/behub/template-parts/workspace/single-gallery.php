<?php
$gallery = get_post_meta(get_the_ID(), 'workspace_gallery', true);
?>

<div class=" workspace-gallery-content-item">
    <article <?php post_class('workspace'); ?>>
        <div class="workspace-inner">
            <?php if (has_post_thumbnail() && ($gallery)) : ?>
                <div class="workspace-slideshow">
                    <div class="inner behub-carousel">
                        <?php foreach ($gallery as $attachment_id => $attachment_url){ ?>
                            <div class="gallery-wrap">
                                <a data-elementor-open-lightbox="yes" <?php echo behub_elementor_get_render_attribute_string('link', $this); ?>
                                   href="<?php echo esc_url($attachment_url); ?>">
                                    <?php echo wp_get_attachment_image($attachment_id,'behub-workspace-grid'); ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </article><!-- #post-## -->
</div>