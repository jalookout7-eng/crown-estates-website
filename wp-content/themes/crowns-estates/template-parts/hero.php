<?php
/**
 * Template part: Hero Section
 * Args: title, subtitle, label, cta_text, cta_url, bg_image
 */
$title    = $args['title'] ?? '';
$subtitle = $args['subtitle'] ?? '';
$label    = $args['label'] ?? '';
$cta_text = $args['cta_text'] ?? '';
$cta_url  = $args['cta_url'] ?? '#';
$bg_image = $args['bg_image'] ?? '';
?>
<div class="ce-hero"<?php if ($bg_image): ?> style="background-image: url('<?php echo esc_url($bg_image); ?>');"<?php endif; ?>>
    <div class="ce-hero__content">
        <?php if ($label): ?>
            <div class="ce-hero__label"><?php echo esc_html($label); ?></div>
        <?php endif; ?>
        <?php if ($title): ?>
            <h1><?php echo esc_html($title); ?></h1>
        <?php endif; ?>
        <?php if ($subtitle): ?>
            <p><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
        <?php if ($cta_text): ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="ce-btn ce-btn--gold ce-btn--lg"><?php echo esc_html($cta_text); ?></a>
        <?php endif; ?>
    </div>
</div>
