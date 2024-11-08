<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Kingsland_Travel_Package_Widget extends Widget_Base
{
    // Previous get_name(), get_title(), get_icon(), get_categories() methods remain the same...

    /**
     * Get all tour categories
     * @return array
     */
    private function get_tour_categories()
    {
        $categories = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
        ]);

        $category_options = ['all' => __('All Categories', 'kingsland-custom-widget')];

        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $category_options[$category->slug] = $category->name;
            }
        }

        return $category_options;
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
            'layout',
            [
                'label' => __('Layout', 'kingsland-custom-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'list',
                'options' => [
                    'list' => __('List', 'kingsland-custom-widget'),
                    'grid' => __('Grid', 'kingsland-custom-widget'),
                ],
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

        // Start output buffering
        ob_start();

        if ($query->have_posts()) {
            echo '<div class="travel-package-wrapper ' . esc_attr($settings['layout']) . '-layout">';

            if ($query->have_posts()) {
                // Add category filter buttons if needed
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
            }

            while ($query->have_posts()) {
                $query->the_post();
                // Get post categories
                $post_categories = get_the_terms(get_the_ID(), 'tour_category');
                $category_classes = '';
                if (!is_wp_error($post_categories) && !empty($post_categories)) {
                    foreach ($post_categories as $cat) {
                        $category_classes .= ' category-' . $cat->slug;
                    }
                }

                // Get post meta with default values
                $price = get_post_meta(get_the_ID(), 'price', true) ?: 'N/A';
                $old_price = get_post_meta(get_the_ID(), 'old_price', true) ?: '';
                $discount = get_post_meta(get_the_ID(), 'discount', true) ?: '';
                $hotel_star = get_post_meta(get_the_ID(), 'hotel_star', true) ?: 'Not specified';
                $duration = get_post_meta(get_the_ID(), 'duration', true) ?: 'Duration not specified';
                $trip_location = get_post_meta(get_the_ID(), 'trip_location', true) ?: 'Location not specified';

                ?>
                <div class="travel-package-card">
                    <div class="package-image-section">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'package-image', 'style' => 'width: 100%; height: 100%;')); ?>
                        <?php endif; ?>

                        <?php if ($discount): ?>
                            <div class="discount-badge">
                                Winter Deal
                                <div><?php echo esc_html($discount); ?>% off</div>
                            </div>
                        <?php endif; ?>

                        <div class="features-row">
                            <!-- add service-icons from product -->

                            <?php
                            $services = maybe_unserialize(get_post_meta(get_the_ID(), 'services', true));
                            if (!empty($services) && is_array($services)):
                                foreach ($services as $service_icon):
                                    if ($service_icon === 'hotel'): ?>
                                        <span class="feature-badge">
                                            <i class="fas fa-hotel">
                                                <p>Hotel</p>
                                            </i>
                                        </span>

                                    <?php elseif ($service_icon === 'utensils'): ?>
                                        <span class="feature-badge">
                                            <i class="fas fa-utensils">
                                                <p>Meal</p>
                                            </i>
                                        </span>
                                    <?php elseif ($service_icon === 'car'): ?>
                                        <span class="feature-badge">
                                            <i class="fas fa-car">
                                                <p>Cab</p>
                                            </i>
                                        </span>

                                    <?php endif;
                                endforeach;
                            endif; ?>
                            <!-- <span class="feature-badge">sightseeing</span>
                            <span class="feature-badge">Meals</span>
                            <span class="feature-badge">Cab</span> -->
                            <span class="feature-badge"><?php echo esc_html($hotel_star); ?></span>
                        </div>
                    </div>

                    <div class="package-details">
                        <h2 class="package-title"><?php the_title(); ?></h2>

                        <?php if ($duration): ?>
                            <div class="package-meta">
                                <span><?php echo esc_html($duration); ?></span>
                                <span><?php echo esc_html($hotel_star); ?> Hotels Included</span>
                            </div>
                        <?php endif; ?>

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

                            <a href="<?php echo esc_url(add_query_arg('request_callback', 'true', get_permalink())); ?>"
                                class="check-availability">Request Callback</a>
                        </div>
                    </div>
                </div>
                <?php
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<div class="no-packages-found">';
            echo __('No packages found. Please check your widget settings and ensure you have published tour packages.', 'kingsland-custom-widget');
            echo '</div>';
        }

        // End output buffering and echo content
        echo ob_get_clean();
    }

    protected function content_template()
    {
        ?>
        <div class="travel-package-wrapper {{ settings.layout }}-layout">
            <div class="travel-package-card">
                <div class="package-image-section">
                    <img src="<?php echo plugins_url('assets/placeholder.jpg', __FILE__); ?>" alt="Package"
                        class="package-image">
                    <div class="discount-badge">Winter Deal<div>23% off</div>
                    </div>
                    <div class="features-row">

                        <span class="feature-badge">Cab</span>
                        <span class="feature-badge">Meals</span>
                        <span class="feature-badge">sightseeing</span>
                        <span class="feature-badge">Upto 4 Stars</span>
                    </div>
                </div>
                <div class="package-details">
                    <h2 class="package-title">Sample Package Title</h2>
                    <div class="package-meta"><span>5 Days & 4 Nights</span></div>
                    <div class="price-section">
                        <span class="price">₹73,999/-</span>
                        <span class="original-price">₹96,199/-</span>
                        <span class="discount-tag">23% Off</span>
                        <div class="price-note">Per Person on twin sharing</div>
                    </div>
                    <div class="cities">Cities: Sample City (5D)</div>
                    <div class="action-buttons">
                        <a href="#" class="view-deal">View Deal</a>
                        <a href="#" class="check-availability">Request Callback</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
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