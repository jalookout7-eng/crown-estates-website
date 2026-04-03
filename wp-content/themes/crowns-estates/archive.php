<?php get_header(); ?>
<main class="ce-main">
    <?php get_template_part('template-parts/hero', null, [
        'title' => 'Insights & Guides',
        'subtitle' => 'Expert articles on Saudi Arabian property investment, market trends, and investor guides.',
        'label' => 'Blog',
    ]); ?>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-blog-layout">
                <div class="ce-blog-layout__main">
                    <?php if (have_posts()): ?>
                        <div class="ce-grid ce-grid--2">
                            <?php while (have_posts()): the_post(); ?>
                                <article class="ce-post-card">
                                    <?php if (has_post_thumbnail()): ?>
                                        <a href="<?php the_permalink(); ?>" class="ce-post-card__image" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')); ?>');"></a>
                                    <?php endif; ?>
                                    <div class="ce-post-card__body">
                                        <?php $cats = get_the_category(); if ($cats): ?>
                                            <span class="ce-post-card__category"><?php echo esc_html($cats[0]->name); ?></span>
                                        <?php endif; ?>
                                        <h3 class="ce-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <p class="ce-post-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                        <div class="ce-post-card__meta">
                                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                            <a href="<?php the_permalink(); ?>" class="ce-post-card__link">Read More &rarr;</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        <div class="ce-pagination">
                            <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
                        </div>
                    <?php else: ?>
                        <p>No articles yet. Check back soon.</p>
                    <?php endif; ?>
                </div>
                <aside class="ce-blog-layout__sidebar">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
