<footer class="ce-footer">
    <div class="ce-container">
        <div class="ce-footer__grid">
            <div class="ce-footer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="ce-header__logo">
                    CROWNS <span>ESTATES</span>
                </a>
                <p>Connecting UK and global investors with quality property opportunities in Saudi Arabia. 20 years of local expertise and trusted guidance.</p>
            </div>

            <div>
                <h4>Quick Links</h4>
                <div class="ce-footer__links">
                    <a href="<?php echo esc_url(home_url('/projects')); ?>">Projects</a>
                    <a href="<?php echo esc_url(home_url('/how-it-works')); ?>">How It Works</a>
                    <a href="<?php echo esc_url(home_url('/blog')); ?>">Insights</a>
                    <a href="<?php echo esc_url(home_url('/about')); ?>">About Us</a>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a>
                </div>
            </div>

            <div>
                <h4>Contact</h4>
                <div class="ce-footer__links">
                    <a href="mailto:info@crownsestates.co.uk">info@crownsestates.co.uk</a>
                    <?php
                    $wa = function_exists('get_field') ? get_field('ce_whatsapp_number', 'option') : '';
                    if ($wa): ?>
                        <a href="https://wa.me/<?php echo esc_attr($wa); ?>" target="_blank" rel="noopener">WhatsApp</a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4>Legal</h4>
                <div class="ce-footer__links">
                    <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                    <a href="<?php echo esc_url(home_url('/terms')); ?>">Terms of Service</a>
                    <a href="<?php echo esc_url(home_url('/disclaimer')); ?>">Disclaimer</a>
                    <a href="<?php echo esc_url(home_url('/cookie-policy')); ?>">Cookie Policy</a>
                </div>
            </div>
        </div>

        <div class="ce-footer__disclaimer">
            Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions.
        </div>

        <div class="ce-footer__bottom">
            <span>&copy; <?php echo date('Y'); ?> Crowns Estates. All rights reserved.</span>
            <div class="ce-footer__legal">
                <span>A UK-registered company</span>
            </div>
        </div>
    </div>
</footer>

<?php get_template_part('template-parts/whatsapp-button'); ?>
<?php get_template_part('template-parts/modal-register-interest'); ?>

<?php wp_footer(); ?>
</body>
</html>
