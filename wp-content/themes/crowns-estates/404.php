<?php get_header(); ?>
<main class="ce-main">
    <div class="ce-404">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p class="ce-subtitle">The page you're looking for doesn't exist or has been moved.</p>
        <div class="ce-404__actions">
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="ce-btn ce-btn--gold">Browse Properties</a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--outline">Contact Us</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
