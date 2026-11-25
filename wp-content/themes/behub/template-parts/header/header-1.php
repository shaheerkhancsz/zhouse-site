<header id="masthead" class="site-header header-1" role="banner">
    <div class="header-container">
        <div class="container header-main">
            <div class="header-left">
                <?php
                behub_site_branding();
                ?>
                <?php behub_mobile_nav_button(); ?>
            </div>
            <div class="header-center">
                <?php behub_primary_navigation(); ?>
            </div>
            <div class="header-right desktop-hide-down">
                <div class="header-group-action">
                    <?php
                    behub_header_account();
                    ?>
                </div>
            </div>
        </div>
    </div>
</header><!-- #masthead -->
