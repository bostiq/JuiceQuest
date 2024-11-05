<?php
/**
 * Functions - Child theme custom functions
 */


/************************************************************************************************
***************** CAUTION: do not remove or edit anything within this section ******************/

/**
 * Makes the Divi Children Engine available for the child theme.
 * Do not remove this, your child theme will not work.
 */
require_once('divi-children-engine/divi_children_engine.php');

/***********************************************************************************************/


/*- You can include any custom code for your child theme below this line -*/


/* MODIFY FILE 'WP-LOIGN.PHP'
*/

// Logo

function my_login_logo() { ?>
<style type="text/css">
  
  .x-overlay {
    background-image: url('../../../wp-content/uploads/2022/11/Squatters-Collective-Deli-Daniel-Purvis-Dec-2020_19735-scaled.jpg') !important;
  }
  

  #login h1 a, .login h1 a {
	background-image: url('../../../wp-content/uploads/2023/03/NEW_JQ_logo_doublecolour_lime_green.png');
    height:200px;
    width:200px;
    background-size: 200px;
    background-repeat: no-repeat;
  }
/*   #login h1:after {
    content: "Juice Quest";
    font-family: 'Zen Maru Gothic',Helvetica,Arial,Lucida,sans-serif;
    font-weight: 700;
    font-size: 40px;
    line-height: 40px;
    color: rgba(255, 255, 255, 0.7);
    text-shadow: none;
  } */
  .login h1 a {
    margin: 0 auto 0 !important;
  }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap" rel="stylesheet">

<?php }

// To change the link values so the logo links

add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
  return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
  return 'Juice Quest';
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );


// END LOGO

// Attach CSS to wp-login.php

function my_login_stylesheet() {
  // wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login-index.css' );
  wp_enqueue_style( 'custom-fonts', 'https://indexwebmedia.com/bostiq/commons/font-awesome-4.7.0/css/font-awesome.min.css' );
  wp_enqueue_style( 'custom-login-style', 'https://indexwebmedia.com/bostiq/commons/wp-login/login-index.min.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );


// END CSS

// Attach html after Form to wp-login.php
add_action( 'login_footer', 'login_extra_note' );

function login_extra_note() {

//Adding the text

  ?>
<div id="bottom_html">
  <p class="txt"></p>
</div>
<div class="x-overlay">&nbsp;</div>

  <?php
}

// Enqueue Index Styles

function index_styles() {
  wp_enqueue_style( 'index-styles-2024', get_stylesheet_directory_uri() . '/css/style.min.css' );
  // wp_enqueue_style( 'custom-fonts', 'https://indexwebmedia.com/bostiq/commons/font-awesome-4.7.0/css/font-awesome.min.css' );
  // wp_enqueue_style( 'custom-login-style', 'https://indexwebmedia.com/bostiq/commons/wp-login/login-index.min.css' );
}
add_action( 'wp_enqueue_scripts', 'index_styles' );


// function index_styles() {

//   $parent_style = 'Divi';

//   //wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
//   wp_enqueue_style( 'index-style',
//       get_stylesheet_directory_uri() . '/index-jq-style.min.css',
//       array( $parent_style ),
//       wp_get_theme()->get('1.0.0')
//   );
// }
// add_action( 'wp_enqueue_scripts', 'index_styles' );





?>