<?php

  if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
  }

  class Likes_List_Table extends WP_List_Table {
    function __construct() {
      parent::__construct(array(
        'singular' => 'like',
        'plural' => 'likes',
        'ajax' => false
      ));
    }

    function get_columns() {
      return array(
        'cb' => '<input type="checkbox" />',
        'post_title' => 'Статья',
        'likes' => 'Лайки',
        'dislikes' => 'Дизлайки'
      );
    }

    function column_cb($item) {
      return sprintf(
        '<input type="checkbox" name="id[]" value="%s" />',
        $item['post_id']
      );
    }

    function column_post_title($item) {
      $post = get_post($item['post_id']);
      return sprintf('<a href="%s">%s</a>', get_edit_post_link($post->ID), $post->post_title);
    }

    function prepare_items() {
      global $wpdb;
      $table_name = $wpdb->prefix . 'post_likes';

      $query = "SELECT post_id, 
               SUM(IF(vote_type='like', 1, 0)) AS likes,
               SUM(IF(vote_type='dislike', 1, 0)) AS dislikes
        FROM $table_name
        GROUP BY post_id";

      $total_items = $wpdb->query($query);

      $per_page = 20;
      $current_page = $this->get_pagenum();

      $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page'    => $per_page,
      ));

      $this->items = $wpdb->get_results($query, ARRAY_A);
    }

    function column_default($item, $column_name) {
      switch ($column_name) {
        case 'likes':
        case 'dislikes':
            return $item[$column_name];
        default:
            return print_r($item, true); // Show the whole array for troubleshooting purposes
      }
    }
  }

  
  function display_likes_statistics() {
    global $wpdb;

    $likesTable = new Likes_List_Table();
    $likesTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Статистика лайков и дизлайков</h2>
        <form method="post">
            <input type="hidden" name="page" value="likes-statistics">
            <?php
            $likesTable->search_box('поиск', 'search_id');
            $likesTable->display();
            ?>
        </form>
    </div>
    <?php
  }
