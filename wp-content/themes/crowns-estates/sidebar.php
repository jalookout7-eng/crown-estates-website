<?php
/**
 * Blog sidebar: search, categories, recent posts, CTA card.
 */
?>
<div class="ce-sidebar">
    <!-- Search -->
    <div class="ce-sidebar__widget">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input class="ce-form__input" type="search" placeholder="Search articles..." name="s" value="<?php echo get_search_query(); ?>">
        </form>
    </div>

    <!-- Categories -->
    <div class="ce-sidebar__widget">
        <h3 class="ce-widget__title">Categories</h3>
        <ul class="ce-sidebar__list">
            <?php wp_list_categories(['title_li' => '', 'show_count' => true]); ?>
        </ul>
    </div>

    <!-- Recent Posts -->
    <div class="ce-sidebar__widget">
        <h3 class="ce-widget__title">Recent Posts</h3>
        <ul class="ce-sidebar__list">
            <?php
            $recent = new WP_Query(['post_type' => 'post', 'posts_per_page' => 5]);
            while ($recent->have_posts()): $recent->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </ul>
    </div>

    <!-- CTA Card -->
    <div class="ce-sidebar__cta">
        <h3>Ready to invest?</h3>
        <p>Speak to our team about Saudi property opportunities.</p>
        <button class="ce-btn ce-btn--gold" data-open-modal="register-interest">Register Interest</button>
    </div>
</div>
