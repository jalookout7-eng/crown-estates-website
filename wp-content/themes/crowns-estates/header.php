<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="ce-header">
    <div class="ce-header__inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="ce-header__logo">
            CROWNS <span>ESTATES</span>
        </a>

        <nav class="ce-header__nav">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="<?php echo is_page('projects') ? 'active' : ''; ?>">Projects</a>
            <a href="<?php echo esc_url(home_url('/how-it-works')); ?>" class="<?php echo is_page('how-it-works') ? 'active' : ''; ?>">How It Works</a>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="<?php echo is_page('blog') || is_single() ? 'active' : ''; ?>">Insights</a>
            <a href="<?php echo esc_url(home_url('/about')); ?>" class="<?php echo is_page('about') ? 'active' : ''; ?>">About</a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="<?php echo is_page('contact') ? 'active' : ''; ?>">Contact</a>
        </nav>

        <div class="ce-header__actions">
            <div class="ce-currency-toggle">
                <span class="ce-currency-toggle__option active">GBP</span>
                <span class="ce-currency-toggle__option">SAR</span>
                <span class="ce-currency-toggle__option">USD</span>
            </div>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="ce-btn ce-btn--gold ce-btn--sm">Enquire</a>
        </div>

        <button class="ce-hamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>
