<?php get_header(); ?>
<main class="ce-main">

    <!-- HERO -->
    <div class="ce-hero" style="background-image: url('https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1920&q=80');">
        <div class="ce-hero__content">
            <div class="ce-hero__label">Saudi Arabia Property Investment</div>
            <h1>Connecting Investors with Quality Property Opportunities</h1>
            <p>British expat expertise, 20 years of local knowledge, and end-to-end investor support for the Saudi property market.</p>
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="ce-btn ce-btn--gold ce-btn--lg">View Opportunities</a>
        </div>
    </div>

    <!-- TRUST BAR -->
    <div class="ce-trust-bar">
        <div class="ce-container">
            <div class="ce-trust-bar__inner">
                <div class="ce-trust-bar__item">
                    <span class="ce-trust-bar__icon">&#9670;</span>
                    20 Years in Saudi Arabia
                </div>
                <div class="ce-trust-bar__divider"></div>
                <div class="ce-trust-bar__item">
                    <span class="ce-trust-bar__icon">&#9670;</span>
                    British Expat Expertise
                </div>
                <div class="ce-trust-bar__divider"></div>
                <div class="ce-trust-bar__item">
                    <span class="ce-trust-bar__icon">&#9670;</span>
                    End-to-End Investor Support
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURED PROPERTIES -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Featured Opportunities</span>
                <h2>Premium Investment Properties</h2>
                <p class="ce-subtitle">Hand-picked developments offering strong returns and quality living in Saudi Arabia's most sought-after locations.</p>
            </div>

            <div class="ce-grid ce-grid--3">
                <!-- Property Card 1 -->
                <div class="ce-property-card">
                    <div class="ce-property-card__image" style="background-image: url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80');">
                        <span class="ce-property-card__badge">Riyadh</span>
                        <span class="ce-property-card__status ce-property-card__status--off-plan">Off-Plan</span>
                    </div>
                    <div class="ce-property-card__body">
                        <h3 class="ce-property-card__title">ROSHN Sedra Residences</h3>
                        <div class="ce-property-card__type">Apartment &bull; Freehold</div>
                        <div class="ce-property-card__meta">
                            <div class="ce-property-card__meta-item"><strong>2-3</strong> Beds</div>
                            <div class="ce-property-card__meta-item"><strong>125</strong> Sq.M.</div>
                            <div class="ce-property-card__meta-item"><strong>2027</strong> Completion</div>
                        </div>
                        <div class="ce-property-card__footer">
                            <div class="ce-property-card__price" data-price="750000" data-currency="SAR">&pound;160,000 <small>from</small></div>
                            <a href="#" class="ce-btn ce-btn--outline ce-btn--sm">View</a>
                        </div>
                    </div>
                </div>

                <!-- Property Card 2 -->
                <div class="ce-property-card">
                    <div class="ce-property-card__image" style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80');">
                        <span class="ce-property-card__badge">Jeddah</span>
                        <span class="ce-property-card__status ce-property-card__status--construction">Under Construction</span>
                    </div>
                    <div class="ce-property-card__body">
                        <h3 class="ce-property-card__title">Dar Al Arkan Jeddah Waterfront</h3>
                        <div class="ce-property-card__type">Villa &bull; Freehold</div>
                        <div class="ce-property-card__meta">
                            <div class="ce-property-card__meta-item"><strong>4-5</strong> Beds</div>
                            <div class="ce-property-card__meta-item"><strong>320</strong> Sq.M.</div>
                            <div class="ce-property-card__meta-item"><strong>2026</strong> Completion</div>
                        </div>
                        <div class="ce-property-card__footer">
                            <div class="ce-property-card__price" data-price="2100000" data-currency="SAR">&pound;448,000 <small>from</small></div>
                            <a href="#" class="ce-btn ce-btn--outline ce-btn--sm">View</a>
                        </div>
                    </div>
                </div>

                <!-- Property Card 3 -->
                <div class="ce-property-card">
                    <div class="ce-property-card__image" style="background-image: url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80');">
                        <span class="ce-property-card__badge">NEOM</span>
                        <span class="ce-property-card__status ce-property-card__status--off-plan">Off-Plan</span>
                    </div>
                    <div class="ce-property-card__body">
                        <h3 class="ce-property-card__title">NEOM The Line Residences</h3>
                        <div class="ce-property-card__type">Apartment &bull; Freehold</div>
                        <div class="ce-property-card__meta">
                            <div class="ce-property-card__meta-item"><strong>1-3</strong> Beds</div>
                            <div class="ce-property-card__meta-item"><strong>95</strong> Sq.M.</div>
                            <div class="ce-property-card__meta-item"><strong>2029</strong> Completion</div>
                        </div>
                        <div class="ce-property-card__footer">
                            <div class="ce-property-card__price" data-price="1200000" data-currency="SAR">&pound;256,000 <small>from</small></div>
                            <a href="#" class="ce-btn ce-btn--outline ce-btn--sm">View</a>
                        </div>
                    </div>
                </div>
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
                <div class="ce-testimonial-card">
                    <div class="ce-testimonial-card__stars">
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                    </div>
                    <p class="ce-testimonial-card__quote">"Crowns Estates made investing in Saudi property straightforward and stress-free. Their local knowledge is unmatched, and I felt supported at every step."</p>
                    <div class="ce-testimonial-card__author">
                        <div class="ce-testimonial-card__avatar">JT</div>
                        <div>
                            <div class="ce-testimonial-card__name">James T.</div>
                            <div class="ce-testimonial-card__location">London, UK</div>
                            <a href="#" class="ce-testimonial-card__google">&#10003; Google Review</a>
                        </div>
                    </div>
                </div>

                <div class="ce-testimonial-card">
                    <div class="ce-testimonial-card__stars">
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                    </div>
                    <p class="ce-testimonial-card__quote">"As a first-time overseas investor, I had many concerns. The team at Crowns Estates addressed every question with patience and genuine expertise."</p>
                    <div class="ce-testimonial-card__author">
                        <div class="ce-testimonial-card__avatar">SM</div>
                        <div>
                            <div class="ce-testimonial-card__name">Sarah M.</div>
                            <div class="ce-testimonial-card__location">Manchester, UK</div>
                            <a href="#" class="ce-testimonial-card__google">&#10003; Google Review</a>
                        </div>
                    </div>
                </div>

                <div class="ce-testimonial-card">
                    <div class="ce-testimonial-card__stars">
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                        <span class="ce-testimonial-card__star">&#9733;</span>
                    </div>
                    <p class="ce-testimonial-card__quote">"Professional, transparent, and incredibly knowledgeable about the Saudi market. I've now purchased two properties through them and wouldn't go anywhere else."</p>
                    <div class="ce-testimonial-card__author">
                        <div class="ce-testimonial-card__avatar">AK</div>
                        <div>
                            <div class="ce-testimonial-card__name">Ahmed K.</div>
                            <div class="ce-testimonial-card__location">Dubai, UAE</div>
                            <a href="#" class="ce-testimonial-card__google">&#10003; Google Review</a>
                        </div>
                    </div>
                </div>
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

    <!-- CTA BANNER -->
    <section class="ce-cta">
        <div class="ce-container">
            <h2>Ready to Invest?</h2>
            <p>Speak to our team about investment opportunities in Saudi Arabia.</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--gold ce-btn--lg">Talk to Our Team</a>
        </div>
    </section>

</main>
<?php get_footer(); ?>
