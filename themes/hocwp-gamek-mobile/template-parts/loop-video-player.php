<div class="video-player">
    <?php hocwp_video_play(array('height' => 400, 'width' => 687, 'autoplay' => true)); ?>
</div>
<div class="clear"></div>
<div class="video-desc">
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p class="time"><?php the_category(', '); ?> - <?php echo get_the_date('H:i:s'); ?> <span><?php echo get_the_date('d/m/Y'); ?></span></p>
    <div class="clear"></div>
    <?php the_excerpt(); ?>
    <div class="share margin-top-10">
        <?php hocwp_addthis_toolbox(); ?>
    </div>
</div>