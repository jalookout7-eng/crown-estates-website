<?php get_header(); ?>
<main class="ce-main">

    <?php get_template_part('template-parts/hero', null, [
        'title'    => 'Investment Projects',
        'subtitle' => 'Explore our curated selection of Saudi Arabian property investment opportunities.',
        'label'    => 'Projects',
    ]); ?>
    <div id="ce-3d-projects-map" class="ce-3d-canvas ce-3d-canvas--map" style="height: 300px; position: relative;"></div>

    <section class="ce-section">
        <div class="ce-container">
            <!-- Filter Bar -->
            <div class="ce-filter-bar">
                <div class="ce-filter-group">
                    <button class="ce-filter-btn active" data-filter="all" data-filter-type="city">All Cities</button>
                    <?php
                    $cities = get_terms(['taxonomy' => 'ce_city', 'hide_empty' => true]);
                    if ($cities && !is_wp_error($cities)):
                        foreach ($cities as $city):
                            printf(
                                '<button class="ce-filter-btn" data-filter="%s" data-filter-type="city">%s</button>',
                                esc_attr($city->slug),
                                esc_html($city->name)
                            );
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Property Grid -->
            <div class="ce-grid ce-grid--3">
                <?php
                $properties = new WP_Query([
                    'post_type'      => 'ce_property',
                    'posts_per_page' => -1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ]);
                if ($properties->have_posts()):
                    while ($properties->have_posts()): $properties->the_post();
                        get_template_part('template-parts/property-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Properties coming soon. Check back shortly.</p>';
                endif;
                ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>

</main>
<?php get_footer(); ?>
