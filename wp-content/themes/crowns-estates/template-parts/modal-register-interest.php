<?php
/**
 * Template part: Register Interest Modal
 * Included in footer.php. Hidden by default, opened via JS.
 */
?>
<div class="ce-modal" id="modal-register-interest" aria-hidden="true">
    <div class="ce-modal__overlay" data-close-modal></div>
    <div class="ce-modal__content">
        <button class="ce-modal__close" data-close-modal aria-label="Close">&times;</button>
        <h3>Register Your Interest</h3>
        <p>Complete the form below and our team will be in touch within 24 hours.</p>
        <form class="ce-form" id="form-register-interest" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
            <input type="hidden" name="source" value="register_interest">
            <input type="hidden" name="source_url" value="">
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-name">Full Name *</label>
                <input class="ce-form__input" type="text" id="ri-name" name="name" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-email">Email *</label>
                <input class="ce-form__input" type="email" id="ri-email" name="email" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-phone">Phone</label>
                <input class="ce-form__input" type="tel" id="ri-phone" name="phone">
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-message">Message</label>
                <textarea class="ce-form__input" id="ri-message" name="message" rows="3"></textarea>
            </div>
            <div class="ce-form__group ce-form__group--checkbox">
                <label>
                    <input type="checkbox" name="gdpr_consent" required>
                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a> and consent to Crowns Estates processing my data to respond to my enquiry.
                </label>
            </div>
            <button type="submit" class="ce-btn ce-btn--gold">Send Enquiry</button>
            <div class="ce-form__status" aria-live="polite"></div>
        </form>
    </div>
</div>
