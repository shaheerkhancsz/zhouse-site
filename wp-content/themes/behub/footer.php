
		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'behub_before_footer' );
    if (behub_is_elementor_activated() && function_exists('hfe_init') && (hfe_footer_enabled() || hfe_is_before_footer_enabled())) {
        do_action('hfe_footer_before');
        do_action('hfe_footer');
    } else {
        ?>

        <footer id="colophon" class="site-footer" role="contentinfo">
            <?php
            /**
             * Functions hooked in to behub_footer action
             *
             * @see behub_footer_default - 20
             *
             *
             */
            do_action('behub_footer');

            ?>

        </footer><!-- #colophon -->

        <?php
    }

		/**
		 * Functions hooked in to behub_after_footer action
		 * @see behub_sticky_single_add_to_cart 	- 999 - woo
		 */
		do_action( 'behub_after_footer' );
	?>

</div><!-- #page -->

<?php

/**
 * Functions hooked in to wp_footer action
 * @see behub_template_account_dropdown 	- 1
 * @see behub_mobile_nav - 1
 */

wp_footer();
?>

</body>
</html>
