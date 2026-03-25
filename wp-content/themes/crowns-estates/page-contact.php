<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>
<main class="ce-main">

    <div class="ce-hero ce-hero--small" style="background-image: url('https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1920&q=80');">
        <div class="ce-hero__content">
            <div class="ce-hero__label">Get in Touch</div>
            <h1>Contact Us</h1>
            <p>Ready to invest? Have a question? We're here to help.</p>
        </div>
    </div>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-two-col">
                <div>
                    <h2>Send Us a Message</h2>
                    <p>Fill out the form below and our team will get back to you within 24 hours.</p>

                    <form class="ce-form" style="margin-top: var(--ce-space-lg);">
                        <div class="ce-form__group">
                            <label class="ce-form__label" for="contact-name">Full Name *</label>
                            <input type="text" id="contact-name" class="ce-form__input" placeholder="Your full name" required>
                        </div>
                        <div class="ce-form__group">
                            <label class="ce-form__label" for="contact-email">Email Address *</label>
                            <input type="email" id="contact-email" class="ce-form__input" placeholder="your@email.com" required>
                        </div>
                        <div class="ce-form__group">
                            <label class="ce-form__label" for="contact-phone">Phone Number</label>
                            <input type="tel" id="contact-phone" class="ce-form__input" placeholder="+44 7000 000000">
                        </div>
                        <div class="ce-form__group">
                            <label class="ce-form__label" for="contact-interest">I'm Interested In</label>
                            <select id="contact-interest" class="ce-form__select">
                                <option value="">Select an option</option>
                                <option value="buying">Buying Investment Property</option>
                                <option value="golden-visa">Golden Visa / Premium Residency</option>
                                <option value="specific-project">A Specific Project</option>
                                <option value="general">General Enquiry</option>
                            </select>
                        </div>
                        <div class="ce-form__group">
                            <label class="ce-form__label" for="contact-message">Message</label>
                            <textarea id="contact-message" class="ce-form__textarea" placeholder="Tell us about your investment goals..."></textarea>
                        </div>
                        <div class="ce-form__checkbox">
                            <input type="checkbox" id="contact-gdpr" required>
                            <label for="contact-gdpr">I agree to the <a href="#">Privacy Policy</a> and consent to Crowns Estates processing my data to respond to my enquiry.</label>
                        </div>
                        <button type="submit" class="ce-btn ce-btn--gold">Send Message</button>
                    </form>
                </div>

                <div>
                    <div style="background: var(--ce-grey-lightest); padding: var(--ce-space-xl); border-radius: var(--ce-border-radius-lg);">
                        <h3>Contact Information</h3>
                        <div style="margin-top: var(--ce-space-lg); display: flex; flex-direction: column; gap: var(--ce-space-lg);">
                            <div>
                                <div class="ce-label" style="margin-bottom: 4px;">Email</div>
                                <a href="mailto:info@crownsestates.co.uk" style="font-size: 1.0625rem; font-weight: 500;">info@crownsestates.co.uk</a>
                            </div>
                            <div>
                                <div class="ce-label" style="margin-bottom: 4px;">WhatsApp</div>
                                <a href="#" style="font-size: 1.0625rem; font-weight: 500;">Chat with us on WhatsApp</a>
                            </div>
                            <div>
                                <div class="ce-label" style="margin-bottom: 4px;">Office</div>
                                <p style="font-size: 1.0625rem; margin-bottom: 0;">United Kingdom<br><span style="color: var(--ce-grey-dark); font-size: 0.875rem;">Registered in England & Wales</span></p>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: var(--ce-space-lg); background: var(--ce-grey-light); height: 300px; border-radius: var(--ce-border-radius-lg); display: flex; align-items: center; justify-content: center; color: var(--ce-grey-dark);">
                        Google Maps Placeholder
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php get_footer(); ?>
