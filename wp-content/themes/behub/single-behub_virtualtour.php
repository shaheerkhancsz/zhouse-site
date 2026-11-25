<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="profile" href="//gmpg.org/xfn/11">
    <?php
    wp_head();
    ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page">
    <?php
    $mainid     = get_the_ID();
    $query_args = [
        'post_type'           => 'behub_virtualtour',
        'ignore_sticky_posts' => 1,
        'post_status'         => 'publish',
        //        'post__not_in'        => [$mainid],
        'posts_per_page'      => -1
    ];
    $query      = new WP_Query($query_args);
    $images     = [];
    $count      = 0;
    $index      = 0;
    ?>
    <div class="viewer-nav">
        <a href="<?php echo esc_url(get_post_type_archive_link('behub_virtualtour'));?>" class="go-back" target="_self"><i class="behub-icon-chevron-circle-left"></i><?php esc_html_e('Back', 'behub'); ?>
        </a>
        <div class="viewer-nav-inner">
            <?php

            if ($query->found_posts) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $image = get_post_meta(get_the_ID(), 'virtualtour_image', true);
                    if ($image) {
                        $images[] = $image;
                        $class    = $mainid == get_the_ID() ? 'viewer-button active' : 'viewer-button';
                        $index    = $mainid == get_the_ID() ? $count : $index;
                        ?>
                        <a class="<?php echo esc_attr($class); ?>" href="<?php the_permalink(); ?>" id="viewer-<?php echo esc_attr($count++); ?>"><?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail');
                            }
                            the_title();
                            ?></a>
                        <?php
                    }

                }
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <div id="container" class="behub-panorama" data-index="<?php echo esc_attr($index); ?>" data-settings=<?php echo wp_json_encode($images); ?>></div>
</div>

<?php
wp_footer();
?>
</body>
</html>

