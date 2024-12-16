<?php
/**
 * Template Name: Kingsland Tour Package
 * Description: A custom template for displaying a single tour package
 *
 * @package Kingsland_Tour_Packages
 * @version 1.0.0
 * @since 1.0.0
 */

// Ensure this code is placed at the beginning of the template file
require_once(ABSPATH . 'wp-load.php');
require_once(ABSPATH . 'wp-includes/class-wp-query.php');
get_header();
// Get the current post ID  
$post_id = get_the_ID();
// Retrieve the necessary post meta data
// At the beginning of your template, add debugging
$selected_cats = get_post_meta(get_the_ID(), 'display_categories', true);
error_log('Selected Categories: ' . print_r($selected_cats, true));
// Retrieve the necessary post meta data
$slideshow_images = get_post_meta($post->ID, 'slideshow_images', true);
$slideshow_captions = get_post_meta($post->ID, 'slideshow_captions', true);
$slideshow_positions = get_post_meta($post->ID, 'slideshow_positions', true);

// Retrieve the necessary post meta data
$package = [
    'title' => get_the_title($post_id),
    'deal' => get_post_meta($post_id, 'deal', true),
    'duration' => get_post_meta($post_id, 'duration', true),
    'location' => get_post_meta($post_id, 'trip_location', true),
    'hotel_info' => get_post_meta($post_id, 'hotel_info', true),
    'price' => get_post_meta($post_id, 'price', true),
    'old_price' => get_post_meta($post_id, 'old_price', true),
    'rating' => get_post_meta($post_id, 'rating', true),
    'accommodation' => get_post_meta($post_id, 'accommodation', true),
    'activities' => get_post_meta($post_id, 'activities', true),
    'highlights' => maybe_unserialize(get_post_meta($post_id, 'highlights', true)),
    'itinerary' => maybe_unserialize(get_post_meta($post_id, 'itinerary', true)), //in itinerary we can show day with title or one paragraph so user can see information about the day  with tags
    'stay_info' => get_post_meta($post_id, 'stay_info', true),
    'inclusions' => maybe_unserialize(get_post_meta($post_id, 'inclusions', true)),
    'exclusions' => maybe_unserialize(get_post_meta($post_id, 'exclusions', true)),
    'reviews' => maybe_unserialize(get_post_meta($post_id, 'reviews', true)),
    'faqs' => maybe_unserialize(get_post_meta($post_id, 'faqs', true)),
    'hotel_star' => maybe_unserialize(get_post_meta($post_id, 'hotel_star', true)),
    'services' => maybe_unserialize(get_post_meta($post_id, 'services', true)),
    'discount' => maybe_unserialize(get_post_meta($post_id, 'deal_text', true)),
    'destinations_covered' => get_post_meta(get_the_ID(), '_destinations_covered', true),
    'things_to_do' => get_post_meta(get_the_ID(), 'things_to_do', true),
    'hotels' => maybe_unserialize(get_post_meta($post_id, 'hotels', true)),
    'gallery' => maybe_unserialize(get_post_meta($post_id, 'gallery', true)),
    'slideshow_images' => get_post_meta($post_id, 'slideshow_images', true),
    'slideshow_captions' => get_post_meta($post_id, 'slideshow_captions', true),
    'slideshow_positions' => get_post_meta($post_id, 'slideshow_positions', true),
    'destinations' => get_post_meta($post_id, 'destinations', true),
];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($package['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/css/style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-size: 21px;
        }


        /* Content and Read More styling */
        .content-wrapper {
            position: relative;
            max-height: 100px;
            /* Adjust this value to show roughly 4 lines */
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .content-wrapper.expanded {
            max-height: none;
        }

        .content-wrapper:not(.expanded)::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: linear-gradient(transparent, white);
        }

        .mob-tab-sticky {
            overflow: hidden;
            position: relative;
            width: 100%;
            background: #fff;
            z-index: 100;
        }

        /* Class that gets added via JavaScript */
        .is-sticky {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-in-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .mob-only {
            display: block;
            /* Visible by default on mobile */
        }



        .mobile-tabs a {
            text-decoration: none;
            /* Remove default underline */
            position: relative;
        }

        .mobile-tabs a.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background-color: #000;
            /* Change to your desired color */
        }

        @media screen and (min-width: 768px) {
            .mob-only {
                display: none;
                /* Hidden on desktop */
            }
        }

        .slideshow-prev,
        .slideshow-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            padding: 16px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 18px;
            border: none;
            cursor: pointer;
            border-radius: 50%;
            transition: background-color 0.3s;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slideshow-next {
            right: 20px;
        }

        .slideshow-prev {
            left: 20px;
        }



        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
        }

        .lightbox-content {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            margin: auto;
        }

        .lightbox .close {
            position: absolute;
            top: 15px;
            right: 25px;
            color: #fff;
            font-size: 35px;
            cursor: pointer;
            z-index: 1001;
        }

        .lightbox .prev,
        .lightbox .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 16px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
        }

        .lightbox .prev {
            left: 20px;
        }

        .lightbox .next {
            right: 20px;
        }

        .lightbox-caption {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            padding: 10px;
            background: rgba(0, 0, 0, 0.5);
        }

        .slideshow-container {
            max-width: 1000px;
            position: relative;
            margin: auto;
            height: 290px;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
        }

        .slide {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .slide.active {
            display: block;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .caption {
            position: absolute;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 18px;
            transition: opacity 0.3s;
        }

        .caption.top-left {
            top: 20px;
            left: 20px;
        }

        .caption.top-right {
            top: 20px;
            right: 20px;
        }

        .caption.bottom-left {
            bottom: 20px;
            left: 20px;
        }

        .caption.bottom-right {
            bottom: 20px;
            right: 20px;
        }

        .caption.middle {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }



        @keyframes fade-silde {
            from {
                opacity: 0.4;
            }

            to {
                opacity: 1;
            }
        }

        .faq-div {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .faq-ques {
            width: 60%;

        }

        .faq-img {
            width: 40%;
            padding-left: 10%;
        }

        .faq-img img {
            height: 300px;
            width: 300px;
            object-fit: cover;
        }
<<<<<<< HEAD

        .whatsapp-btn {
            background: #128C7E !important;
            color: white !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;

        }

        .whatsapp-btn:hover {
            background: #20a397 !important;
            transform: translateY(-2px);
        }

        .whatsapp-btn i {
            font-size: 20px;
        }
=======
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
    </style>
    <?php
    // Get WhatsApp number from options
    $whatsapp_number = preg_replace('/[^0-9]/', '', get_option('kingsland_whatsapp_number', '+916376983416'));
    $package_title = get_the_title();
    $price = get_post_meta(get_the_ID(), 'price', true);
    $duration = get_post_meta(get_the_ID(), 'duration', true);

    // Build message
    $message = "Hi, I'm interested in {$package_title}";
    if ($duration) {
        $message .= " ({$duration})";
    }
    if ($price) {
        $message .= " - ₹{$price}/-";
    }
    ?>
</head>

<body>
    <!-- Scrollable tabs -->
    <div class="mob-tab-sticky ">
        <div class="mobile-tabs" id="tabs">
            <a href="#overview" class="tab-item">Overview</a>
            <a href="#itinerary" class="tab-item">Itinerary</a>
            <a href="#hotels" class="tab-item">Hotels</a>
            <a href="#inclusions" class="tab-item">Inclusions/Exclusions</a>
            <a href="#faq" class="tab-item">FAQ</a>
        </div>
    </div>
    <div class="overview">
        <div class="op-container">


            <!-- First Section: Overview of Package -->
            <div class="image-container">

                <div class="slideshow-container">
                    <?php
                    // Ensure $package['slideshow_images'] is an array
                    if (is_array($package['slideshow_images'])) {
                        // Include the featured image as the first slide if it exists
                        if (has_post_thumbnail($post_id)) {
                            ?>
                            <div class="slide fade-silde">
                                <img src="<?php echo get_the_post_thumbnail_url($post_id, 'full'); ?>"
                                    alt="<?php echo esc_attr($package['title']); ?>">
                            </div>
                            <?php
                        }
                        foreach ($package['slideshow_images'] as $index => $image): ?>
                            <div class="slide fade-silde">
                                <img src="<?php echo esc_url($image); ?>"
                                    alt="<?php echo esc_attr($package['slideshow_captions'][$index]); ?>">
                                <?php if (!empty($package['slideshow_captions'][$index])): ?>
                                    <div class="caption <?php echo esc_attr($package['slideshow_positions'][$index]); ?>">
                                        <?php echo esc_html($package['slideshow_captions'][$index]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach;
                    } else {
                        echo '<p>No slideshow images available.</p>';
                    }
                    ?>

                    <button class="slideshow-prev" onclick="changeSlide(-1)">❮</button>
                    <button class="slideshow-next" onclick="changeSlide(1)">❯</button>
                </div>

                <div id="lightbox" class="lightbox" style="display">
                    <span class="close" onclick="closeLightbox()">&times;</span>
                    <div class="lightbox-content">
                        <img id="lightbox-img" src="" alt="">
                        <div class="lightbox-caption"></div>
                        <button class="lightbox-prev" onclick="changeLightboxSlide(-1)">❮</button>
                        <button class="lightbox-next" onclick="changeLightboxSlide(1)">❯</button>
                    </div>
                </div>
                <script>
                    let currentLightboxSlide = 0;
                    const lightboxSlides = document.querySelectorAll(".slide img");

                    function openLightbox(index) {
                        currentLightboxSlide = index;
                        const lightbox = document.getElementById("lightbox");
                        const lightboxImg = document.getElementById("lightbox-img");
                        const lightboxCaption = document.querySelector(".lightbox-caption");

                        lightboxImg.src = lightboxSlides[index].src;
                        lightboxCaption.textContent = lightboxSlides[index].alt;
                        lightbox.style.display = "block";
                    }

                    function closeLightbox() {
                        document.getElementById("lightbox").style.display = "none";
                    }

                    function changeLightboxSlide(direction) {
                        currentLightboxSlide += direction;

                        if (currentLightboxSlide >= lightboxSlides.length) {
                            currentLightboxSlide = 0;
                        } else if (currentLightboxSlide < 0) {
                            currentLightboxSlide = lightboxSlides.length - 1;
                        }

                        const lightboxImg = document.getElementById("lightbox-img");
                        const lightboxCaption = document.querySelector(".lightbox-caption");

                        lightboxImg.src = lightboxSlides[currentLightboxSlide].src;
                        lightboxCaption.textContent = lightboxSlides[currentLightboxSlide].alt;
                    }

                    document.querySelectorAll(".slide img").forEach((img, index) => {
                        img.addEventListener("click", () => openLightbox(index));
                    });
                </script>
            </div>

            <!-- Right Side: Details and Call to Action -->
            <div class="detailSide">
                <div class="details">
                    <div class="hotel-info mob-only">
                        <!-- <h4><?php echo esc_html($package['title']); ?> </h4> -->


                        <p>
<<<<<<< HEAD
                        <h2 style="display:inline;"> Cities: </h2> <?php echo esc_html($package['location']); ?>
=======
                        <h2> Cities: </h2> <?php echo esc_html($package['location']); ?>
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                        </p>
                        <p>
                            <?php echo esc_html($package['duration']); ?>
                            | <strong><?php echo esc_html($package['hotel_star']); ?></strong> Hotel included in
                            package
                        </p>


                    </div>
                    <div class="hotel-info desktop-only">
                        <p>
                            Cities:
                            <?php echo esc_html($package['location']); ?>
                        </p>

                        <strong>
                            <p>
                                <?php echo esc_html($package['duration']); ?>
                            </p>
                        </strong>
                        <p>
                            <strong><?php echo esc_html($package['hotel_star']); ?></strong> Hotel Included In
                            Package
                        </p>

                    </div>
                    <div class="price-section" style="margin:0;">
                        <h3 class="price">₹<?php echo esc_html($package['price']); ?>/-</h3>
                        <h3 class="old-price">₹<?php echo esc_html($package['old_price']); ?>/-</h3>

                        <span class="discount">

                            <?php echo esc_attr($package['discount']); ?>
                            <?php
                            // Get the current package price and old price from the package array
                            $current_price = floatval($package['price']);
                            $old_price = floatval($package['old_price']);

                            // Calculate the discount percentage based on the old price
                            if ($old_price > 0) { // Ensure old price is greater than zero to avoid division by zero
                                $discount_percentage = (($old_price - $current_price) / $old_price) * 100; // Calculate the discount percentage
                            } else {
                                $discount_percentage = 0;
                            }

                            // Display deal text and discount percentage
                            echo get_post_meta(get_the_ID(), 'deal_text', true);
                            echo number_format($discount_percentage) . '%' . ' off ';
                            ?>

                        </span>
<<<<<<< HEAD
                        <p>Double Sharing</p>
=======
                        <p>Per Person</p>
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                        <!-- <p class="rating">Rated <?php echo esc_html($package['rating']); ?> (based on reviews)</p> -->
                    </div>
                </div>
                <span>
                    <div class="cta-section">
                        <div class="service-icons">
                            <?php
                            $services = maybe_unserialize(get_post_meta(get_the_ID(), 'services', true));
                            if (!empty($services) && is_array($services)):
                                foreach ($services as $service_icon):
                                    if ($service_icon === 'guide'): ?>
                                        <i class="fas fa-user-tie">
                                            <p>Guide</p>
                                        </i>

                                    <?php elseif ($service_icon === 'hotel'): ?>
                                        <i class="fas fa-hotel">
                                            <p>Hotel</p>
                                        </i>

                                    <?php elseif ($service_icon === 'utensils'): ?>
                                        <i class="fas fa-utensils">
                                            <p>Meal</p>
                                        </i>

                                    <?php elseif ($service_icon === 'car'): ?>
                                        <i class="fas fa-car">
                                            <p>Cab</p>
                                        </i>
                                    <?php elseif ($service_icon === 'sightseeing'): ?>
                                        <i class="fa-solid fa-binoculars">
                                            <p>sightseeing</p>
                                        </i>

                                    <?php endif;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>

                    <a class="check-availability whatsapp-btn"
                        href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode($message); ?>"
                        target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <span>Enquire on WhatsApp</span>
                    </a>

                    <!-- <div class="package-info">

                    </div> -->
                </span>
            </div>
        </div>
    </div>

    <!-- Highlights and Itinerary -->
    <div class="content-section" id="overview">
        <div class="package-details">
            <div class="package-detailTOView">
                <h1 style="font-size:27px"><?php echo esc_html($package['title']); ?></h1>
                <!-- default content show here for default enditor-->

                <strong style="display:inline">
                    <h2>Overview:</h2>
                </strong>
                <div class="entry-content">
                    <div class="content-wrapper">
                        <?php the_content(); ?>
                    </div>
                    <div class="btn-read-more" style="
                                    display: flex;
                                    align-items: center;
                                    margin-bottom: 1rem;">
                        <div></div>
                        <button class="read-more-btn">Read More </button>
                    </div>
                </div>
                <span>
                    <p><strong>Cities:</strong> <?php echo esc_html($package['location']); ?></p>
                    <p><strong>Destinations Covered:</strong>
                        <?php echo esc_html(get_post_meta(get_the_ID(), 'destinations_covered', true)); ?></p>
                </span>

                <p><strong>Accommodation:</strong>
                    <?php echo esc_html(get_post_meta(get_the_ID(), 'accommodation', true)); ?></p>

                <!-- Brief content -->
                <p><strong>Things to do:</strong>
                    <?php echo esc_html($package['things_to_do']); ?>
                </p>
            </div>


            <!-- Highlights Section -->
            <div class="highlights">
                <h3>Highlights</h3>
                <div class="highlight-list">
                    <?php
                    $highlights = get_post_meta(get_the_ID(), 'highlights', true);
                    if (!empty($highlights)) {
                        $highlight_chunks = explode(',', $highlights);
                        $counter = 0;
                        foreach ($highlight_chunks as $highlight) {
                            echo '<div class="highlight-item"><p>' . esc_html(trim($highlight)) . '</p></div>';
                            $counter++;
                            if ($counter % 3 == 0) {
                                echo '<br class="desktop-only"/>'; // Add a line break after every third item or when mobile view it will hide
                            }
                        }
                    } else {
                        // Default highlights if none are set
                        echo '<div class="highlight-item"><p>Return flight tickets</p><p> City tour</p></div> <br/>';
                        echo '<div class="highlight-item"><p>Watersports Activities</p><p>Meals included</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- Overlay -->
        <div class="modal-overlay"></div>

        <!-- Contact Form -->
        <!-- Right: Inquiry Form -->
        <div class="form-section fromM " id="contactForm">
            <div class="drag-indicator"></div>
            <button class="close-btn">&times;</button>
            <h3>Request a Callback</h3>
            <form method="post">

                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Your Name" required />

                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="Your Phone Number" required />

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Your Email" style="width: 100%;
    padding: 10px;    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;" />


                <!-- package label or input are hide for user -->

                <input type="hidden" name="package" value="<?php echo esc_html($package['title']); ?>">



                <label for="message">Message</label>
                <textarea type="text" name="message" id="message" style="width: 100%;
        padding: 10px;    border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;"></textarea>

                <button type="submit" class="submit-btn" name="send" value="send">Submit</button>
            </form>
        </div>
        <!-- Right: Inquiry Form -->
        <div class="form-section sticky-form" id="contact">

            <h3>Request a Callback</h3>
            <form method="post">

                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Your Name" required />

                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="Your Phone Number" required />

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Your Email" style="width: 100%;
        padding: 10px;    border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;" />


                <!-- package label or input are hide for user -->

                <input type="hidden" name="package" value="<?php echo esc_html($package['title']); ?>">



                <label for="message">Message</label>
                <textarea type="text" name="message" id="message" style="width: 100%;
                padding: 10px;    border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;"></textarea>

                <button type="submit" class="submit-btn" name="send" value="send">Submit</button>
            </form>
        </div>
    </div>
    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    if (isset($_POST['send'])) {
        $name = htmlspecialchars($_POST['name']);
        $phone = htmlspecialchars($_POST['phone']);
        $email = htmlspecialchars($_POST['email']);
        $package_name = htmlspecialchars($_POST['package']);
        $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

        // Validate required fields
        if (empty($name) || empty($phone) || empty($email)) {
            echo "Please fill in all required fields.";
            exit;
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format";
            exit;
        }
        require plugin_dir_path(__FILE__) . '../PHPMailer/Exception.php';
        require plugin_dir_path(__FILE__) . '../PHPMailer/SMTP.php';
        require plugin_dir_path(__FILE__) . '../PHPMailer/PHPMailer.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = get_option('kingsland_email_username'); // Updated
            $mail->Password = get_option('kingsland_email_password'); // Updated
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('no-reply@kingland.com', 'Kingsland Holiday');
            $mail->addAddress('Kingslandholiday@gmail.com', 'Tour Package Inquiry');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Callback Request';
            $mail->Body = "
            <h2>New Callback Request Details</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Package:</strong> {$package_name}</p>
            <p><strong>Message:</strong> {$message}</p>
        ";
            $mail->AltBody = "New Callback Request\n\nName: {$name}\nPhone: {$phone}\nEmail: {$email}\nPackage: {$package_name}\nmessage: {$message}";

            $mail->send();
            // Show success message at bottom of form
            echo '<div class="form-message success">Your message has been sent successfully!</div>';

            // Add CSS for the message
            echo '<style>
                .form-message {
                    margin-top: 15px;
                    padding: 10px;
                    border-radius: 4px;
                    text-align: center;
                }
                .form-message.success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
            </style>';
        } catch (Exception $e) {
            echo "<div class='error-message'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    }
    ?>


    <!-- Itinerary -->
    <div class="row">
        <div class="itinerary-container">
            <div class="itinerary-header">Itinerary</div>
            <div class="itinerary-scroll-container" id="itinerary">
                <?php
                // Fetch the itinerary from post meta
                $itinerary = maybe_unserialize(get_post_meta(get_the_ID(), 'itinerary', true));

                // Ensure the variable is an array before using foreach
                if (is_array($itinerary) && !empty($itinerary)) {
                    foreach ($itinerary as $day) {
                        ?>
                        <div class="days-scroll-container">
                            <div class="day">
                                <div class="toggleitDaY" onclick="toggleTaq(this)">

                                    <div class="day-title" style="display:inline-block">
                                        <h3 style="font-size: 21px; margin-top: 0;">
                                            <?php echo esc_html($day['day_title']); ?>
                                            <br>
                                            <?php
                                            // Get tags for this day if they exist
                                            if (isset($day['day_tags']) && is_array($day['day_tags'])) {
                                                $tag_count = count($day['day_tags']);
                                                $displayed_tags = array_slice($day['day_tags'], 0, 2); // Show only the first 2 tags
                                    
                                                foreach ($displayed_tags as $tag) {
                                                    echo '<span class="day-tag day-tags" style="margin-right: 5px;">' . esc_html($tag) . '</span>';
                                                }

                                                if ($tag_count > 2) {
                                                    echo '<span class="day-tag day-tags">+' . ($tag_count - 2) . ' others</span>';
                                                }
                                            } else {
                                                // Default tags if none are set
                                                echo '<span class="day-tag">Breakfast</span>';
                                                echo '<span class="day-tag">Lunch</span>';
                                                echo '<span class="day-tag">Dinner</span>';
                                            }
                                            ?>



                                        </h3>
                                    </div>

                                    <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span>

                                </div>



                                <div class="day-label faq-answer" style="display: none;">
                                    <!-- Add tags for this day -->
                                    <div class="day-tags">
                                        <?php
                                        // Get tags for this day if they exist
                                        if (isset($day['day_tags']) && is_array($day['day_tags'])) {
                                            foreach ($day['day_tags'] as $tag) {
                                                echo '<span class="day-tag">' . esc_html($tag) . '</span>';
                                            }
                                        } else {
                                            // Default tags if none are set
                                            echo '<span class="day-tag">Breakfast</span>';
                                            echo '<span class="day-tag">Lunch</span>';
                                            echo '<span class="day-tag">Dinner</span>';
                                        }
                                        ?>
                                    </div>
                                    <div class="entry-content">

                                        <p style="font-size: 19px;">
                                            <?php echo ($day['day_label']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .day-tags {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 8px;
                                    margin: 10px 0;
                                }

                                .day-tag {
                                    background-color: #f0f0f0;
                                    color: #333;
                                    padding: 4px 12px;
                                    border-radius: 16px;
                                    font-size: 12px;
                                    font-weight: 500;
                                    display: inline-block;
                                    border: 1px solid #ddd;
                                }

                                .day-tag:hover {
                                    background-color: #e0e0e0;
                                }
                            </style>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No itinerary available for this tour package.</p>';
                }
                ?>
            </div>
        </div>


        <!-- Hotels Section -->
        <section class="hotel-section" id="hotels">
            <div class="hotel-div">
                <h1>Hotels</h1>

                <div class="hotel-cards-container">
                    <?php
                    $hotels = maybe_unserialize(get_post_meta(get_the_ID(), 'hotels', true));
                    if (!empty($hotels) && is_array($hotels)) {
                        foreach ($hotels as $hotel) {
                            ?>
                            <div class="hotel-card" style="border: 2px solid;"
                                onclick="showHotelPopup('<?php echo esc_url($hotel['image']); ?>', '<?php echo esc_html($hotel['name']); ?>', '<?php echo esc_html($hotel['address']); ?>', '<?php echo intval($hotel['rating']); ?>')">
                                <!-- Hotel Image -->
                                <img src="<?php echo esc_url($hotel['image']); ?>" alt="<?php echo esc_attr($hotel['name']); ?>"
                                    class="hotel-image" />

                                <!-- Hotel Details -->
                                <div class="hotel-details">
                                    <h2><?php echo esc_html($hotel['name']); ?></h2>

                                    <!-- Star Rating -->
                                    <div class="star-rating">
                                        <?php
                                        $rating = intval($hotel['rating']);
                                        for ($i = 0; $i < 5; $i++) {
                                            echo $i < $rating ? '⭐' : '☆';
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    // Extract city from the address
                                    $address_parts = explode(',', $hotel['address']);
                                    $city = count($address_parts) > 1 ? $address_parts[count($address_parts) - 2] : $address_parts[0]; // Show the word if only one part
                                    ?>
                                    <p><?php echo esc_html($city); ?></p>
                                </div>
                            </div>

                            <!-- Hotel Popup Modal -->
                            <div id="hotel-popup" class="hotel-popup" onclick="closeHotelPopup(event)">
                                <div class="hotel-popup-content">
                                    
                                    <button class="hotel-a-prev" onclick="changeHotel(-1)">❮</button>
                                    <img id="hotel-popup-img" src="" alt="Hotel Image" class="hotel-popup-img">
                                    <button class="hotel-a-next" onclick="changeHotel(1)">❯</button>
                                    <h2 id="hotel-popup-name"></h2>
                                    <div id="hotel-popup-rating" class="star-rating" style="justify-content: space-around;">
                                    </div>
                                    <p id="hotel-popup-address"></p>
                                </div>
                            </div>

                            <script>
                                let currentHotelIndex = 0;
                                const hotels = <?php echo json_encode($hotels); ?>;

                                function showHotelPopup(image, name, address, rating) {
                                    document.getElementById('hotel-popup-img').src = image;
                                    document.getElementById('hotel-popup-name').textContent = name;
                                    document.getElementById('hotel-popup-address').textContent = address;

                                    const ratingContainer = document.getElementById('hotel-popup-rating');
                                    ratingContainer.innerHTML = '';
                                    for (let i = 0; i < 5; i++) {
                                        ratingContainer.innerHTML += i < rating ? '⭐' : '☆';
                                    }

                                    document.getElementById('hotel-popup').style.display = 'flex';
                                }

                                function closeHotelPopup(event) {
                                    if (event.target.id === 'hotel-popup' || event.target.classList.contains('close')) {
                                        document.getElementById('hotel-popup').style.display = 'none';
                                    }
                                }

                                function changeHotel(direction) {
                                    currentHotelIndex += direction;

                                    if (currentHotelIndex >= hotels.length) {
                                        currentHotelIndex = 0;
                                    } else if (currentHotelIndex < 0) {
                                        currentHotelIndex = hotels.length - 1;
                                    }

                                    const hotel = hotels[currentHotelIndex];
                                    showHotelPopup(hotel.image, hotel.name, hotel.address, hotel.rating);
                                }
                            </script>
                            <?php
                        }
                    } else {
                        echo '<p>No hotels available.</p>';
                    }
                    ?>
                </div>
                <p class="note">
                    Note: Click Hotel For See Full Address
                </p>
            </div>
            <div class="container-inc" id="inclusions" class="section">
                <div class="tabs">
                    <div class="tab active" data-target="inclusions-content">Inclusions</div>
                    <div class="tab " data-target="exclusions-content">Exclusions</div>
                </div>

                <div class="content">
                    <div id="inclusions-content" class="tab-content active" style="    border-bottom: 5px solid 
    #20a397;">
                        <ul>
                            <?php
                            $inclusions = maybe_unserialize(get_post_meta(get_the_ID(), 'inclusions', true));
                            if (!empty($inclusions) && is_array($inclusions)) {
                                foreach ($inclusions as $inclusion) {
                                    echo '<li>' . esc_html($inclusion) . '</li>';
                                }
                            } else {
                                // Default inclusions if none are set
                                echo '<li>4 nights stay in Beach Villa</li>';
                                echo '<li>Meal Plan - Full Board</li>';
                                echo '<li>Round trip tickets Delhi-Gan-Delhi (Delhi economy seat in Sri Lankan Airline)</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div id="exclusions-content" class="tab-content " style="    border-bottom: 5px solid 
    #B42D1C;">
                        <ul>
                            <?php
                            $exclusions = maybe_unserialize(get_post_meta(get_the_ID(), 'exclusions', true));
                            if (!empty($exclusions) && is_array($exclusions)) {
                                foreach ($exclusions as $exclusion) {
                                    echo '<li>' . esc_html($exclusion) . '</li>';
                                }
                            } else {
                                // Default exclusions if none are set
                                echo '<li>Return speed boat transfers from Gan International Airport to Resort</li>';
                                echo '<li>Green Tax</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- FAQ Section -->
    <div class="faq-container mob-only" id="faq">
        <h2>FAQs About <?php echo esc_html($package['title']); ?> Tour Packages</h2>
        <?php if (is_array($package['faqs'])): ?>
            <?php foreach ($package['faqs'] as $faq): ?>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span class="icon">Q</span> <?php echo esc_html($faq['question']); ?>
                        <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer" style="display: none;">
                        <p><?php echo esc_html($faq['answer']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="faq-container desktop-only" id="faq">
        <h2>FAQs About <?php echo esc_html($package['title']); ?> Tour Packages</h2>
        <div class="faq-div">
            <div class="faq-ques">
                <?php if (is_array($package['faqs'])): ?>
                    <?php foreach ($package['faqs'] as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <span class="icon">Q</span> <?php echo esc_html($faq['question']); ?>
                                <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                            <div class="faq-answer" style="display: none;">
                                <p><?php echo esc_html($faq['answer']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="faq-img">
                <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/faq-travel-agent.png'); ?>"
                    alt="">
            </div>
        </div>
    </div>
    <div class="fixed-buttons" id="contactButton">
        <?php
        $phone_number = get_option('plugin_phone_number', 'default_phone_number');
        ?>
        <button class="call-btn" onclick="window.location.href='tel:+916376983416'">Call
            Us</button>
        <button class="availability-btn req" onclick="showContactForm()">Request a
            Callback</button>
    </div>
    <!-- show selected_cats packages -->

    <!-- Package Section -->
    <section class="published-package">
        <h2 style="text-align: center;">Recently Published Packages</h2>
        <div class="package-container-wrapper">
            <div class="package-container">
                <?php
                // Get the current post ID to exclude it from the query
                $current_id = get_the_ID();

                // Query arguments for recently published packages
                $recent_packages_args = array(
                    'post_type' => 'tour_package',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'post__not_in' => array($current_id),
                    'orderby' => 'date',
                    'order' => 'DESC',
                );

                $recent_packages_query = new WP_Query($recent_packages_args);

                if ($recent_packages_query->have_posts()):
                    while ($recent_packages_query->have_posts()):
                        $recent_packages_query->the_post();
                        $package_id = get_the_ID();
                        $package_location = get_post_meta($package_id, 'trip_location', true);
                        $package_duration = get_post_meta($package_id, 'duration', true);
                        $package_price = get_post_meta($package_id, 'price', true);
                        ?>
                        <div class="package-card">
                            <a href="<?php the_permalink(); ?>" class="package-link">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($package_id, 'medium'); ?>"
                                        alt="<?php echo esc_attr(get_the_title()); ?>" class="package-image" />
                                <?php endif; ?>

                                <div class="package-details">
                                    <h3 class="package-title"><?php the_title(); ?></h3>
                                    <?php if (!empty($package_location)): ?>
                                        <p class="package-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo esc_html($package_location); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($package_duration)): ?>
                                        <p class="package-meta">
                                            <i class="fas fa-clock"></i>
                                            <?php echo esc_html($package_duration); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($package_price)): ?>
                                        <p class="package-price">
                                            <i class="fas fa-rupee-sign"></i>
                                            <?php echo number_format(floatval($package_price)); ?>/-
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else: ?>
                    <p class="no-packages">No recently published packages available.</p>
                <?php endif; ?>
            </div>

            <!-- Navigation Arrows -->
            <button class="scroll-arrow scroll-left" aria-label="Scroll Left">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-arrow scroll-right" aria-label="Scroll Right">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.querySelector('.package-container');
            const leftArrow = document.querySelector('.scroll-left');
            const rightArrow = document.querySelector('.scroll-right');

            function checkScroll() {
                if (!container || !leftArrow || !rightArrow) return;

                const hasHorizontalScroll = container.scrollWidth > container.clientWidth;
                const maxScroll = container.scrollWidth - container.clientWidth;

                if (hasHorizontalScroll) {
                    // Show/hide left arrow with class for animation
                    leftArrow.classList.toggle('visible', container.scrollLeft > 0);
                    // Show/hide right arrow with class for animation
                    rightArrow.classList.toggle('visible', container.scrollLeft < maxScroll);
                }
            }

            leftArrow.addEventListener('click', () => {
                container.scrollBy({
                    left: -container.offsetWidth / 3,
                    behavior: 'smooth'
                });
            });

            rightArrow.addEventListener('click', () => {
                container.scrollBy({
                    left: container.offsetWidth / 3,
                    behavior: 'smooth'
                });
            });

            // Throttle scroll event for better performance
            let scrollTimeout;
            container.addEventListener('scroll', () => {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(() => {
                        checkScroll();
                        scrollTimeout = null;
                    }, 100);
                }
            });

            window.addEventListener('resize', checkScroll);
            checkScroll();
        });
    </script>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.querySelector('.package-container');
            const leftArrow = document.querySelector('.scroll-left');
            const rightArrow = document.querySelector('.scroll-right');

            function checkScroll() {
                if (!container || !leftArrow || !rightArrow) return;

                const hasHorizontalScroll = container.scrollWidth > container.clientWidth;
                const maxScroll = container.scrollWidth - container.clientWidth;

                if (hasHorizontalScroll) {
                    // Show/hide left arrow with class for animation
                    leftArrow.classList.toggle('visible', container.scrollLeft > 0);
                    // Show/hide right arrow with class for animation
                    rightArrow.classList.toggle('visible', container.scrollLeft < maxScroll);
                }
            }

            leftArrow.addEventListener('click', () => {
                container.scrollBy({
                    left: -container.offsetWidth / 3,
                    behavior: 'smooth'
                });
            });

            rightArrow.addEventListener('click', () => {
                container.scrollBy({
                    left: container.offsetWidth / 3,
                    behavior: 'smooth'
                });
            });

            // Throttle scroll event for better performance
            let scrollTimeout;
            container.addEventListener('scroll', () => {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(() => {
                        checkScroll();
                        scrollTimeout = null;
                    }, 100);
                }
            });

            window.addEventListener('resize', checkScroll);
            checkScroll();
        });
    </script>
    <!-- Google Reviews Section -->
    <section class="google-reviews">
        <h2>What Our Guests Say</h2>
        <div class="reviews-container">
            <!-- Original reviews -->
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Meera Kanneri</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "We had a fantastic experience with the Kingsland Holidays while planning our Rajasthan trip! A big
                    thanks to Rohit for considering all our requirements and arranging everything perfectly, especially
                    while traveling with elderly family members"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Apoorv Sinha</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "Had a wonderful time in Rajasthan with Kingsland Holiday. They planned everything perfectly and
                    according to my needs - the trip covered all the places in Jodhpur, Jaisalmer and Udaipur. Special
                    mention of our cab driver Mr. Dara Singh ji who added that…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Jaideep singh Jatain</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "We had an amazing trip to Gujarat at Statue of Unity and all the thanks to Kingsland Holiday for
                    that. Our trip was planned by Mr. Mohit who remotely assisted us in best way possible, was always
                    available at any hour of the day and made sure of everything requirement..."
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>

            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Payal Malekar</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "I want to thank you for providing us with a wonderful travel experience with Kingsland holidays .
                    Everything was planned and organized perfectly, and we enjoyed the trip immensely. It was one of the
                    best trips we ever had!

                    Our driver Sunder Singh was very helpful throughout the journey…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Nitisha Chauhan</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "Had a wonderful experience with Kingsland holiday travel agency…. They have made our two dham trip
                    so comfortable that even our 6 month old baby was also enjoying d trip….. they have give every
                    minute details regarding d trip n it made me easy to carry my child…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <!-- Duplicate reviews for smooth infinite scroll -->
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Meera Kanneri</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "We had a fantastic experience with the Kingsland Holidays while planning our Rajasthan trip! A big
                    thanks to Rohit for considering all our requirements and arranging everything perfectly, especially
                    while traveling with elderly family members."
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Apoorv Sinha</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "Had a wonderful time in Rajasthan with Kingsland Holiday. They planned everything perfectly and
                    according to my needs - the trip covered all the places in Jodhpur, Jaisalmer and Udaipur. Special
                    mention of our cab driver Mr. Dara Singh ji who added that…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Jaideep singh Jatain</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "We had an amazing trip to Gujarat at Statue of Unity and all the thanks to Kingsland Holiday for
                    that. Our trip was planned by Mr. Mohit who remotely assisted us in best way possible, was always
                    available at any hour of the day and made sure of everything requirement..."
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Payal Malekar</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "I want to thank you for providing us with a wonderful travel experience with Kingsland holidays .
                    Everything was planned and organized perfectly, and we enjoyed the trip immensely. It was one of the
                    best trips we ever had!

                    Our driver Sunder Singh was very helpful throughout the journey…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-name">Nitisha Chauhan</div>
                    <div class="review-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <p class="review-text">
                    "Had a wonderful experience with Kingsland holiday travel agency…. They have made our two dham trip
                    so comfortable that even our 6 month old baby was also enjoying d trip….. they have give every
                    minute details regarding d trip n it made me easy to carry my child…"
                </p>
                <div class="review-header">
                    <div></div>
                    <img height="30" width="30"
                        src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/revision.png'); ?>"
                        alt="Google Review">
                </div>
            </div>
        </div>

    </section>

    <section class="published-package destination-package">
        <?php
        // Get the saved destinations from the post meta
        $destinations = get_post_meta(get_the_ID(), 'destinations', true);

        if (!empty($destinations) && is_array($destinations)) {
            ?>
            <div class="package-container-wrapper">
                <h2 style="text-align:center">Destinations</h2>
                <div class="package-container destination-container">
                    <?php foreach ($destinations as $destination) { ?>
                        <div class="package-card destination-card">
                            <a href="<?php echo esc_url($destination['destination_url']); ?>" target="_blank">
                                <?php if (!empty($destination['image'])) { ?>
                                    <img src="<?php echo esc_url($destination['image']); ?>" class="package-image"
                                        alt="<?php echo esc_attr($destination['name']); ?>">
                                    <div class="">
                                        <h3 style="text-align:center;"><?php echo esc_html($destination['name']); ?></h3>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <span class="show-only-mob">
                    <button class="destination-class-arrow scroll-left" onclick="scrollDestinations(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="destination-class-arrow scroll-right" onclick="scrollDestinations(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </span>
            </div>
            <?php
        }
        ?>
    </section>

    <script>
        function scrollDestinations(direction) {
            const container = document.querySelector('.destination-container');
            const scrollAmount = container.offsetWidth / 2; // Adjust the scroll amount as needed
            container.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }
        // Hide arrows if no scroll is needed
        const checkScrollDestinations = () => {
            const container = document.querySelector('.destination-container');
            const leftArrow = document.querySelector('.destination-class-arrow.scroll-left');
            const rightArrow = document.querySelector('.destination-class-arrow.scroll-right');

            const canScrollLeft = container.scrollLeft > 0;
            const canScrollRight = container.scrollLeft < container.scrollWidth - container.clientWidth;

            leftArrow.style.display = canScrollLeft ? 'flex' : 'none';
            rightArrow.style.display = canScrollRight ? 'flex' : 'block';
        };

        const destinationContainer = document.querySelector('.destination-container');
        destinationContainer.addEventListener('scroll', checkScrollDestinations);
        window.addEventListener('resize', checkScrollDestinations);
        checkScrollDestinations(); // Initial check
    </script>


    <style>
        .destination-container {
            flex-wrap: wrap;
        }

        .show-only-mob {
            display: none;
        }

        .destination-class-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .destination-class-arrow.scroll-left {
            left: 0;
        }

        .destination-class-arrow.scroll-right {
            right: 0;
        }

        .destination-info {
            padding: 16px;
            background-color: #fff;
        }

        .destination-info h3 {
            margin-top: 0;
            font-size: 18px;
            font-weight: 600;
        }

        /* Container Styles */
        .published-package {
            width: 80%;
            margin: auto;
            padding: 40px 0;
            font-family: 'Merriweather', serif;
            position: relative;
        }

        .package-container-wrapper {
            position: relative;
            overflow: hidden;

        }

            {
            display: flex;
            flex-direction: row;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */
            padding: 20px 0;
        }

        /* Hide scrollbar */
        .package-container::-webkit-scrollbar {
            display: none;
        }

        /* Card Styles */
        .package-container .package-card {
            flex: 0 0 calc(25% - 15px);
            /* Show 4 cards */
            min-width: 280px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Scroll Arrows */
        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .scroll-left {
            left: 0;
        }

        .scroll-right {
            right: 0;
        }

        /* Rest of your existing styles */
        .package-link {
            text-decoration: none;
            color: inherit;
        }

        .package-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .package-details {
            padding: 15px;
        }

        .package-title {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #333;
        }

        .package-meta {
            margin: 5px 0;
            color: #666;
        }

        .package-price {
            margin: 10px 0 0 0;
            font-size: 20px;
            font-weight: bold;
            color: #20a397;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .package-card {
                flex: 0 0 calc(33.333% - 14px);
                /* Show 3 cards */
            }
        }

        @media (max-width: 992px) {
            .package-card {
                flex: 0 0 calc(50% - 10px);
                /* Show 2 cards */
            }
        }

        @media (max-width: 576px) {
            .package-card {
                flex: 0 0 calc(100% - 10px);
                /* Show 1 card */
            }

            .published-package {
                width: 95%;
            }

            .destination-container {
                flex-wrap: nowrap;
            }

            .show-only-mob {
                display: block;
            }
        }
    </style>
    <script>
        function toggleTaq(element) {
            const dayLabel = element.nextElementSibling;
            const dayTagsInTitle = element.querySelectorAll('.day-title .day-tags');
            const arrow = element.querySelector('.arrow i');

            if (dayLabel.style.display === 'none' || dayLabel.style.display === '') {
                dayLabel.style.display = 'block';
                dayTagsInTitle.forEach(tag => tag.style.display = 'none');
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-up');
            } else {
                dayLabel.style.display = 'none';
                dayTagsInTitle.forEach(tag => tag.style.display = 'inline');
                arrow.classList.remove('fa-chevron-up');
                arrow.classList.add('fa-chevron-down');
            }
        }
    </script>
    <script>
        // < !-- Add this JavaScript for scroll arrows functionality -->
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.querySelector('.package-container');
            const leftArrow = document.querySelector('.scroll-left');
            const rightArrow = document.querySelector('.scroll-right');

            if (leftArrow && rightArrow) {
                leftArrow.addEventListener('click', () => {
                    container.scrollBy({
                        left: -300,
                        behavior: 'smooth'
                    });
                });

                rightArrow.addEventListener('click', () => {
                    container.scrollBy({
                        left: 300,
                        behavior: 'smooth'
                    });
                });

                // Hide arrows if no scroll is needed
                const checkScroll = () => {
                    const canScrollLeft = container.scrollLeft > 0;
                    const canScrollRight = container.scrollLeft < container.scrollWidth - container.clientWidth;

                    leftArrow.style.display = canScrollLeft ? 'flex' : 'none';
                    rightArrow.style.display = canScrollRight ? 'flex' : 'none';
                }

                    ;

                container.addEventListener('scroll', checkScroll);
                window.addEventListener('resize', checkScroll);
                checkScroll(); // Initial check
            }
        });

    </script>
    <script>function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const arrow = element.querySelector('.arrow');

            if (answer.style.display === 'none' || !answer.style.display) {
                answer.style.display = 'block';
                arrow.classList.add('active');
            }

            else {
                answer.style.display = 'none';
                arrow.classList.remove('active');
            }
        }

        // Optional: Add smooth scrolling when new day is opened
        document.querySelectorAll('.toggleitDaY').forEach(toggle => {
            toggle.addEventListener('click', function () {
                setTimeout(() => {
                    const dayElement = this.parentElement;
                    const container = document.querySelector('.itinerary-scroll-container');
                    const dayRect = dayElement.getBoundingClientRect();
                    const containerRect = container.getBoundingClientRect();

                    if (dayRect.bottom > containerRect.bottom) {
                        container.scrollTo({
                            top: container.scrollTop + (dayRect.bottom - containerRect.bottom),
                            behavior: 'smooth'
                        });
                    }
                }

                    , 100);
            });
        });

        // 
        document.addEventListener("DOMContentLoaded", function () {
            var mobileTabs = document.querySelectorAll(".mobile-tabs a");
            var sections = [];

            mobileTabs.forEach(function (tab) {
                var sectionId = tab.getAttribute("href").substring(1);
                var section = document.getElementById(sectionId);

                if (section) {
                    sections.push(section);
                }

                // Add click event to scroll to the section
                tab.addEventListener("click", function (e) {
                    e.preventDefault();

                    window.scrollTo({
                        top: section.offsetTop,
                        behavior: "smooth"
                    });
                });
            });

            function setActiveTab() {
                var scrollPosition = window.scrollY + window.innerHeight / 2;

                sections.forEach(function (section, index) {
                    if (scrollPosition >= section.offsetTop && scrollPosition < section.offsetTop + section.offsetHeight) {
                        mobileTabs.forEach(function (tab) {
                            tab.classList.remove("active");
                        });
                        mobileTabs[index].classList.add("active");

                        // Scroll the mobile-tabs element to keep the active tab in view


                        var activeTab = mobileTabs[index];
                        var tabsContainer = document.querySelector(".mobile-tabs");
                        var tabOffsetLeft = activeTab.offsetLeft;
                        var tabWidth = activeTab.offsetWidth;
                        var containerScrollLeft = tabsContainer.scrollLeft;
                        var containerWidth = tabsContainer.offsetWidth;

                        if (tabOffsetLeft < containerScrollLeft || tabOffsetLeft + tabWidth > containerScrollLeft + containerWidth) {
                            tabsContainer.scrollTo({
                                left: tabOffsetLeft - containerWidth / 2 + tabWidth / 2,
                                behavior: "smooth"
                            });
                        }
                    }
                });
            }

            window.addEventListener("scroll", setActiveTab);
            setActiveTab(); // Initial call to set the active tab on page load
        });
        // end
        let currentSlide = 0;
        const slides = document.getElementsByClassName("slide");

        // Show initial slide
        showSlide(currentSlide);

        function changeSlide(direction) {
            showSlide(currentSlide += direction);
        }

        function showSlide(n) {

            // Handle wraparound
            if (n >= slides.length) {
                currentSlide = 0;
            }

            else if (n < 0) {
                currentSlide = slides.length - 1;
            }

            else {
                currentSlide = n;
            }

            // Hide all slides
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
            }

            // Show current slide
            slides[currentSlide].classList.add("active");
        }

    </script>
    <script>
        function toggleContactForm() {
            var contactButton = document.getElementById('contactButton');
            var contactForm = document.getElementById('contactForm');

            if (contactForm.style.display === 'none' || contactForm.style.display === '') {
                contactForm.style.display = 'block';
                contactButton.style.display = 'none';
            }

            else {
                contactForm.style.display = 'none';
                contactButton.style.display = 'block';
            }
        }

        const nav = document.querySelector('.mob-tab-sticky');
        let navTop = nav.offsetTop;

        function handleSticky() {
            if (window.scrollY >= navTop) {
                nav.classList.add('is-sticky');
            }

            else {
                nav.classList.remove('is-sticky');
            }
        }

        window.addEventListener('scroll', handleSticky);

        window.addEventListener('resize', () => {
            navTop = nav.offsetTop; // Recalculate on resize
        });

    </script>
    <script>function toggleFaq(element) {
            var answer = element.nextElementSibling;
            var arrow = element.querySelector('.arrow');

            // Toggle between displaying and hiding the answer
            if (answer.style.display === "none" || answer.style.display === "") {
                answer.style.display = "block";
                arrow.innerHTML = '<i class="fa-solid fa-chevron-up"></i>'; // Arrow changes to up
            }

            else {
                answer.style.display = "none";
                arrow.innerHTML = '<i class="fa-solid fa-chevron-down"></i>'; // Arrow changes to down
            }
        }

    </script>
    <script> // Add click event to all tab links

        document.querySelectorAll(".tab-item").forEach((item) => {
            item.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default link behavior

                // Scroll to the content section (vertically)
                const targetId = this.getAttribute("href"); // Get the section ID
                const targetElement = document.querySelector(targetId); // Find the section

                targetElement.scrollIntoView({
                    behavior: "smooth", // Smooth vertical scrolling
                    block: "start" // Scroll to the top of the section
                });

                // Scroll the tabs horizontally to make the clicked tab visible
                const tabsContainer = document.getElementById("tabs");
                const tabWidth = this.offsetWidth;
                const tabPosition = this.offsetLeft;

                // Calculate the amount of scroll required to bring the tab into view
                const scrollPosition = tabPosition - (tabsContainer.offsetWidth - tabWidth) / 2;

                // Scroll the tabs container horizontally to center the clicked tab
                tabsContainer.scroll({
                    left: scrollPosition,
                    behavior: "smooth"
                });
            });
        });

    </script>
    <script>
        function toggleReadMore(link) {
            var content = link.closest('.package-detailTOView').querySelector('.read-more-content');

            if (content.style.display === "none") {
                content.style.display = "block";
                link.textContent = "Read Less"; // Change the link text
            }

            else {
                content.style.display = "none";
                link.textContent = "Read More"; // Change the link text back
            }
        }

        function toggleContent(button) {
            const contentWrapper = button.parentElement;
            const preview = contentWrapper.querySelector('.content-preview');
            const fullContent = contentWrapper.querySelector('.content-full');

            if (fullContent.style.display === 'none') {
                preview.style.display = 'none';
                fullContent.style.display = 'block';
                button.textContent = 'Read Less';
            }

            else {
                preview.style.display = 'block';
                fullContent.style.display = 'none';
                button.textContent = 'Read More';
            }
        }

    </script>
    <style>
        .tab-content {
            display: none;
            /* Hide all tab contents by default */
        }

        .tab-content.active {
            display: block;
            /* Show active tab content */
        }
    </style>

    <script>
        // JavaScript for tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active class from all tabs and contents
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to the clicked tab
                this.classList.add('active');

                // Show the corresponding content
                const target = this.getAttribute('data-target');
                document.getElementById(target).classList.add('active');
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const contentWrapper = document.querySelector('.content-wrapper');
            const readMoreBtn = document.querySelector('.read-more-btn');

            // Hide button if content is shorter than max-height
            if (contentWrapper.scrollHeight <= contentWrapper.clientHeight) {
                readMoreBtn.classList.add('hidden');
            }

            readMoreBtn.addEventListener('click', function () {
                contentWrapper.classList.toggle('expanded');

                if (contentWrapper.classList.contains('expanded')) {
                    readMoreBtn.textContent = 'Show Less';
                } else {
                    readMoreBtn.textContent = 'Read More';
                    // Smooth scroll back to top of content if needed
                    contentWrapper.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            // Select all content wrappers and read more buttons
            const dayElements = document.querySelectorAll('.day');

            dayElements.forEach(dayElement => {
                const contentWrapper = dayElement.querySelector('.content-wrapper');
                const readMoreBtn = dayElement.querySelector('.read-more-btn');

                if (contentWrapper && readMoreBtn) {
                    // Initially check if we need the read more button
                    if (contentWrapper.scrollHeight <= contentWrapper.clientHeight) {
                        readMoreBtn.classList.add('hidden');
                    }

                    // Add click event listener to each button
                    readMoreBtn.addEventListener('click', function () {
                        contentWrapper.classList.toggle('expanded');

                        if (contentWrapper.classList.contains('expanded')) {
                            readMoreBtn.textContent = 'Show Less';
                        } else {
                            readMoreBtn.textContent = 'Read More';
                            // Smooth scroll back to the start of this specific content
                            contentWrapper.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
            });
        });

        // 
        // Show modal
        function showContactForm() {
            document.querySelector(".fromM").classList.add("show-modal");
            document.querySelector(".modal-overlay").classList.add("show-modal");
        }

        // Hide modal
        function hideContactForm() {
            document.querySelector(".fromM").classList.remove("show-modal");
            document.querySelector(".modal-overlay").classList.remove("show-modal");
        }

        // Event listeners
        document.querySelector(".close-btn").addEventListener("click", hideContactForm);
        document
            .querySelector(".modal-overlay")
            .addEventListener("click", hideContactForm);
    </script>

    <?php get_footer(); ?>
</body>


</html>