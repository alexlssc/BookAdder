<?php
/**
*  @package BookAdder
*/

/*
   Plugin Name: Book Adder
   Plugin URI: http://my-awesomeness-emporium.com
   description: A plugin to add books to a database and display them on a page
   Version: 1.0
   Author: Alexandre Lissac
   Author URI: http://mrtotallyawesome.com
   License: GPL2
*/

if (! defined( 'ABSPATH' )){
  die;
}

global $jal_db_version;
$jal_db_version = '1.0';

Class BookAdder
{

  public $plugin;

  function __construct(){
    //add_action( 'init', array( $this, 'custom_post_type' ) );
    $this->plugin = plugin_basename( __FILE__ );
  }

  public function register() {

    //Queue scripts
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

    //Trigger menu creation
    add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );

    //Trigger filter
    add_filter( 'plugin_action_link' . $this->plugin , array( $this, 'settings_link' ));
  }

  public function settings_links( $links ){
    //Add custom settings links if needed
  }

  function add_admin_pages(){
    add_menu_page('Book Adder Plugin', 'Book Adder', 'manage_options', 'bookadder', array( $this, 'admin_index' ), 'dashicons-format-aside', 110 );

    add_submenu_page( 'bookadder', 'Add Book', 'Add Book', 'manage_options', 'bookadder2', array( $this, 'addBook_index' ) );

    //add_submenu_page( 'BookAdder', 'Edit Book', 'manage_options', 'bookadderEdit', array( $this, 'editBook_index' ) );
    add_submenu_page( null, 'Edit Book', 'Edit Book', 'manage_options', 'bookadderEdit', array( $this, 'editBook_index' ) );
  }

  function admin_index(){
    require_once plugin_dir_path( __FILE__ ) . 'templates/bookList.php';
  }

  function addBook_index(){
    require_once plugin_dir_path( __FILE__ ) . 'templates/addBook.php';
  }

  function editBook_index(){
    require_once plugin_dir_path( __FILE__ ) . 'templates/editBook.php';
  }

  function activate(){
    $this->custom_post_type();

    global $wpdb;
	  global $jal_db_version;

	  $table_name = $wpdb->prefix . 'books';

	  $charset_collate = $wpdb->get_charset_collate();

	  $sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  title varchar(50) NOT NULL,
		  shortdesc LONGTEXT NOT NULL,
      normaldesc LONGTEXT NOT NULL,
		  picurl varchar(255) NOT NULL,
      shopurl varchar(255) NOT NULL,
		  PRIMARY KEY  (id)
	  ) $charset_collate;";

	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $sql );

	  add_option( 'jal_db_version', $jal_db_version );

    flush_rewrite_rules();
  }

  function deactivate(){
    flush_rewrite_rules();
  }

  function custom_post_type(){
    register_post_type('book', ['public' => true, 'label' => 'Books'] );
  }

  function enqueue() {
    wp_enqueue_media();
    wp_enqueue_style( 'mypluginstyle2', plugins_url( '/assets/styleAddBook.css', __FILE__ ) );
    //wp_enqueue_script("jquery");
    //wp_enqueue_script( 'wp-media-uploader', plugins_url( '/assets/wp_media_uploader.js', __FILE__ ), array( 'jquery' ), 1.0 );
    //wp_enqueue_scripts('imageUploader', plugins_url( '/assets/imageUploader.js', __FILE__ ), array( 'jquery' ), 1.0);
  }

}

if(class_exists( 'BookAdder' )){
  $bookAdder = new BookAdder();
  $bookAdder->register();
}

//activation
register_activation_hook( __FILE__, array( $bookAdder, 'activate' ) );

//deactivation
register_deactivation_hook( __FILE__, array( $bookAdder, 'deactivate' ) );
