<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked in to behub_page action
	 *
	 * @see behub_page_header          - 10
	 * @see behub_page_content         - 20
	 *
	 */
	do_action( 'behub_page' );
	?>
</article><!-- #post-## -->
