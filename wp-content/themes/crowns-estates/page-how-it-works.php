<?php /* Template Name: How It Works */ ?>
<?php get_header(); ?>
<main class="ce-main">

    <div class="ce-hero ce-hero--small" style="background-image: url('https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1920&q=80');">
        <div class="ce-hero__content">
            <div class="ce-hero__label">Your Investment Journey</div>
            <h1>How It Works</h1>
            <p>A clear, step-by-step guide to investing in Saudi Arabian property with Crowns Estates.</p>
        </div>
    </div>

    <div id="ce-3d-journey" class="ce-3d-canvas ce-3d-canvas--journey" style="height: 400px; position: relative;"></div>

    <!-- STEPS -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">The Process</span>
                <h2>Five Simple Steps to Your Investment</h2>
                <p class="ce-subtitle">We handle the complexity so you can invest with confidence.</p>
            </div>

            <div class="ce-steps" style="max-width: 800px; margin: 0 auto;">
                <div class="ce-step">
                    <div class="ce-step__number"></div>
                    <div>
                        <h3>Discover the Opportunity</h3>
                        <p>Browse our curated selection of investment properties, or tell us what you're looking for. We'll match you with developments that suit your goals, budget, and timeline.</p>
                    </div>
                </div>
                <div class="ce-step">
                    <div class="ce-step__number"></div>
                    <div>
                        <h3>Speak to Our Team</h3>
                        <p>Schedule a consultation with our Saudi-based team. We'll walk you through the market, answer your questions, and help you understand the investment landscape.</p>
                    </div>
                </div>
                <div class="ce-step">
                    <div class="ce-step__number"></div>
                    <div>
                        <h3>Property Selection</h3>
                        <p>Once you've identified the right opportunity, we provide detailed information, 3D renders, floor plans, and financial projections to help you make an informed decision.</p>
                    </div>
                </div>
                <div class="ce-step">
                    <div class="ce-step__number"></div>
                    <div>
                        <h3>Reservation &amp; Legal Process</h3>
                        <p>We guide you through the reservation, contract, and payment process. Our local team coordinates with developers and legal professionals on your behalf.</p>
                    </div>
                </div>
                <div class="ce-step">
                    <div class="ce-step__number"></div>
                    <div>
                        <h3>Completion &amp; Handover</h3>
                        <p>From construction updates to final handover and title deed registration, we're with you until you have the keys. And beyond — we can help with property management and resale.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INVESTMENT CALCULATOR -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Cost Estimator</span>
                <h2>Investment Calculator</h2>
                <p class="ce-subtitle">Get a quick estimate of the total costs associated with your property purchase.</p>
            </div>

            <div class="ce-calculator">
                <h3>Estimate Your Investment Costs</h3>

                <div class="ce-form" style="margin-bottom: var(--ce-space-lg);">
                    <div class="ce-form__group">
                        <label class="ce-form__label">Property Price (&pound;)</label>
                        <input type="number" class="ce-form__input" placeholder="e.g. 200000" value="200000" id="calc-price">
                    </div>
                </div>

                <div class="ce-calculator__row">
                    <span>Property Price</span>
                    <strong id="calc-base">&pound;200,000</strong>
                </div>
                <div class="ce-calculator__row">
                    <span>Registration Fee (2.5%)</span>
                    <span id="calc-reg">&pound;5,000</span>
                </div>
                <div class="ce-calculator__row">
                    <span>VAT (5%)</span>
                    <span id="calc-vat">&pound;10,000</span>
                </div>
                <div class="ce-calculator__row">
                    <span>Agency Fee (2%)</span>
                    <span id="calc-agency">&pound;4,000</span>
                </div>
                <div class="ce-calculator__row">
                    <span>Estimated Total</span>
                    <span id="calc-total">&pound;219,000</span>
                </div>

                <div class="ce-calculator__disclaimer">
                    This calculator provides estimates only and does not constitute financial advice. Actual costs may vary. Please contact us for a detailed breakdown.
                </div>

                <div style="text-align: center; margin-top: var(--ce-space-lg);">
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--gold">Speak to Our Team About This Investment</a>
                </div>
            </div>
        </div>
    </section>

    <!-- GOLDEN VISA -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-two-col" style="align-items: center;">
                <div>
                    <span class="ce-label">Saudi Premium Residency</span>
                    <h2 style="margin-top: 8px;">The Golden Visa Through Property</h2>
                    <p>Saudi Arabia's Premium Residency programme allows international investors to obtain long-term residency through qualifying real estate investments. This opens doors to living, working, and doing business in the Kingdom.</p>
                    <p><strong>Key benefits include:</strong></p>
                    <ul class="ce-page-content" style="margin-top: 0;">
                        <li>Indefinite residency for you and your family</li>
                        <li>Freedom to own property and businesses</li>
                        <li>Access to Saudi banking and financial services</li>
                        <li>No requirement for a Saudi sponsor</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--outline">Enquire About the Golden Visa</a>
                </div>
                <div style="background: var(--ce-grey-light); height: 400px; border-radius: var(--ce-border-radius-lg); display: flex; align-items: center; justify-content: center; color: var(--ce-grey-dark);">
                    Golden Visa Infographic Placeholder
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Common Questions</span>
                <h2>Frequently Asked Questions</h2>
            </div>

            <div class="ce-faq">
                <div class="ce-faq__item">
                    <button class="ce-faq__question">Can foreign nationals buy property in Saudi Arabia?</button>
                    <div class="ce-faq__answer">
                        <p>Yes. Recent reforms allow international investors to purchase freehold property in designated zones across Saudi Arabia, including Riyadh, Jeddah, and NEOM. Crowns Estates guides you through the eligibility and process.</p>
                    </div>
                </div>
                <div class="ce-faq__item">
                    <button class="ce-faq__question">What is the minimum investment for the Premium Residency (Golden Visa)?</button>
                    <div class="ce-faq__answer">
                        <p>The qualifying investment amount varies and is subject to government regulation. Contact our team for the latest requirements and eligible property types.</p>
                    </div>
                </div>
                <div class="ce-faq__item">
                    <button class="ce-faq__question">How do I pay for a property from the UK?</button>
                    <div class="ce-faq__answer">
                        <p>We guide you through international payment options, including bank transfers and approved payment plans offered by developers. Many developments offer flexible instalment plans during construction.</p>
                    </div>
                </div>
                <div class="ce-faq__item">
                    <button class="ce-faq__question">What are the risks of off-plan property investment?</button>
                    <div class="ce-faq__answer">
                        <p>Off-plan investments carry risks including construction delays and market fluctuations. We mitigate these by working only with established, government-approved developers and providing regular construction updates.</p>
                    </div>
                </div>
                <div class="ce-faq__item">
                    <button class="ce-faq__question">Do I need to visit Saudi Arabia to buy property?</button>
                    <div class="ce-faq__answer">
                        <p>No. The entire process can be managed remotely with our team handling on-the-ground coordination. However, we welcome investors who wish to visit and can arrange property tours.</p>
                    </div>
                </div>
                <div class="ce-faq__item">
                    <button class="ce-faq__question">What ongoing support do you provide after purchase?</button>
                    <div class="ce-faq__answer">
                        <p>We offer continued support including construction progress updates, handover coordination, property management referrals, and resale assistance. Our relationship doesn't end at the sale.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="ce-cta">
        <div class="ce-container">
            <h2>Have More Questions?</h2>
            <p>Our team is ready to help you navigate your investment journey.</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--gold ce-btn--lg">Get in Touch</a>
        </div>
    </section>

</main>
<?php get_footer(); ?>
