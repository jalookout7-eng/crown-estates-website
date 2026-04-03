<?php
/**
 * Template part: CTA Banner
 * Args: title, subtitle, cta_text, cta_url, cta_modal (bool — opens register interest modal instead of link)
 */
$title     = $args['title'] ?? 'Ready to Invest?';
$subtitle  = $args['subtitle'] ?? 'Speak to our team about investment opportunities in Saudi Arabia.';
$cta_text  = $args['cta_text'] ?? 'Talk to Our Team';
$cta_url   = $args['cta_url'] ?? home_url('/contact');
$cta_modal = $args['cta_modal'] ?? false;
?>
<section class="ce-cta">
    <div class="ce-container">
        <h2><?php echo esc_html($title); ?></h2>
        <p><?php echo esc_html($subtitle); ?></p>
        <?php if ($cta_modal): ?>
            <button class="ce-btn ce-btn--gold ce-btn--lg" data-open-modal="register-interest"><?php echo esc_html($cta_text); ?></button>
        <?php else: ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="ce-btn ce-btn--gold ce-btn--lg"><?php echo esc_html($cta_text); ?></a>
        <?php endif; ?>
    </div>
</section>
