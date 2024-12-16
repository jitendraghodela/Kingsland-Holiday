<?php
// Wiget for displaying travel packages As Grid OR List
// Elementor\Widget_Base is the base class for all widgets
// Author: Jitendra Ghodela
// Version: 1.0.0

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Kingsland_Travel_Package_Widget extends Widget_Base
{

    /**
     * Get all tour categories
     * @return array
     */
    private function get_tour_categories()
    {
        try {
            // Default options
            $category_options = [
                'all' => __('All Categories', 'kingsland-custom-widget')
            ];

            // Get categories with error handling
            $categories = get_terms([
                'taxonomy' => 'category',
                'hide_empty' => false,
            ]);

            // Check for WP_Error
            if (is_wp_error($categories)) {
                error_log('Category fetch error: ' . $categories->get_error_message());
                return $category_options;
            }

            // Check for null/empty
            if (!empty($categories) && is_array($categories)) {
                foreach ($categories as $category) {
                    if (isset($category->slug) && isset($category->name)) {
                        $category_options[$category->slug] = $category->name;
                    }
                }
            }

            return $category_options;

        } catch (Exception $e) {
            error_log('Error getting categories: ' . $e->getMessage());
            return ['all' => __('All Categories', 'kingsland-custom-widget')];
        }
    }

    public function get_name()
    {
        return 'kingsland_travel_package';
    }

    public function get_title()
    {
        return __('Kingsland Travel Package', 'kingsland-custom-widget');
    }

    public function get_icon()
    {
        return 'eicon-upgrade-crown';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {
        // Content Section remains the same
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'kingsland-custom-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Previous controls remain the same
        $this->add_control(
            'choose_category',
            [
                'label' => __('Choose category', 'kingsland-custom-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => $this->get_tour_categories(),
            ]
        );


        $this->add_control(
            'choose_category_tour',
            [
                'label' => __('Tour Category', 'kingsland-custom-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => '3_days',
                'options' => [
                    '3_days' => __('3 Days', 'kingsland-custom-widget'),
                    '7_days' => __('7 Days', 'kingsland-custom-widget'),
                ],
                'condition' => [
                    'choose_service' => 'tour',
                ],
            ]
        );



        // Update order control
        $this->add_control(
            'order',
            [
                'label' => __('Order', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'Ascending',
                'options' => [
                    'ascending' => __('Ascending', 'kingsland-custom-widget'),
                    'descending' => __('Descending', 'kingsland-custom-widget'),

                ],
            ]
        );
        // Add in register_controls() method after other controls
        $this->add_control(
            'contact_number',
            [
                'label' => __('Contact Number', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '+916376983416',
                'placeholder' => '+91XXXXXXXXXX',
                'description' => __('Enter WhatsApp number with country code', 'kingsland-custom-widget'),
                'label_block' => true,
            ]
        );

        // filter for number of items to display a(int) to d(int)  and e(int) to g(int) or z(int) to b(int) in order show
        $this->add_control(
            'number_items',
            [
                'label' => __('Number of Items', 'kingsland-custom-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 50,
            ]
        );


        $this->add_control(
            'layout',
            [
                'label' => __('Layout', 'kingsland-custom-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'List',
                'options' => [
                    'grid' => __('Grid', 'kingsland-custom-widget'),
                    'list' => __('List', 'kingsland-custom-widget'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style section remains the same
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'kingsland-custom-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'kingsland-custom-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .package-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Price Color', 'kingsland-custom-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#4CAF50',
                'selectors' => [
                    '{{WRAPPER}} .price' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->end_controls_section();
    }

    private function get_tour_packages($settings)
    {
        // Modified query arguments
        $args = array(
            'post_type' => 'tour_package',
            'posts_per_page' => intval($settings['number_items']),
            'post_status' => 'publish',
            'orderby' => 'date',
            'cache_results' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );
        // Handle different order scenarios
        switch ($settings['order']) {
            case 'ascending':
                $query_args['order'] = 'ASC';
                $query_args['orderby'] = 'date';
                break;

            case 'descending':
                $query_args['order'] = 'DESC';
                $query_args['orderby'] = 'date';
                break;

            case 'custom':
                // Custom order handling
                $custom_order = !empty($settings['custom_post_order'])
                    ? explode(',', $settings['custom_post_order'])
                    : [];

                if (!empty($custom_order)) {
                    // Sanitize and validate the custom order
                    $custom_order = array_map('intval', $custom_order);
                    $custom_order = array_filter($custom_order);

                    if (!empty($custom_order)) {
                        $query_args['orderby'] = 'post__in';
                        $query_args['post__in'] = $custom_order;
                    }
                }
                break;
        }

        // Add category filter if not 'all'
        if ($settings['choose_category'] !== 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $settings['choose_category'],
                ]
            ];
        }

        // Debug query
        $query = new \WP_Query($args);
        error_log('Tour Package Query: ' . print_r($args, true));
        error_log('Found Posts: ' . $query->found_posts);

        return $query;
    }

    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $query = $this->get_tour_packages($settings);

        if ($query->have_posts()) {
            // Add grid-specific wrapper class
            $layout_class = $settings['layout'] === 'grid' ? 'travel-grid-wrapper' : 'travel-list-wrapper';

            echo '<div class="travel-package-wrapper ' . esc_attr($layout_class) . '">';

            // Category filters section
            if ($settings['choose_category_tour'] === 'all') {
                $categories = get_terms([
                    'taxonomy' => 'tour_category',
                    'hide_empty' => true,
                ]);
                if (!is_wp_error($categories) && !empty($categories)) {
                    echo '<div class="tour-category-filters">';
                    echo '<button class="category-filter active" data-category="all">' . __('All', 'kingsland-custom-widget') . '</button>';
                    foreach ($categories as $category) {
                        echo '<button class="category-filter" data-category="' . esc_attr($category->slug) . '">'
                            . esc_html($category->name) . '</button>';
                    }
                    echo '</div>';
                }
            }

            // Grid container for grid layout
            if ($settings['layout'] === 'grid') {
                echo '<div class="travel-grid-container">';
            }

            while ($query->have_posts()) {
                $query->the_post();

                // Get post meta with default values
                $price = get_post_meta(get_the_ID(), 'price', true) ?: 'N/A';
                $old_price = get_post_meta(get_the_ID(), 'old_price', true) ?: '';
                $discount = get_post_meta(get_the_ID(), 'discount', true) ?: '';
                $hotel_star = get_post_meta(get_the_ID(), 'hotel_star', true) ?: 'Not specified';
                $duration = get_post_meta(get_the_ID(), 'duration', true) ?: 'Duration not specified';
                $trip_location = get_post_meta(get_the_ID(), 'trip_location', true) ?: 'Location not specified';

                // Grid item class based on layout
                $item_class = $settings['layout'] === 'grid' ? 'travel-grid-item' : 'travel-list-item';
                ?>


                <div class="travel-package-card <?php echo esc_attr($item_class); ?>">
                    <div class="package-image-section" style="cursor: pointer;"
                        onclick="window.location.href='<?php the_permalink(); ?>'">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'package-image')); ?>
                        <?php endif; ?>

                        <?php if ($discount): ?>
                            <div class="discount-badge">
                                Winter Deal
                                <div><?php echo esc_html($discount); ?>% off</div>
                            </div>
                        <?php endif; ?>

                        <div class="features-row">
                            <?php
                            $services = maybe_unserialize(get_post_meta(get_the_ID(), 'services', true));
                            if (!empty($services) && is_array($services)):
                                foreach ($services as $service_icon):
                                    switch ($service_icon) {
                                        case 'hotel':
                                            echo '<span class="feature-badge">
                                                    <i class="fas fa-hotel"><p>Hotel</p></i>
                                                  </span>';
                                            break;
                                        case 'utensils':
                                            echo '<span class="feature-badge">
                                                    <i class="fas fa-utensils"><p>Meal</p></i>
                                                  </span>';
                                            break;
                                        case 'car':
                                            echo '<span class="feature-badge">
                                                    <i class="fas fa-car"><p>Cab</p></i>
                                                  </span>';
                                            break;
                                    }
                                endforeach;
                            endif;
                            ?>
                            <span class="feature-badge">
                                <i class="fas fa-star" style="color: #FFD700;"></i>
                                <?php echo esc_html($hotel_star); ?></span>
                        </div>
                    </div>

                    <div class="package-details">
                        <h2 style="cursor: pointer;" onclick="window.location.href='<?php the_permalink(); ?>'" class="package-title">
                            <?php the_title(); ?>
                        </h2>

                        <div class="package-meta">
                            <?php if ($duration): ?>
                                <span><?php echo esc_html($duration); ?></span>
                            <?php endif; ?>
                            <span><?php echo esc_html($hotel_star); ?> Hotels</span>
                        </div>

                        <div class="price-section">
                            <?php if ($price !== 'N/A'): ?>
                                <span class="price">₹<?php echo esc_html($price); ?>/-</span>
                            <?php endif; ?>

                            <?php if ($old_price): ?>
                                <span class="original-price">₹<?php echo esc_html($old_price); ?>/-</span>
                            <?php endif; ?>

                            <?php if ($discount): ?>
                                <span class="discount-tag"><?php echo esc_html($discount); ?>% Off</span>
                            <?php endif; ?>

                            <div class="price-note">On Double sharing</div>
                        </div>

                        <?php if ($trip_location): ?>
                            <div class="cities">Cities: <?php echo esc_html($trip_location); ?></div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <a href="<?php the_permalink(); ?>" class="view-deal">View Deal</a>
                            <a href="https://wa.me/<?php
                            // Remove non-numeric chars from number
                            $number = preg_replace('/[^0-9]/', '', $settings['contact_number']);
                            echo esc_attr($number);
                            ?>?text=Hi, I'm interested in <?php echo esc_attr(get_the_title()); ?>" target="_blank"
                                class="check-availability whatsapp-btn">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                    <!-- add button for scrolling and make dyn -->
                </div>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                    }

                    /* Enhanced Grid Layout Styles */
                    .travel-package-wrapper.grid-layout {
                        padding: 30px;
                        max-width: 300px;
                        margin: 0 auto;
                        background: #f8f9fa;
                    }

                    .travel-grid-container {

                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                        gap: 30px;
                        margin-top: 30px;
                    }

                    /* Card Styling */
                    .travel-grid-item {
                        width: 344px;
                        background: #fff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        height: 498px;
                        display: flex;
                        flex-direction: column;
                        border: 1px solid rgba(0, 0, 0, 0.05);
                    }

                    .travel-grid-item:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                    }

                    /* Image Section */
                    .travel-grid-item .package-image-section {
                        position: relative;

                        overflow: hidden;
                    }

                    .travel-grid-item .package-image {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.5s ease;
                    }

                    .travel-grid-item:hover .package-image {
                        transform: scale(1.05);
                    }

                    /* Discount Badge */
                    .travel-grid-item .discount-badge {
                        position: absolute;
                        top: 15px;
                        left: 15px;
                        background: linear-gradient(45deg, #ff5a5f, #ff4757);
                        color: #fff;
                        padding: 8px 15px;
                        border-radius: 25px;
                        font-size: 14px;
                        font-weight: 600;
                        box-shadow: 0 2px 8px rgba(255, 90, 95, 0.3);
                    }

                    /* Features Row */
                    .travel-grid-item .features-row {
                        position: absolute;
                        bottom: 5px;
                        left: 15px;
                        right: 15px;
                        display: flex;
                        gap: 10px;
                        background: rgba(255, 255, 255, 0.95);
                        padding: 1px;
                        border-radius: 9px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    }

                    .travel-grid-item .feature-badge {
                        display: flex;
                        align-items: center;
                        font-size: 13px;
                        color: #444;
                    }

                    .travel-grid-item .feature-badge i {
                        margin-right: 5px;
                        color: #20a397;
                    }

                    /* Content Section */
                    .travel-grid-item .package-details {
                        padding: 10px;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                    }

                    .travel-grid-item .package-title {
                        margin: 0;
                        font-size: 20px;
                        font-weight: 600;
                        color: #2d3436;
                        margin: 0 0 15px;
                        line-height: 1.4;
                    }

                    .travel-grid-item .package-meta {
                        margin: 0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        font-size: 14px;
                        color: #636e72;

                    }

                    /* Price Section */
                    .travel-grid-item .price-section {
                        margin: 0;
                        display: flex;
                        align-items: center;

                        flex-wrap: wrap;
                    }

                    .travel-grid-item .price {
                        font-size: 24px;
                        font-weight: 700;
                        color: #20a397;
                    }

                    .travel-grid-item .original-price {
                        font-size: 16px;
                        color: #b2bec3;
                        text-decoration: line-through;
                    }

                    .travel-grid-item .discount-tag {
                        background: #20a397;
                        color: white;
                        padding: 4px 8px;
                        border-radius: 4px;
                        font-size: 12px;
                        font-weight: 600;
                    }

                    .travel-grid-item .price-note {
                        width: 100%;
                        font-size: 12px;
                        color: #7f8c8d;

                    }

                    .travel-grid-item .cities {
                        font-size: 14px;
                        color: #636e72;
                        margin: 2px;
                    }

                    /* Action Buttons */
                    .travel-grid-item .action-buttons {
                        display: flex;
                        gap: 15px;
                        margin: 0;
                    }

                    .travel-grid-item .view-deal,
                    .travel-grid-item .check-availability {
                        flex: 1;
                        padding: 12px 20px;
                        border-radius: 6px;
                        font-weight: 600;
                        text-align: center;
                        transition: all 0.3s ease;
                    }

                    .travel-grid-item .view-deal {
                        background: transparent;
                        border: 2px solid #20a397;
                        color: #20a397;
                    }

                    .travel-grid-item .check-availability {
                        background: #20a397;
                        color: white;
                        border: none;
                    }

                    .travel-grid-item .view-deal:hover {
                        background: #20a397;
                        color: white;
                    }

                    .travel-grid-item .check-availability:hover {
                        background: #178577;
                    }

                    /* Category Filters */
                    .travel-package-wrapper .tour-category-filters {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 12px;
                        margin-bottom: 30px;
                    }

                    .category-filter {
                        padding: 10px 20px;
                        border-radius: 25px;
                        background: white;
                        border: 1px solid #dfe6e9;
                        color: #636e72;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }

                    .category-filter.active,
                    .category-filter:hover {
                        background: #20a397;
                        color: white;
                        border-color: #20a397;
                    }


                    /* Mobile View Adjustments */
                    @media (max-width: 480px) {
                        .travel-package-wrapper.grid-layout {
                            /* padding: 15px; */
                        }

                        .travel-grid-container {
                            display: flex;
                            gap: 20px;
                            overflow-x: auto;
                            scroll-snap-type: x mandatory;
                            -webkit-overflow-scrolling: touch;
                            padding-bottom: 10px;
                        }

                        .travel-grid-item {
                            flex: 0 0 80%;
                            /* 80% of the screen width per card */
                            scroll-snap-align: center;
                        }

                        .travel-grid-container::-webkit-scrollbar {
                            display: none;
                            /* Hide scrollbar */
                        }
                    }
                </style>

                <?php
            }

            // Close grid container if in grid layout
            if ($settings['layout'] === 'grid') {
                echo '</div>';
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<div class="no-packages-found">';
            echo __('No packages found. Please check your widget settings.', 'kingsland-custom-widget');
            echo '</div>';
        }
    }


}


// Register the widget
function register_kingsland_travel_package_widget($widgets_manager)
{
    if (!did_action('elementor/loaded')) {
        return;
    }

    \Elementor\Plugin::instance()->widgets_manager->register(new Kingsland_Travel_Package_Widget());
}

add_action('elementor/widgets/register', 'register_kingsland_travel_package_widget');
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

});
