<div class="post entry clearfix">
    <div class="thumb">
        <?php hocwp_post_thumbnail(array('width' => 320, 'height' => 160)); ?>
    </div>
    <div class="entry-header">
        <?php hocwp_post_title_link(); ?>
        <p class="meta-date"><?php echo get_the_date(); ?></p>
        <div class="short-description">
            <?php hocwp_entry_summary(); ?>
        </div>
    </div>
</div>