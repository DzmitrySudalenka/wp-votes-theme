<?php
  get_header();
?>

<div class="post-excerpts">
  <h2>Статьи</h2>
  <?php
    $args = array(
      'posts_per_page' => 5,
      'post_status' => 'publish',
    );
    $recent_posts = new WP_Query($args);
    if ($recent_posts->have_posts()) :
      while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
        <div class="post-item">
          <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
              <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
              </a>
            </div>
          <?php endif; ?>
          <div class="post-desc">
            <h2><a href="<?php the_permalink(); ?>" class="post-link"><?php the_title(); ?></a></h2>
            <p><?php the_excerpt(); ?></p>
            <p class="post-meta">
              Автор: <span><?php the_author(); ?></span>
            </p>
            <div class="post-votes">
              <button class="vote-button" data-post-id="<?php echo get_the_ID(); ?>" data-vote-type="like">
                👍 <span class="like-count">0</span>
              </button>
              <button class="vote-button" data-post-id="<?php echo get_the_ID(); ?>" data-vote-type="dislike">
                👎 <span class="dislike-count">0</span>
              </button>
            </div>
          </div>
        </div>
      <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>No posts found.</p>';
    endif;
  ?>
</div>

<?php
  get_footer();
?>
