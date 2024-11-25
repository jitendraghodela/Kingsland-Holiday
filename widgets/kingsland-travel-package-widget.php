<?php
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

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'kingsland-custom-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ascending',
                'options' => [
                    'ascending' => __('Ascending', 'kingsland-custom-widget'),
                    'descending' => __('Descending', 'kingsland-custom-widget'),
                ],
            ]
        );

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
            'order' => $settings['order'] === 'ascending' ? 'ASC' : 'DESC',
            'cache_results' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );

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
                            <span class="feature-badge"><?php echo esc_html($hotel_star); ?></span>
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

                            <div class="price-note">Per Person on twin sharing</div>
                        </div>

                        <?php if ($trip_location): ?>
                            <div class="cities">Cities: <?php echo esc_html($trip_location); ?></div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <a href="<?php the_permalink(); ?>" class="view-deal">View Deal</a>
                            <button class="check-availability" onclick="window.location.href='tel:+916376983416'">Call Us</button>
                        </div>
                    </div>
                    <!-- add button for scrolling and make dyn -->
                </div>


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
