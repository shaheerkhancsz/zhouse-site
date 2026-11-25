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

            <?php
            $column = behub_get_theme_option('workspace_archive_column', 1);
            $style = behub_get_theme_option('workspace_archive_style', 'style-1');
            echo '<div class="row" data-elementor-columns="' . esc_attr($column) . '" data-elementor-columns-tablet="1" data-elementor-columns-mobile="1">';

            while (have_posts()) : the_post();

                get_template_part('template-parts/workspace/item-workspace-' . $style);

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
