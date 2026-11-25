<div class="column-item post-style-2">
    <div class="post-inner">
        <?php behub_post_thumbnail('behub-post-grid', true); ?>
        <div class="post-content">
            <div class="entry-header">
                <div class="entry-meta">
                    <?php behub_post_meta();?>
                </div>
                <?php the_title(sprintf('<h5 class="entry-title omega"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h5>'); ?>
            </div>
            <div class="entry-content">
                <?php echo '<div class="more-link-wrap"><a class="more-link" href="' . get_permalink() . '">' . esc_html__('Read more', 'behub') . '</a></div>'; ?>
            </div>
        </div>
    </div>
</div>
