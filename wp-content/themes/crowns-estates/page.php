<?php get_header(); ?>
<main class="ce-main">
    <div class="ce-hero ce-hero--small" style="background-image: url('https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1920&q=80');">
        <div class="ce-hero__content">
            <h1><?php the_title(); ?></h1>
        </div>
    </div>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-page-content">
                <?php the_content(); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
