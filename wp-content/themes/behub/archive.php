<?php
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
            if (is_post_type_archive('behub_location') || is_tax('behub_location_cat')) {
                get_template_part('template-parts/archive-location');
            }elseif (is_post_type_archive('behub_workspace') || is_tax('behub_workspace_cat')){
                get_template_part('template-parts/archive-workspace');
            }elseif (is_post_type_archive('behub_virtualtour')){
                get_template_part('template-parts/archive-virtualtour');
            }else {
                get_template_part( 'loop' );
            }

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'behub_sidebar' );
get_footer();
