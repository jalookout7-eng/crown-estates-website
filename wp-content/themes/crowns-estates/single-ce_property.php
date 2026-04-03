<?php get_header(); ?>
<?php the_post(); ?>
<?php
$post_id     = get_the_ID();
$developer   = get_field('ce_developer') ?: '';
$badge       = get_field('ce_developer_badge') ?: 'none';
$type        = get_field('ce_property_type') ?: '';
$status      = get_field('ce_status') ?: '';
$completion  = get_field('ce_completion_date');
$bedrooms    = get_field('ce_bedrooms') ?: '';
$size        = get_field('ce_size_sqm') ?: '';
$freehold    = get_field('ce_is_freehold');
$description = get_field('ce_full_description') ?: '';
$gallery     = get_field('ce_gallery') ?: [];
$brochure    = get_field('ce_brochure_pdf') ?: '';
$gated       = get_field('ce_brochure_gated');
$map_embed   = get_field('ce_map_embed') ?: '';
$city_terms  = get_the_terms($post_id, 'ce_city');
$city_name   = $city_terms ? $city_terms[0]->name : '';

$status_labels = ['off-plan' => 'Off-Plan', 'under-construction' => 'Under Construction', 'ready' => 'Ready'];
$status_colors = ['off-plan' => '#3B82F6', 'under-construction' => '#F59E0B', 'ready' => '#10B981'];
$type_labels   = ['apartment' => 'Apartment', 'villa' => 'Villa', 'commercial' => 'Commercial'];
?>

<main class="ce-main">
    <!-- Gallery -->
    <section class="ce-property-gallery">
        <?php if ($gallery): ?>
            <div class="ce-property-gallery__hero" style="background-image: url('<?php echo esc_url($gallery[0]['url']); ?>');"></div>
            <?php if (count($gallery) > 1): ?>
                <div class="ce-property-gallery__thumbs">
                    <?php foreach (array_slice($gallery, 1, 4) as $img): ?>
                        <div class="ce-property-gallery__thumb" style="background-image: url('<?php echo esc_url($img['sizes']['medium_large'] ?? $img['url']); ?>');"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="ce-property-gallery__hero" style="background: var(--ce-grey-light); display: flex; align-items: center; justify-content: center; min-height: 400px; color: var(--ce-grey-dark);">Image Coming Soon</div>
        <?php endif; ?>
    </section>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-property-detail">
                <!-- Left column: info -->
                <div class="ce-property-detail__main">
                    <h1><?php the_title(); ?></h1>

                    <div class="ce-property-detail__badges">
                        <?php if ($city_name): ?><span class="ce-property-card__badge"><?php echo esc_html($city_name); ?></span><?php endif; ?>
                        <?php if ($status): ?><span class="ce-property-card__status" style="background: <?php echo esc_attr($status_colors[$status] ?? '#666'); ?>;"><?php echo esc_html($status_labels[$status] ?? $status); ?></span><?php endif; ?>
                        <?php if ($freehold): ?><span class="ce-badge ce-badge--freehold">Freehold</span><?php endif; ?>
                    </div>

                    <div class="ce-property-detail__developer">
                        <?php echo esc_html($developer); ?>
                        <?php if ($badge !== 'none'): ?>
                            <?php get_template_part('template-parts/developer-badge', null, ['badge' => $badge]); ?>
                        <?php endif; ?>
                    </div>

                    <div class="ce-property-detail__price">
                        <?php echo ce_display_price($post_id); ?>
                    </div>

                    <!-- Specs table -->
                    <table class="ce-property-specs">
                        <?php if ($type): ?><tr><td>Type</td><td><?php echo esc_html($type_labels[$type] ?? $type); ?></td></tr><?php endif; ?>
                        <?php if ($bedrooms): ?><tr><td>Bedrooms</td><td><?php echo esc_html($bedrooms); ?></td></tr><?php endif; ?>
                        <?php if ($size): ?><tr><td>Size</td><td><?php echo esc_html($size); ?> Sq.M.</td></tr><?php endif; ?>
                        <?php if ($completion): ?><tr><td>Completion</td><td><?php echo esc_html(date('F Y', strtotime($completion))); ?></td></tr><?php endif; ?>
                        <?php if ($freehold !== null): ?><tr><td>Ownership</td><td><?php echo $freehold ? 'Freehold' : 'Leasehold'; ?></td></tr><?php endif; ?>
                    </table>

                    <?php if ($description): ?>
                        <div class="ce-property-detail__description">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($brochure): ?>
                        <?php if ($gated): ?>
                            <button class="ce-btn ce-btn--outline" data-open-modal="brochure-gate" data-property-name="<?php echo esc_attr(get_the_title()); ?>" data-brochure-url="<?php echo esc_url($brochure); ?>">
                                Download Brochure
                            </button>
                        <?php else: ?>
                            <a href="<?php echo esc_url($brochure); ?>" class="ce-btn ce-btn--outline" target="_blank" rel="noopener">Download Brochure</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($map_embed): ?>
                        <div class="ce-property-detail__map">
                            <h3>Location</h3>
                            <iframe src="<?php echo esc_url($map_embed); ?>" width="100%" height="400" style="border:0; border-radius: var(--ce-border-radius);" allowfullscreen loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right column: enquiry form -->
                <div class="ce-property-detail__sidebar">
                    <div class="ce-property-detail__enquiry-card">
                        <h3>Interested in this property?</h3>
                        <p>Register your interest and our team will be in touch within 24 hours.</p>
                        <form class="ce-form" id="form-property-enquiry" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
                            <input type="hidden" name="source" value="property_page">
                            <input type="hidden" name="property_interest" value="<?php echo esc_attr(get_the_title()); ?>">
                            <input type="hidden" name="source_url" value="<?php echo esc_url(get_permalink()); ?>">
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="text" name="name" placeholder="Full Name *" required>
                            </div>
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="email" name="email" placeholder="Email *" required>
                            </div>
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="tel" name="phone" placeholder="Phone">
                            </div>
                            <div class="ce-form__group">
                                <textarea class="ce-form__input" name="message" rows="3" placeholder="Message"></textarea>
                            </div>
                            <div class="ce-form__group ce-form__group--checkbox">
                                <label>
                                    <input type="checkbox" name="gdpr_consent" required>
                                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a>.
                                </label>
                            </div>
                            <button type="submit" class="ce-btn ce-btn--gold" style="width: 100%;">Register Interest</button>
                            <div class="ce-form__status" aria-live="polite"></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related testimonials -->
            <?php if ($city_terms):
                $related_testimonials = new WP_Query([
                    'post_type'      => 'ce_testimonial',
                    'posts_per_page' => 3,
                    'tax_query'      => [
                        ['taxonomy' => 'ce_city', 'field' => 'term_id', 'terms' => $city_terms[0]->term_id],
                    ],
                ]);
                if ($related_testimonials->have_posts()): ?>
                    <div class="ce-section" style="margin-top: var(--ce-space-xl);">
                        <h2>What Investors Say About <?php echo esc_html($city_name); ?></h2>
                        <div class="ce-grid ce-grid--3">
                            <?php while ($related_testimonials->have_posts()): $related_testimonials->the_post();
                                get_template_part('template-parts/testimonial-card');
                            endwhile;
                            wp_reset_postdata(); ?>
                        </div>
                    </div>
                <?php endif;
            endif; ?>

            <!-- Property disclaimer -->
            <div class="ce-property-disclaimer">
                <p>Prices, specifications, and completion dates are indicative and subject to change. Please contact us for the latest information.</p>
            </div>
        </div>
    </section>
</main>

<?php if ($gated): ?>
    <?php get_template_part('template-parts/modal-brochure-gate'); ?>
<?php endif; ?>

<?php get_footer(); ?>
