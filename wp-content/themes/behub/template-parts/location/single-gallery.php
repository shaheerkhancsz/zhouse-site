<?php
$gallery = get_post_meta(get_the_ID(), 'location_gallery', true);
?>

<div class=" location-gallery-content-item">
    <article <?php post_class('location'); ?>>
        <div class="location-inner">
            <?php if (has_post_thumbnail() && empty($gallery)) : ?>
                <div class="location-post-thumbnail">
                    <?php the_post_thumbnail('full'); ?>
                </div><!-- .post-thumbnail -->
            <?php endif; ?>

            <?php if (has_post_thumbnail() && ($gallery)) : ?>
                <div class="location-slideshow">
                    <div class="inner behub-carousel">
                        <div class="gallery-wrap">
                            <?php the_post_thumbnail('behub-location-grid'); ?>
                        </div>
                        <?php foreach ($gallery as $attachment_id => $attachment_url){ ?>
                            <div class="gallery-wrap">
                                <?php echo wp_get_attachment_image($attachment_id,'behub-location-grid'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </article><!-- #post-## -->
</div>