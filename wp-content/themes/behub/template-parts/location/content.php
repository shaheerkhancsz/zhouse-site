<?php
    $address = get_post_meta(get_the_ID(), 'location_address', true);
    $available_time = get_post_meta(get_the_ID(), 'location_available_time', true);
?>

<article <?php post_class('location'); ?>>
    <div class="location-inner">
        <div class="location-post-thumbnail">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('behub-location-grid'); ?>
            <?php endif; ?>
        </div><!-- .post-thumbnail -->
        <div class="location-content">
            <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <?php if ($address || $available_time): ?>
                <div class="location-meta">
                    <?php if ($address): ?>
                        <div class="address"><?php echo esc_html($address); ?></div>
                    <?php endif; ?>
                    <?php if ($available_time): ?>
                        <div class="time">
                            <span class="sub-title">
                                <?php echo esc_html__('Available Time', 'behub')?>
                            </span>
                            <span class="title"><?php echo esc_html($available_time); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="location-button">
                <a class="button-more-link" href="<?php the_permalink() ?>">
                    <span><?php echo esc_html__('Discover', 'behub'); ?></span>
                </a>
            </div>
        </div>
    </div>
</article><!-- #post-## -->
