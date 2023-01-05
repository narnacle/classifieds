<?php
/**
 * Plugin Name: Classifieds
 * Plugin URI: https://narnacle.com/classifieds
 * Description: A simple plugin that adds a classifieds section to your WordPress site.
 * Version: 1.0
 * Author: Narnacle Digital
 * Author URI: https://narnacle.com
 * License: GPL2
 */

// Register the classifieds custom post type
function classifieds_register_post_type() {
  register_post_type('classified', array(
    'labels' => array(
      'name' => 'Classifieds',
      'singular_name' => 'Classified',
    ),
    'public' => true,
    'has_archive' => true,
  ));
}
add_action('init', 'classifieds_register_post_type');

// Create the page template for displaying classified ads
function classifieds_page_template($template) {
  if (is_page('classifieds')) {
    $new_template = plugin_dir_path(__FILE__) . 'page-classifieds.php';
    if ('' != $new_template) {
      return $new_template ;
    }
  }
  return $template;
}
add_filter('template_include', 'classifieds_page_template', 99);

// Display the classified ads
function classifieds_display_classifieds() {
  // Load the classified ads
  $classifieds = get_posts(array(
    'post_type' => 'classified',
    'posts_per_page' => -1,
  ));

  // Display the classified ads
  if ($classifieds) {
    foreach ($classifieds as $classified) {
      ?>
      <div class="classified">
        <h3><?php echo get_the_title($classified); ?></h3>
        <div class="classified-content">
          <?php echo apply_filters('the_content', $classified->post_content); ?>
        </div>
      </div>
      <?php
    }
  } else {
    ?>
    <p>There are no classified ads to display.</p>
    <?php
  }
}

// Display the classified submission form
function classifieds_display_form() {
  // Check if the form has been submitted
  if (isset($_POST['submit_classified'])) {
    // Create a new classified ad
    $classified_id = wp_insert_post(array(
      'post_type' => 'classified',
      'post_title' => $_POST['title'],
      'post_content' => $_POST['description'],
      'post_status' => 'pending',
    ));

    // Redirect to the classified ad page
    wp_redirect(get_permalink($classified_id));
    exit;
  }

  // Display the form
  ?>
  <form method="post">
    <p>
      <label for="title">Title:</label>
    <p>
      <label for="description">Description:</label>
      <textarea name="description" id="description"></textarea>
    </p>
    <p>
      <input type="submit" name="submit_classified" value="Submit Classified" />
    </p>
  </form>
  <?php
}

// Hook everything up
function classifieds_init() {
  add_shortcode('classifieds', 'classifieds_display_classifieds');
  add_shortcode('classifieds_form', 'classifieds_display_form');
}
add_action('init', 'classifieds_init');
