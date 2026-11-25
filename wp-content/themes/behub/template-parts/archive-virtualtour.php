<main class="site-main">
    <div class="container clearfix">
        <header class="page-header text-center">
            <?php
            if (behub_is_cmb2_activated()) {
                $maybe_page_title = cmb2_get_option('behub_virtualtour_options', 'archive-title');
                if (empty($maybe_page_title) || false == $maybe_page_title) {
                    $maybe_page_title = esc_html__('Take a tour of Co-working Space in 360 degree', 'behub');
                }
                ?>
                <h4 class="virtualtour-page-title delta"><?php echo esc_html($maybe_page_title); ?></h4>
                <?php
                $maybe_cpt_content = cmb2_get_option('behub_virtualtour_options', 'cpt-content');
                if (false !== $maybe_cpt_content && !empty($maybe_cpt_content)) {
                    ?>
                    <div class="archive-description">
                        <?php echo apply_filters('the_content', $maybe_cpt_content); ?>
                    </div>
                    <?php
                }

            } else {
                the_archive_title('<h1 class="entry-title">', '</h1>');
                the_archive_description('<p class="archive-description">', '</p>');
            }
            ?>
        </header>
        <div id="primary" class="content-area">

            <?php

            echo '<div class="row" data-elementor-columns="3" data-elementor-columns-tablet="2" data-elementor-columns-mobile="1">';

            while (have_posts()) : the_post();

                get_template_part('template-parts/virtualtour/content');

            endwhile;
            echo '</div>';
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
