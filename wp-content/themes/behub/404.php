<?php
get_header(); ?>

    <div id="primary" class="content">
        <main id="main" class="site-main">
            <div class="error-404 not-found">
                <div class="error-img404">
                    <img src="<?php echo get_theme_file_uri('assets/images/404/404-image.png') ?>" alt="<?php echo esc_attr__('404 Page', 'behub') ?>">
                </div>
                <div class="page-content">
                    <header class="page-header">
                        <h2 class="sub-title"><?php esc_html_e('Opps! That links is broken.', 'behub'); ?></h2>
                    </header><!-- .page-header -->

                    <div class="error-text">
                        <span><?php esc_html_e("Page does not exist or some other error occured. Go to our ", 'behub') ?></span>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="return-home c-secondary">
                            <?php esc_html_e('Homepage.', 'behub'); ?>
                        </a>
                    </div>
                    <div class="button-error-wrapper">
                        <a href="javascript: history.go(-1)" class="button-link">
                            <span class="button-content">
                                <span class="button-text"><?php echo esc_html__('Homepage', 'behub'); ?></span>
                            </span>
                        </a>
                    </div>
                </div><!-- .page-content -->
            </div><!-- .error-404 -->
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
get_footer();
