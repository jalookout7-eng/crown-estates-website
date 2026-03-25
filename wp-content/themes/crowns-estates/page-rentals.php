<?php /* Template Name: Rentals */ ?>
<?php get_header(); ?>
<main class="ce-main">

    <div class="ce-hero ce-hero--small" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80');">
        <div class="ce-hero__content">
            <h1>Rental Properties</h1>
        </div>
    </div>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-coming-soon">
                <div class="ce-coming-soon__badge">Coming Soon</div>
                <h2 style="margin-top: var(--ce-space-lg);">Rental Properties</h2>
                <p class="ce-subtitle" style="max-width: 600px; margin: var(--ce-space-md) auto var(--ce-space-xl);">
                    We're preparing a curated selection of rental properties across Saudi Arabia. Register your interest below to be notified when our rental listings go live.
                </p>

                <form class="ce-form" style="max-width: 400px; margin: 0 auto;">
                    <div class="ce-form__group">
                        <input type="text" class="ce-form__input" placeholder="Your name">
                    </div>
                    <div class="ce-form__group">
                        <input type="email" class="ce-form__input" placeholder="Your email address">
                    </div>
                    <button type="submit" class="ce-btn ce-btn--gold" style="width: 100%;">Notify Me</button>
                </form>

                <div style="margin-top: var(--ce-space-xl);">
                    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="ce-btn ce-btn--outline">Browse Investment Properties</a>
                </div>
            </div>
        </div>
    </section>

</main>
<?php get_footer(); ?>
