<?php get_header(); ?>
<main class="ce-main">

    <?php get_template_part('template-parts/hero', null, [
        'label'    => 'Saudi Arabia Property Investment',
        'title'    => 'Connecting Investors with Quality Property Opportunities',
        'subtitle' => 'British expat expertise, 20 years of local knowledge, and end-to-end investor support for the Saudi property market.',
        'cta_text' => 'View Opportunities',
        'cta_url'  => home_url('/projects'),
        'bg_image' => 'https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1920&q=80',
    ]); ?>

    <?php get_template_part('template-parts/trust-bar'); ?>

    <!-- FEATURED PROPERTIES -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Featured Opportunities</span>
                <h2>Premium Investment Properties</h2>
                <p class="ce-subtitle">Hand-picked developments offering strong returns and quality living in Saudi Arabia's most sought-after locations.</p>
            </div>

            <div class="ce-grid ce-grid--3">
                <?php
                $featured = new WP_Query([
                    'post_type'      => 'ce_property',
                    'posts_per_page' => 3,
                    'meta_key'       => 'ce_featured',
                    'meta_value'     => '1',
                ]);
                if ($featured->have_posts()):
                    while ($featured->have_posts()): $featured->the_post();
                        get_template_part('template-parts/property-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Properties coming soon.</p>';
                endif;
                ?>
            </div>

            <div style="text-align: center; margin-top: var(--ce-space-xl);">
                <a href="<?php echo esc_url(home_url('/projects')); ?>" class="ce-btn ce-btn--outline">View All Properties</a>
            </div>
        </div>
    </section>

    <!-- WHY INVEST -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Why Saudi Arabia</span>
                <h2>A Market of Opportunity</h2>
                <p class="ce-subtitle">Saudi Arabia's Vision 2030 is transforming the Kingdom into one of the world's most exciting investment destinations.</p>
            </div>
            <div class="ce-grid ce-grid--3">
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#8593;</div>
                    <h3>Market Growth</h3>
                    <p>Saudi Arabia's real estate sector is experiencing unprecedented growth driven by Vision 2030, mega-projects, and regulatory reform.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#9733;</div>
                    <h3>Golden Visa</h3>
                    <p>The Premium Residency programme offers investors long-term residency through qualifying real estate purchases.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#8962;</div>
                    <h3>Freehold Zones</h3>
                    <p>International investors can now own freehold property in designated zones across Riyadh, Jeddah, and NEOM.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Investor Testimonials</span>
                <h2>Trusted by Investors Worldwide</h2>
            </div>
            <div class="ce-grid ce-grid--3">
                <?php
                $testimonials = new WP_Query([
                    'post_type'      => 'ce_testimonial',
                    'posts_per_page' => 3,
                    'meta_key'       => 'ce_testimonial_featured',
                    'meta_value'     => '1',
                ]);
                if ($testimonials->have_posts()):
                    while ($testimonials->have_posts()): $testimonials->the_post();
                        get_template_part('template-parts/testimonial-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Testimonials coming soon.</p>';
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- ABOUT SNIPPET -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-two-col" style="align-items: center;">
                <div>
                    <span class="ce-label">About Crowns Estates</span>
                    <h2 style="margin-top: 8px;">Your Trusted Partner in Saudi Property Investment</h2>
                    <p>As British expats who have lived and worked in Saudi Arabia for over 20 years, we bring unparalleled local expertise to international property investors. Our focus is on sourcing high-quality investment opportunities, providing honest advice, and ensuring that every investor feels confident and supported.</p>
                    <a href="<?php echo esc_url(home_url('/about')); ?>" class="ce-btn ce-btn--outline">Learn More About Us</a>
                </div>
                <div style="background: var(--ce-grey-mid); height: 400px; border-radius: var(--ce-border-radius-lg); display: flex; align-items: center; justify-content: center; color: var(--ce-grey-dark);">
                    Team Photo Placeholder
                </div>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>

</main>
<?php get_footer(); ?>
