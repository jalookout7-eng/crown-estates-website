<?php
/**
 * Template part: Brochure Gate Modal
 * Included in single-ce_property.php for gated brochure downloads.
 */
?>
<div class="ce-modal" id="modal-brochure-gate" aria-hidden="true">
    <div class="ce-modal__overlay" data-close-modal></div>
    <div class="ce-modal__content">
        <button class="ce-modal__close" data-close-modal aria-label="Close">&times;</button>
        <h3>Download Brochure</h3>
        <p>Enter your details and we'll send the brochure to your email.</p>
        <form class="ce-form" id="form-brochure-gate" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
            <input type="hidden" name="source" value="brochure_download">
            <input type="hidden" name="brochure_url" value="">
            <input type="hidden" name="property_interest" value="">
            <input type="hidden" name="source_url" value="">
            <div class="ce-form__group">
                <label class="ce-form__label" for="bg-name">Full Name *</label>
                <input class="ce-form__input" type="text" id="bg-name" name="name" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="bg-email">Email *</label>
                <input class="ce-form__input" type="email" id="bg-email" name="email" required>
            </div>
            <div class="ce-form__group ce-form__group--checkbox">
                <label>
                    <input type="checkbox" name="gdpr_consent" required>
                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a>.
                </label>
            </div>
            <button type="submit" class="ce-btn ce-btn--gold">Get Brochure</button>
            <div class="ce-form__status" aria-live="polite"></div>
        </form>
    </div>
</div>
