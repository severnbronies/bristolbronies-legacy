<?php

/**
 * THEME CODE NAME 
 *
 * This is used utterly everywhere, so don't go changing it
 * unless it's a new version or something.
 */

define('BB_VERSION', 'gardner');

/**
 * Register post thumbnails
 */

add_theme_support('post-thumbnails');

/**
 * Register navigation menus
 */

register_nav_menus(array('primary' => 'Main navigation'));
register_nav_menus(array('secondary' => 'Secondary navigation'));
register_nav_menus(array('footer' => 'Footer navigation'));
register_nav_menus(array('social' => 'Social links'));

/**
 * Register sidebars
 */

register_sidebar(array('name' => 'Main sidebar', 'id' => 'primary'));

/**
 * Enable HTML5 output
 */

add_theme_support('html5', array('comment-list', 'comment-form', 'search-form'));

/**
 * Prettify search URLs because WordPress doesn't do that
 */

function bb_search_url_rewrite_rule() {
  if(is_search() && !empty($_GET['s'])) {
    wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
    exit();
  } 
}
add_action('template_redirect', 'bb_search_url_rewrite_rule');

/**
 * Highlight search terms
 */

function bb_search_highlight_term($term, $string) {
  $keys = explode(" ", $term);
  $string = preg_replace('/('.implode('|', $keys) .')/iu', '<mark>\0</mark>', $string);
  return $string;
}

/**
 * Better excerpts 
 */

function bb_excerpts($text, $raw_excerpt) {
  if(!$raw_excerpt) {
    $content = apply_filters('the_content', strip_shortcodes(get_the_content()));
    $text = substr($content, 0, strpos($content, '</p>') + 4);
  }
  $text = preg_replace("/<img[^>]+\>/i", "", $text); 
  return $text;
}
add_filter('wp_trim_excerpt', 'bb_excerpts', 10, 2);

/** 
 * Get custom fields 
 */

function bb_custom_field($field_name, $id = false) {
  if(strlen(get_field($field_name, $id)) > 0) {
    return get_field($field_name, $id);
  }
  else {
    return false;
  }
}

/**
 * Meet post type
 */

function bb_meet_post_type() {
  $labels = array(
    'name' => _x('Meets', 'post type general name'),
    'singular_name' => _x('Meet', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Meet'),
    'edit_item' => __('Edit Meet'),
    'new_item' => __('New Meet'),
    'all_items' => __('All Meets'),
    'view_item' => __('View Meet'),
    'search_items' => __('Search Meets'),
    'not_found' => __('No meets found'),
    'not_found_in_trash' => __('No meets found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Meets'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains the meets that we hold.',
    'public' => true,
    'menu_position' => 7,
    'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'author'),
    'taxonomies' => array('category'),
    'has_archive' => true
  );
  register_post_type('meet', $args);
}
add_action('init', 'bb_meet_post_type');

function bb_meet_time_column_title($defaults) {
  $defaults['meet_time'] = "Running Time";
  return $defaults;
}
add_filter('manage_meet_posts_columns', 'bb_meet_time_column_title', 10);

function bb_meet_time_column_content($column_name, $post_id) {
  if($column_name == "meet_time") {
    echo bb_meet_dates(get_field("meet_start_time", $post_id), get_field("meet_end_time", $post_id));
  }
}
add_filter('manage_meet_posts_custom_column', 'bb_meet_time_column_content', 10, 2);

function bb_meet_time_column_sortable($columns) {
  $columns['meet_time'] = 'meet_time';
  return $columns;
}
add_filter('manage_edit-meet_sortable_columns', 'bb_meet_time_column_sortable');

function bb_meet_time_column_orderby($query) {
  $orderby = $query->get('orderby');
  if($orderby == "meet_time") {
    $query->set('meta_key', 'meet_start_time');
    $query->set('orderby', 'meta_value_num');
  }
}
add_action('pre_get_posts', 'bb_meet_time_column_orderby');

function bb_meet_location_column_title($defaults) {
  $defaults['location'] = "Location";
  return $defaults;
}
add_filter('manage_meet_posts_columns', 'bb_meet_location_column_title', 10);

function bb_meet_location_column_content($column_name, $post_id) {
  if($column_name == "location") {
    $meet_location = get_field("meet_location", $post_id);
    for($i = 0; $i < count($meet_location); $i++) {
      $address = get_field("location_address", $meet_location[$i]);
      echo get_the_title($meet_location[$i]) . "<br>" . $address['address'];
      if(!empty($meet_location[$i+1])) { echo "<br>"; }
    }
  }
}
add_filter('manage_meet_posts_custom_column', 'bb_meet_location_column_content', 10, 2);

function bb_meet_runner_column_title($defaults) {
  $defaults['meet_runner'] = "Meet Runner";
  return $defaults;
}
add_filter('manage_meet_posts_columns', 'bb_meet_runner_column_title', 10);

function bb_meet_runner_column_content($column_name, $post_id) {
  if($column_name == "meet_runner") {
    $meet_runner = get_field("meet_runner", $post_id);
    for($i = 0; $i < count($meet_runner); $i++) {
      echo get_the_title($meet_runner[$i]);
      if(!empty($meet_runner[$i+1])) { echo ", "; }
    }
  }
}
add_filter('manage_meet_posts_custom_column', 'bb_meet_runner_column_content', 11, 2);

/**
 * Location post type
 */

function bb_location_post_type() {
  $labels = array(
    'name' => _x('Locations', 'post type general name'),
    'singular_name' => _x('Location', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Location'),
    'edit_item' => __('Edit Location'),
    'new_item' => __('New Location'),
    'all_items' => __('All Locations'),
    'view_item' => __('View Location'),
    'search_items' => __('Search Locations'),
    'not_found' => __('No locations found'),
    'not_found_in_trash' => __('No locations found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Locations'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains meet locations and venues.',
    'public' => false,
    'menu_position' => 7,
    'supports' => array('title', 'custom-fields', 'author'),
    'has_archive' => false,
    'show_ui' => true,
    'show_in_menu' => true
  );
  register_post_type('location', $args);
}
add_action('init', 'bb_location_post_type');

/**
 * Meet Runner post type
 */

function bb_runner_post_type() {
  $labels = array(
    'name' => _x('Meet Runners', 'post type general name'),
    'singular_name' => _x('Meet Runner', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Meet Runner'),
    'edit_item' => __('Edit Meet Runner'),
    'new_item' => __('New Meet Runner'),
    'all_items' => __('All Meet Runners'),
    'view_item' => __('View Meet Runner'),
    'search_items' => __('Search Meet Runners'),
    'not_found' => __('No meet runners found'),
    'not_found_in_trash' => __('No meet runners found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Meet Runners'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains information about meet runners.',
    'public' => false,
    'menu_position' => 7,
    'supports' => array('title', 'editor', 'custom-fields', 'author'),
    'has_archive' => false,
    'show_ui' => true,
    'show_in_menu' => true
  );
  register_post_type('meet_runner', $args);
}
add_action('init', 'bb_runner_post_type');

function bb_runner_staff_column_title($defaults) {
  $defaults['staff'] = "Staff?";
  return $defaults;
}
add_filter('manage_meet_runner_posts_columns', 'bb_runner_staff_column_title', 10);

function bb_runner_staff_column_content($column_name, $post_id) {
  if($column_name == "staff") {
    $staff_status = get_field("runner_staff", $post_id);
    $staff_status = $staff_status[0];
    if($staff_status == "true") { echo "&#x2714; Yes"; }
  }
}
add_filter('manage_meet_runner_posts_custom_column', 'bb_runner_staff_column_content', 10, 2);

function bb_runner_twitter_profile_shortcode($atts, $content = null) {
  extract(shortcode_atts(array(
    'username' => false
  ), $atts));
  if(!empty($username)) {
    
    require_once('functions/TwitterAPIExchange.php');
    
    $settings = array(
      'oauth_access_token'        => TWITTER_OAUTH_TOKEN,
      'oauth_access_token_secret' => TWITTER_OAUTH_TOKEN_SECRET,
      'consumer_key'              => TWITTER_CONSUMER_KEY,
      'consumer_secret'           => TWITTER_CONSUMER_SECRET
    );
    
    $url = 'https://api.twitter.com/1.1/users/show.json';
    $getfield = '?screen_name=' . $username;
    
    $twitter = new TwitterAPIExchange($settings);
    $return = $twitter->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest();

    if(!empty($return)) {
      $return = json_decode($return);
      $output = $return->description;
    }

  }
  else {
    $output = "No username here.";
  }
  return $output; 
}

function bb_runner_twitter_profile_register_shortcode() {
  add_shortcode('profile', 'bb_runner_twitter_profile_shortcode');
}
add_action('init', 'bb_runner_twitter_profile_register_shortcode');

/**
 * Meet category list
 */

function bb_meet_category($post_id, $format = 'name') {
  $categories = get_the_category($post_id);
  $output = array();
  foreach($categories as $category) {
    switch($format) {
      case 'name': 
        $output[] = $category->name;
        break;
      case 'slug':
        $output[] = $category->slug;
        break;
    }
  }
  return $output;
}

/**
 * Meet start/end time
 */

function bb_meet_dates($start, $end) {
  $output = '<time class="dtstart" datetime="'.date("c", $start).'">'.date("jS F Y, g:ia", $start).'</time>&ndash;';
  if(date("Ymd", $start) === date("Ymd", $end)) {
    $output .= '<time class="dtend" datetime="'.date("c", $end).'">'.date("g:ia", $end).'</time>';
  }
  else {
    $output .= '<time class="dtend" datetime="'.date("c", $end).'">'.date("jS F Y, g:ia", $end).'</time>';
  }
  return $output;
}

/**
 * Meet location
 */

function bb_meet_location($id, $format = 'address') {
  $name = get_the_title($id[0]);
  $data = get_field("location_address", $id[0]);
  switch($format) {
    case 'address':
      $output = $name . ', ' . $data['address'];
      break;
    case 'latitude':
      $output = $data['lat']; 
      break;
    case 'longitude':
      $output = $data['lng'];
      break;
    case 'latlng':
      $output = $data['lat'] . ',' . $data['lng'];
      break;
  }
  return $output;
}

/**
 * Meet RSS feeds
 */

function bb_meet_feed_rss2($for_comments) {
  $rss_template = get_template_directory() . '/feeds/feed-meet-rss2.php';
  if(get_query_var('post_type') == 'meet' && file_exists($rss_template)) {
    load_template($rss_template);
  }
  else {
    do_feed_rss2($for_comments); 
  }
}

remove_all_actions('do_feed_rss2');
add_action('do_feed_rss2', 'bb_meet_feed_rss2', 10, 1);

function bb_meet_feed_rss($for_comments) {
  $rss_template = get_template_directory() . '/feeds/feed-meet-rss.php';
  if(get_query_var('post_type') == 'meet' && file_exists($rss_template)) {
    load_template($rss_template);
  }
  else {
    do_feed_rss2($for_comments); 
  }
}

remove_all_actions('do_feed_rss');
add_action('do_feed_rss', 'bb_meet_feed_rss', 10, 1);

function bb_meet_feed_atom($for_comments) {
  $atom_template = get_template_directory() . '/feeds/feed-meet-atom.php';
  if(get_query_var('post_type') == 'meet' && file_exists($atom_template)) {
    load_template($atom_template);
  }
  else {
    do_feed_rss2($for_comments); 
  }
}

remove_all_actions('do_feed_atom');
add_action('do_feed_atom', 'bb_meet_feed_atom', 10, 1);

function bb_meet_feed_rdf($for_comments) {
  $rdf_template = get_template_directory() . '/feeds/feed-meet-rdf.php';
  if(get_query_var('post_type') == 'meet' && file_exists($rdf_template)) {
    load_template($rdf_template);
  }
  else {
    do_feed_rss2($for_comments); 
  }
}

remove_all_actions('do_feed_rdf');
add_action('do_feed_rdf', 'bb_meet_feed_rdf', 10, 1);

/**
 * Profile bios
 */

function bb_profile_biography($id) {
  $content_post = get_post($id);
  $content = $content_post->post_content;
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}

/**
 * Profile avatars
 */

function bb_profile_avatar($id) {
  if($image = get_field("runner_avatar", $id)) {
    return $image['sizes']['thumbnail'];
  }
  else {
    return "http://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=96";
  }
}

/**
 * News post type
 */

function bb_news_post_type() {
  $labels = array(
    'name' => _x('News', 'post type general name'),
    'singular_name' => _x('News', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add News'),
    'edit_item' => __('Edit News'),
    'new_item' => __('New News'),
    'all_items' => __('All News'),
    'view_item' => __('View News'),
    'search_items' => __('Search News'),
    'not_found' => __('No posts found'),
    'not_found_in_trash' => __('No posts found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'News'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Newsy bloggy thing.',
    'public' => true,
    'menu_position' => 7,
    'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'author'),
    'has_archive' => true
  );
  register_post_type('news', $args);
}
add_action('init', 'bb_news_post_type');


/**
 * Meet Runner post type
 */

function bb_community_post_type() {
  $labels = array(
    'name' => _x('Community', 'post type general name'),
    'singular_name' => _x('Community', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Media'),
    'edit_item' => __('Edit Media'),
    'new_item' => __('New Media'),
    'all_items' => __('All Media'),
    'view_item' => __('View Media'),
    'search_items' => __('Search Media'),
    'not_found' => __('No media found'),
    'not_found_in_trash' => __('No media found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Media'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains photos, videos, text, etc.',
    'public' => true,
    'menu_position' => 7,
    'supports' => array('title', 'editor', 'custom-fields'),
    'has_archive' => true,
    'show_ui' => true,
    'show_in_menu' => true
  );
  register_post_type('community', $args);
}
add_action('init', 'bb_community_post_type');

/**
 * Camera metadata
 */

function community_camera_metadata($imagePath) {

  // Check if the variable is set and if the file itself exists before continuing
  if ((isset($imagePath)) and (file_exists($imagePath))) {
  
    // There are 2 arrays which contains the information we are after, so it's easier to state them both
    $exif_ifd0 = read_exif_data($imagePath ,'IFD0' ,0);       
    $exif_exif = read_exif_data($imagePath ,'EXIF' ,0);
    
    //error control
    $notFound = false;
    
    // Make 
    if (@array_key_exists('Make', $exif_ifd0)) {
      $camMake = $exif_ifd0['Make'];
    } else { $camMake = $notFound; }
    
    // Model
    if (@array_key_exists('Model', $exif_ifd0)) {
      $camModel = $exif_ifd0['Model'];
    } else { $camModel = $notFound; }
    
    // Exposure
    if (@array_key_exists('ExposureTime', $exif_ifd0)) {
      $camExposure = $exif_ifd0['ExposureTime'];
    } else { $camExposure = $notFound; }

    // Aperture
    if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
      $camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
    } else { $camAperture = $notFound; }
    
    // Date
    if (@array_key_exists('DateTime', $exif_ifd0)) {
      $camDate = $exif_ifd0['DateTime'];
    } else { $camDate = $notFound; }
    
    // ISO
    if (@array_key_exists('ISOSpeedRatings',$exif_exif)) {
      $camIso = $exif_exif['ISOSpeedRatings'];
    } else { $camIso = $notFound; }

    // Focal Length
    // if (@array_key_exists('FocalLength',$exif_ifd0)) {
    //   $camFocalLength = $exif_ifd0['FocalLength'];
    // } else { $camFocalLength = $notFound; }

    // GPS data
    if(@array_key_exists('GPSLatitudeRef', $exif_ifd0)) {
      $camLatitude = $exif_ifd0['GPSLatitudeRef'];
    } else { $camLatitude = $notFound; }

    if($camLatitude != false) { 
      if(@array_key_exists('GPSLatitude', $exif_ifd0)) {
        $camLatitudeDegrees = explode("/", $exif_ifd0['GPSLatitude'][0]);
        $camLatitudeSeconds = explode("/", $exif_ifd0['GPSLatitude'][1]);
        $camLatitudeSecondsCalc = ((substr($camLatitudeSeconds[0],0,2)*60)+substr($camLatitudeSeconds[0],2,2));
        $camLatitudeReadableDeg = round(($camLatitudeSecondsCalc / 3600) + $camLatitudeDegrees[0], 6);
        if(strtoupper($camLatitude) == "S") { $camLatitudeReadableDeg = "-".$camLatitudeReadableDeg; }
        $camLatitudeReadableGPS = $camLatitude . " " . $camLatitudeDegrees[0] . "&deg; " . ($camLatitudeSeconds[0]/1000);
      } else { $camLatitudeDegrees = $notFound; $camLatitudeSeconds = $notFound; }
    }

    if(@array_key_exists('GPSLongitudeRef', $exif_ifd0)) {
      $camLongitude = $exif_ifd0['GPSLongitudeRef'];
    } else { $camLongitude = $notFound; }

    if($camLongitude != false) {
      if(@array_key_exists('GPSLongitude', $exif_ifd0)) {
        $camLongitudeDegrees = explode("/", $exif_ifd0['GPSLongitude'][0]);
        $camLongitudeSeconds = explode("/", $exif_ifd0['GPSLongitude'][1]);
        $camLongitudeSecondsCalc = ((substr($camLongitudeSeconds[0],0,2)*60)+substr($camLongitudeSeconds[0],2,2));
        $camLongitudeReadableDeg = round(($camLongitudeSecondsCalc / 3600) + $camLongitudeDegrees[0], 6);
        if(strtoupper($camLongitude) == "W") { $camLongitudeReadableDeg = "-".$camLongitudeReadableDeg; }
        $camLongitudeReadableGPS = $camLongitude . " " . $camLongitudeDegrees[0] . "&deg; " . ($camLongitudeSeconds[0]/1000);
      } else { $camLongitudeDegrees = $notFound; $camLongitudeSeconds = $notFound; }
    }

    $return = array();
    $return['make'] = $camMake;
    $return['model'] = $camModel;
    $return['exposure'] = $camExposure;
    $return['aperture'] = $camAperture;
    $return['date'] = $camDate;
    $return['iso'] = $camIso;
    //$return['focallength'] = $camFocalLength;
    $return['latitude'] = array(
      'ref' => $camLatitude,
      'deg' => $camLatitudeReadableDeg,
      'gps' => $camLatitudeReadableGPS
    );
    $return['longitude'] = array(
      'ref' => $camLongitude,
      'deg' => $camLongitudeReadableDeg,
      'gps' => $camLongitudeReadableGPS
    );
    return $return;
  
  } else {
    return false; 
  } 
}

/**
 * Billboard post type
 */

function bb_billboard_post_type() {
  $labels = array(
    'name' => _x('Billboards', 'post type general name'),
    'singular_name' => _x('Billboard', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Billboard'),
    'edit_item' => __('Edit Billboard'),
    'new_item' => __('New Billboard'),
    'all_items' => __('All Billboards'),
    'view_item' => __('View Billboards'),
    'search_items' => __('Search Billboards'),
    'not_found' => __('No billboards found'),
    'not_found_in_trash' => __('No billboards found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Billboards'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains featured front-page links.',
    'public' => false,
    'menu_position' => 7,
    'supports' => array('title', 'editor', 'custom-fields'),
    'has_archive' => false,
    'show_ui' => true,
    'show_in_menu' => true
  );
  register_post_type('billboard', $args);
}
add_action('init', 'bb_billboard_post_type');

/**
 * Affiliates post type
 */

function bb_affiliates_post_type() {
  $labels = array(
    'name' => _x('Affiliates', 'post type general name'),
    'singular_name' => _x('Affiliate', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Affiliate'),
    'edit_item' => __('Edit Affiliate'),
    'new_item' => __('New Affiliate'),
    'all_items' => __('All Affiliates'),
    'view_item' => __('View Affiliates'),
    'search_items' => __('Search Affiliates'),
    'not_found' => __('No affiliates found'),
    'not_found_in_trash' => __('No affiliates found in the trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Affiliates'
  );
  $args = array(
    'labels' => $labels,
    'description' => 'Contains featured front-page links.',
    'public' => false,
    'menu_position' => 7,
    'supports' => array('title', 'custom-fields'),
    'has_archive' => false,
    'show_ui' => true,
    'show_in_menu' => true
  );
  register_post_type('affiliates', $args);
}
add_action('init', 'bb_affiliates_post_type');

/**
 * WordPress Theme Customizer settings 
 */

function bb_theme_customizer($wp_customize) {
  // Homepage banner image
  $wp_customize->add_section(
    'bb_homepage_banner_options',
    array(
      'title'       => "Default homepage banner",
      'description' => "Upload a new default homepage banner image. This will still be overriden by individual page banners.",
      "priority"    => 201
    )
  );
  $wp_customize->add_setting(
    'bb_homepage_banner_image',
    array(
      'default'     => '',
      'transport'   => 'postMessage'
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Image_Control(
      $wp_customize,
      'bb_homepage_banner_image',
      array(
        'label'     => 'Upload image',
        'section'   => 'bb_homepage_banner_options',
        'settings'  => 'bb_homepage_banner_image'
      )
    )
  );
}
add_action('customize_register', 'bb_theme_customizer');