<?php

  define ('TEMPLATE_DIR', get_template_directory_uri());

  function myThemeScripts() {
		
		wp_enqueue_style('style', TEMPLATE_DIR.'/style.css');
		
		wp_enqueue_script('functions', TEMPLATE_DIR.'/functions.js');
    
	}
  
	add_action('wp_enqueue_scripts', 'myThemeScripts');
  
  function register_header_menu() {
    register_nav_menu('header-menu', __( 'Header Menu' ));
  }
  
  add_action( 'init', 'register_header_menu' );
  
  function create_likes_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'post_likes';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      post_id bigint(20) NOT NULL,
      ip_address varchar(100) NOT NULL,
      vote_type varchar(10) NOT NULL,
      page_url varchar(255) NOT NULL,
      vote_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
  
  add_action('after_setup_theme', 'create_likes_table');
  
  function handle_post_vote() {
    if (isset($_POST['post_id']) && isset($_POST['vote_type'])) {
      global $wpdb;
      $post_id = intval($_POST['post_id']);
      $vote_type = sanitize_text_field($_POST['vote_type']);
      $ip_address = $_SERVER['REMOTE_ADDR'];
      $page_url = esc_url($_POST['page_url']);

      $table_name = $wpdb->prefix . 'post_likes';
      $existing_vote = $wpdb->get_row(
        $wpdb->prepare(
          "SELECT * FROM $table_name WHERE post_id = %d AND ip_address = %s",
          $post_id, $ip_address
        )
      );

      if ($existing_vote) {
        if ($existing_vote->vote_type !== $vote_type) {
          $wpdb->update(
            $table_name,
            array('vote_type' => $vote_type, 'vote_time' => current_time('mysql')),
            array('id' => $existing_vote->id)
          );
        }
      } else {
        $wpdb->insert(
          $table_name,
          array(
            'post_id' => $post_id,
            'ip_address' => $ip_address,
            'vote_type' => $vote_type,
            'page_url' => $page_url,
            'vote_time' => current_time('mysql')
          )
        );
      }

      $likes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote_type = 'like'",
        $post_id
      ));
      $dislikes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote_type = 'dislike'",
        $post_id
      ));

      wp_send_json_success(array('likes' => $likes, 'dislikes' => $dislikes));
    } else {
      wp_send_json_error('Invalid parameters');
    }
  }

  add_action('wp_ajax_post_vote', 'handle_post_vote');

  add_action('wp_ajax_nopriv_post_vote', 'handle_post_vote');
  
  function enqueue_vote_scripts() {
    wp_enqueue_script('vote-script', get_template_directory_uri() . '/functions.js', array(), null, true);
    wp_localize_script('vote-script', 'ajaxObject', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
  }
  
  add_action('wp_enqueue_scripts', 'enqueue_vote_scripts');
  
  function add_likes_admin_page() {
    add_menu_page(
      'Статистика лайков',
      'Лайки и дизлайки',
      'manage_options',
      'likes-statistics',
      'display_likes_statistics',
      'dashicons-thumbs-up',
      6
    );
  }
  
  add_action('admin_menu', 'add_likes_admin_page');
  
?>
