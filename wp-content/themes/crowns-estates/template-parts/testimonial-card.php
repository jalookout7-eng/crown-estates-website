<?php
/**
 * Template part: Testimonial Card
 * Used on: front-page.php, single-ce_property.php
 */
$post_id    = get_the_ID();
$name       = get_field('ce_client_name', $post_id) ?: 'Investor';
$location   = get_field('ce_client_location', $post_id) ?: '';
$quote      = get_field('ce_quote', $post_id) ?: '';
$rating     = (int) (get_field('ce_rating', $post_id) ?: 5);
$google_url = get_field('ce_google_review_link', $post_id) ?: '';
$initials   = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), explode(' ', $name)));
?>
<div class="ce-testimonial-card" itemscope itemtype="https://schema.org/Review">
    <div class="ce-testimonial-card__stars">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="ce-testimonial-card__star<?php echo $i <= $rating ? '' : ' ce-testimonial-card__star--empty'; ?>">&#9733;</span>
        <?php endfor; ?>
    </div>
    <p class="ce-testimonial-card__quote" itemprop="reviewBody">"<?php echo esc_html($quote); ?>"</p>
    <div class="ce-testimonial-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
        <div class="ce-testimonial-card__avatar"><?php echo esc_html($initials); ?></div>
        <div>
            <div class="ce-testimonial-card__name" itemprop="name"><?php echo esc_html($name); ?></div>
            <?php if ($location): ?>
                <div class="ce-testimonial-card__location"><?php echo esc_html($location); ?></div>
            <?php endif; ?>
            <?php if ($google_url): ?>
                <a href="<?php echo esc_url($google_url); ?>" class="ce-testimonial-card__google" target="_blank" rel="noopener">&#10003; Google Review</a>
            <?php endif; ?>
        </div>
    </div>
    <meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
</div>
