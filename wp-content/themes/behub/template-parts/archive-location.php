<main class="site-main">
    <div class="container clearfix">
        <?php if (apply_filters('behub_page_title', true)) : ?>
            <header class="page-header text-center">
                <?php
                the_archive_title('<h1 class="entry-title">', '</h1>');
                the_archive_description('<p class="archive-description">', '</p>');
                ?>
            </header>
        <?php endif; ?>
        <div id="primary" class="content-area">
            <div class="navigation-location">
                <?php
                $class = is_post_type_archive('behub_location') ? 'active' : '';
                ?>
                <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url(get_post_type_archive_link('behub_location')); ?>"><?php echo esc_html__('All Location', 'behub'); ?></a>
                <?php
                $taxonomy = 'behub_location_cat';
                $terms    = $terms = get_terms([
                    'taxonomy'   => 'behub_location_cat',
                    'hide_empty' => false,
                ]);
                foreach ($terms as $term) {
                    $item_class = is_tax($taxonomy, $term->slug) ? 'active' : '';
                    ?>
                    <a class="<?php echo esc_attr($item_class); ?>" href="<?php echo esc_url(get_term_link($term->slug, $taxonomy)) ?>"><?php echo esc_html($term->name); ?></a>
                    <?php
                }
                ?>
            </div>
            <div class="location-wrap">
                <?php
                $column = behub_get_theme_option('location_archive_column', 2);
                echo '<div class="row" data-elementor-columns="' . esc_attr($column) . '" data-elementor-columns-tablet="2" data-elementor-columns-mobile="1">';

                while (have_posts()) : the_post();
                    ?>
                    <div class="column-item location-entries">
                        <?php get_template_part('template-parts/location/content'); ?>
                    </div>
                <?php
                endwhile;
                ?>
            </div>
        </div>
        <?php
        $args = array(
            'type'      => 'list',
            'next_text' => '<i class="behub-icon-angle-right"></i>',
            'prev_text' => '<i class="behub-icon-angle-left"></i>',
        );

        the_posts_pagination($args);
        ?>
    </div>
    </div>
</main>
