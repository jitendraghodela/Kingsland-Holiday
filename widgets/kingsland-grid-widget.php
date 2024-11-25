<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Kingsland_Grid_Widget extends \Elementor\Widget_Base
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
        return 'kingsland_grid';
    }

    public function get_title()
    {
        return __('Kingsland Grid', 'kingsland-custom-widget');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'kingsland-custom-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        // Previous controls remain the same
        $this->add_control(
            'choose_category',
            [
                'label' => __('Choose category', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => $this->get_tour_categories(),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Number of Packages', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 1,
                'max' => 12,
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => __('Order', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'ascending',
                'options' => [
                    'ascending' => __('Ascending', 'kingsland-custom-widget'),
                    'descending' => __('Descending', 'kingsland-custom-widget'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'kingsland-custom-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => __('Card Background', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wid-package-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Price Color', 'kingsland-custom-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#00a884',
                'selectors' => [
                    '{{WRAPPER}} .wid-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_tour_packages($settings)
    {
        $args = array(
            'post_type' => 'tour_package',
            'posts_per_page' => intval($settings['posts_per_page']),
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => $settings['order'] === 'ascending' ? 'ASC' : 'DESC',
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

        return new WP_Query($args);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $query = $this->get_tour_packages($settings);

        if (!$query->have_posts()) {
            return;
        }

        ?>
        <div class="wid-container">
            <div class="wid-packages-section">
                <div class="wid-packages-container" id="packagesContainer">
                    <div class="wid-packages-grid" id="packagesGrid">
                        <?php
                        while ($query->have_posts()):
                            $query->the_post();
                            $package_id = get_the_ID();
                            $price = get_post_meta($package_id, 'price', true) ?: 'N/A';
                            $old_price = get_post_meta($package_id, 'old_price', true) ?: '';
                            $discount = get_post_meta($package_id, 'discount', true) ?: '';
                            $hotel_star = get_post_meta($package_id, 'hotel_star', true) ?: 'Not specified';
                            $duration = get_post_meta($package_id, 'duration', true) ?: 'Duration not specified';
                            $trip_location = get_post_meta($package_id, 'trip_location', true) ?: 'Location not specified';
                            $location_text = sprintf('%s (%s)', esc_html($trip_location), esc_html($duration));
                            ?>
                            <a href="<?php the_permalink(); ?>">
                                <div class="wid-package-card">
                                    <div class="wid-package-image">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('large', ['alt' => get_the_title()]); ?>
                                        <?php else: ?>
                                            <img src="<?php echo esc_url(plugins_url('assets/placeholder.jpg', __FILE__)); ?>"
                                                alt="<?php echo esc_attr(get_the_title()); ?>">
                                        <?php endif; ?>
                                        <div class="wid-location-badge"><?php echo $location_text; ?></div>
                                    </div>
                                    <div class="wid-package-content">
                                        <!-- badges -->

                                        <h3 class="wid-package-title"><?php the_title(); ?></h3>
                                        <div class="wid-price-section">
                                            <div>
                                                <span class="wid-price">₹<?php echo number_format((float) $price); ?>/-</span>
                                                <span
                                                    class="wid-per-person"><?php _e('per person', 'kingsland-custom-widget'); ?></span>
                                                <div><?php echo esc_html($hotel_star); ?> Hotels Included</div>

                                            </div>

                                        </div>
                                        <div style="display:flex;    justify-content: space-around;">
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
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const packageCards = document.querySelectorAll('.wid-package-card');

                        packageCards.forEach(card => {
                            card.addEventListener('click', function () {
                                const postId = this.getAttribute('data-post-id');
                                if (postId) {
                                    window.location.href = '<?php echo get_permalink(); ?>' + postId;
                                }
                            });
                        });
                    });
                </script>

                <div class="wid-nav-buttons">
                    <button class="wid-scroll-button prev" id="prevBtn"
                        aria-label="<?php _e('Previous', 'kingsland-custom-widget'); ?>">
                        <svg viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                    </button>
                    <button class="wid-scroll-button next" id="nextBtn"
                        aria-label="<?php _e('Next', 'kingsland-custom-widget'); ?>">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const packagesGrid = document.getElementById('packagesGrid');

                prevBtn.addEventListener('click', function () {
                    packagesGrid.scrollBy({
                        left: -300,
                        behavior: 'smooth'
                    });
                });

                nextBtn.addEventListener('click', function () {
                    packagesGrid.scrollBy({
                        left: 300,
                        behavior: 'smooth'
                    });
                });

            });
        </script>



        <?php
        wp_reset_postdata();
    }

    public function get_script_depends()
    {
        return ['kingsland-grid-widget-js'];
    }

    public function get_style_depends()
    {
        return ['kingsland-grid-widget-css'];
    }
}

// Register Widget
function register_kingsland_grid_widget($widgets_manager)
{
    $widgets_manager->register(new Kingsland_Grid_Widget());
}
add_action('elementor/widgets/register', 'register_kingsland_grid_widget');
