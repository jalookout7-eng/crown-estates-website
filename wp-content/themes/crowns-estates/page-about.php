<?php /* Template Name: About */ ?>
<?php get_header(); ?>
<main class="ce-main">

    <div class="ce-hero ce-hero--small" style="background-image: url('https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1920&q=80');">
        <div class="ce-hero__content">
            <div class="ce-hero__label">Who We Are</div>
            <h1>About Crowns Estates</h1>
            <p>British expertise, Saudi roots. 20 years of trusted property guidance.</p>
        </div>
    </div>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-two-col" style="align-items: center;">
                <div>
                    <span class="ce-label">Our Story</span>
                    <h2 style="margin-top: 8px;">Two Decades of Local Expertise</h2>
                    <p>As British expats who have called Saudi Arabia home for over 20 years, we understand both the opportunities and the concerns that international investors face when entering the Saudi property market.</p>
                    <p>Crowns Estates was founded with a simple mission: to bridge the gap between global investors and Saudi Arabia's rapidly growing real estate sector. We provide honest advice, source high-quality investment opportunities, and ensure that every client feels confident and supported throughout the entire process.</p>
                    <p>We are not just brokers — we are your partners in navigating one of the world's most exciting emerging property markets.</p>
                </div>
                <div style="background: var(--ce-grey-mid); height: 450px; border-radius: var(--ce-border-radius-lg); display: flex; align-items: center; justify-content: center; color: var(--ce-grey-dark);">
                    Team Photo Placeholder
                </div>
            </div>
        </div>
    </section>

    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">What We Stand For</span>
                <h2>Our Core Values</h2>
            </div>
            <div class="ce-grid ce-grid--3">
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#9829;</div>
                    <h3>Trust</h3>
                    <p>We build lasting relationships through transparency, honesty, and integrity. Every recommendation we make is in our client's best interest.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#9672;</div>
                    <h3>Local Knowledge</h3>
                    <p>20 years on the ground gives us insight that no amount of desk research can replicate. We know the developers, the regulations, and the market intimately.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#10004;</div>
                    <h3>Investor-First</h3>
                    <p>Every decision, every recommendation, every step of the process is designed around what's best for you as an investor. Your success is our success.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-stats">
                <div>
                    <div class="ce-stat__number">20+</div>
                    <div class="ce-stat__label">Years in Saudi Arabia</div>
                </div>
                <div>
                    <div class="ce-stat__number">200+</div>
                    <div class="ce-stat__label">Properties Sourced</div>
                </div>
                <div>
                    <div class="ce-stat__number">50+</div>
                    <div class="ce-stat__label">Happy Investors</div>
                </div>
            </div>
        </div>
    </section>

    <section class="ce-cta">
        <div class="ce-container">
            <h2>Ready to Start Your Investment Journey?</h2>
            <p>Let our experience work for you.</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--gold ce-btn--lg">Talk to Our Team</a>
        </div>
    </section>

</main>
<?php get_footer(); ?>
