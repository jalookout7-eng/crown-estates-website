<?php
/**
 * Template part: Developer Reliability Badge
 * Args: badge (string — 'verified', 'track_record', 'premium_partner', 'none')
 */
$badge = $args['badge'] ?? 'none';
if ($badge === 'none') return;

$badges = [
    'verified'        => ['label' => 'Verified Developer', 'icon' => '&#128737;'],
    'track_record'    => ['label' => 'Track Record',       'icon' => '&#128737;&#9733;'],
    'premium_partner' => ['label' => 'Premium Partner',    'icon' => '&#128737;&#9813;'],
];

$info = $badges[$badge] ?? null;
if (!$info) return;
?>
<span class="ce-developer-badge ce-developer-badge--<?php echo esc_attr($badge); ?>" title="<?php echo esc_attr($info['label']); ?>">
    <span class="ce-developer-badge__icon"><?php echo $info['icon']; ?></span>
    <span class="ce-developer-badge__label"><?php echo esc_html($info['label']); ?></span>
</span>
