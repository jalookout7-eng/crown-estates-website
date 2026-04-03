<?php get_header(); ?>
<?php the_post(); ?>
<main class="ce-main">
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-blog-layout">
                <article class="ce-blog-layout__main ce-single-post">
                    <header class="ce-single-post__header">
                        <?php $cats = get_the_category(); if ($cats): ?>
                            <span class="ce-post-card__category"><?php echo esc_html($cats[0]->name); ?></span>
                        <?php endif; ?>
                        <h1><?php the_title(); ?></h1>
                        <div class="ce-single-post__meta">
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                            <span>&bull;</span>
                            <span><?php echo ceil(str_word_count(strip_tags(get_the_content())) / 200); ?> min read</span>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="ce-single-post__featured">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="ce-single-post__content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Related posts -->
                    <?php
                    $related = new WP_Query([
                        'post_type'      => 'post',
                        'posts_per_page' => 3,
                        'post__not_in'   => [get_the_ID()],
                        'category__in'   => wp_get_post_categories(get_the_ID()),
                    ]);
                    if ($related->have_posts()): ?>
                        <div class="ce-related-posts">
                            <h3>Related Articles</h3>
                            <div class="ce-grid ce-grid--3">
                                <?php while ($related->have_posts()): $related->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>" class="ce-related-post">
                                        <h4><?php the_title(); ?></h4>
                                        <time><?php echo get_the_date(); ?></time>
                                    </a>
                                <?php endwhile;
                                wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <aside class="ce-blog-layout__sidebar">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>
</main>
<?php get_footer(); ?>
