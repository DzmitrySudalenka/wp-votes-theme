<!doctype html>

<html <? language_attributes() ?>>

<head>

  <meta charset="<? bloginfo('charset') ?>" />
  
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <? wp_head() ?>
  
</head>

<body <? body_class() ?>>

  <header id="header" class="header">
    
    <h1>Header</h1>
    
	</header><!-- .header -->
  
  <nav class="header-menu">
    <?php
      wp_nav_menu(array(
        'theme_location' => 'header-menu',
        'container' => false,
        'menu_class' => 'nav',
      ));
    ?>
  </nav>

  <main class="main">
