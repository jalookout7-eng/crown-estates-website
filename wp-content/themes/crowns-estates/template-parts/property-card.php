<?php
/**
 * Template part: Property Card
 * Used on: front-page.php, page-projects.php, archive-ce_property.php
 */
$post_id     = get_the_ID();
$city_terms  = get_the_terms($post_id, 'ce_city');
$city_name   = $city_terms ? $city_terms[0]->name : '';
$city_slug   = $city_terms ? $city_terms[0]->slug : '';
$status      = get_field('ce_status', $post_id) ?: '';
$type        = get_field('ce_property_type', $post_id) ?: '';
$bedrooms    = get_field('ce_bedrooms', $post_id) ?: '';
$size        = get_field('ce_size_sqm', $post_id) ?: '';
$freehold    = get_field('ce_is_freehold', $post_id);
$developer   = get_field('ce_developer', $post_id) ?: '';
$badge       = get_field('ce_developer_badge', $post_id) ?: 'none';
$completion  = get_field('ce_completion_date', $post_id);
$comp_year   = $completion ? date('Y', strtotime($completion)) : '';
$thumbnail   = get_the_post_thumbnail_url($post_id, 'large') ?: get_template_directory_uri() . '/img/placeholder-property.jpg';

$status_class = '';
if ($status === 'off-plan') $status_class = 'ce-property-card__status--off-plan';
if ($status === 'under-construction') $status_class = 'ce-property-card__status--construction';
if ($status === 'ready') $status_class = 'ce-property-card__status--ready';

$status_labels = [
    'off-plan'           => 'Off-Plan',
    'under-construction' => 'Under Construction',
    'ready'              => 'Ready',
];
$type_labels = [
    'apartment'  => 'Apartment',
    'villa'      => 'Villa',
    'commercial' => 'Commercial',
];
?>
<div class="ce-property-card" data-city="<?php echo esc_attr($city_slug); ?>" data-developer="<?php echo esc_attr(sanitize_title($developer)); ?>" data-status="<?php echo esc_attr($status); ?>">
    <a href="<?php the_permalink(); ?>" class="ce-property-card__image" style="background-image: url('<?php echo esc_url($thumbnail); ?>');">
        <?php if ($city_name): ?>
            <span class="ce-property-card__badge"><?php echo esc_html($city_name); ?></span>
        <?php endif; ?>
        <?php if ($status): ?>
            <span class="ce-property-card__status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_labels[$status] ?? $status); ?></span>
        <?php endif; ?>
    </a>
    <div class="ce-property-card__body">
        <h3 class="ce-property-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="ce-property-card__developer">
            <?php echo esc_html($developer); ?>
            <?php if ($badge !== 'none'): ?>
                <?php get_template_part('template-parts/developer-badge', null, ['badge' => $badge]); ?>
            <?php endif; ?>
        </div>
        <div class="ce-property-card__type">
            <?php echo esc_html($type_labels[$type] ?? $type); ?>
            <?php if ($freehold): ?> &bull; Freehold<?php endif; ?>
        </div>
        <div class="ce-property-card__meta">
            <?php if ($bedrooms): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($bedrooms); ?></strong> Beds</div>
            <?php endif; ?>
            <?php if ($size): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($size); ?></strong> Sq.M.</div>
            <?php endif; ?>
            <?php if ($comp_year): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($comp_year); ?></strong> Completion</div>
            <?php endif; ?>
        </div>
        <div class="ce-property-card__footer">
            <div class="ce-property-card__price"><?php echo ce_display_price($post_id); ?> <small>from</small></div>
            <a href="<?php the_permalink(); ?>" class="ce-btn ce-btn--outline ce-btn--sm">View</a>
        </div>
    </div>
</div>
