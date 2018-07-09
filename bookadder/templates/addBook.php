<!DOCTYPE html>
<?php
  global $wpdb;

  $table_name = $wpdb->prefix . 'books';

  //Verify if database empty
  $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

  //Display correct tag depending of size of table
  function possibleGreyedCheck($rowcount){
    if($rowcount == 0){
      return '<input type="checkbox" name="onMainPage" value="valueCheckAddPage" checked onclick="return false">Display on main page<br>';
    } else {
      return '<input type="checkbox" name="onMainPage" value="valueCheckAddPage">Display on main page<br>';
    }
  }

  $successMessage = "";

  if (isset ( $_POST['submitButton'] ) ){
    // retrieve the form data by using the element's name attributes value as key
    $title = $_POST['Title'];
    $picture = $_POST['header_logo'];
    $shop = $_POST['Shop'];
    $smallDesc = $_POST['shortdesc'];
    $normalDesc = $_POST['normaldesc'];
    $onMainPage = isset($_POST['onMainPage']) ? 1 : 0;

    if($onMainPage == 1){
      $wpdb->query($wpdb->prepare("UPDATE $table_name SET on_main_page=%d WHERE on_main_page=%d", 0, 1)); //Remove actual main page book from main page
      $wpdb->flush();
    }

    //check if evey field not NULL
    if( !empty($title) && !empty($picture) && !empty($shop) && !empty($smallDesc) && !empty($normalDesc) ){

      $wpdb->insert($table_name, array(
        "title" => $title,
        "shortdesc" => $smallDesc,
        "normaldesc" => $normalDesc,
        "picurl" => $picture,
        "shopurl" => $shop,
        "on_main_page" => $onMainPage
      ));

      $successMessage = "Done!";
    } else {
      $successMessage = "Error in input";
    }
  }
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Book</title>
    <link rel='stylesheet' href='assets/styleAddBook.css' type='text/css'>
    <script type="text/javascript" src="<?php echo plugins_url( '../assets/imageUploader.js', __FILE__ ) ?>"></script>
  </head>
  <body>
    <div class="titleContainer">
      <h1>Book Adder Plugin</h1>
    </div>
    <div id="icon-users" class="icon32"><br/></div>
    <h2>Add Book</h2>
    <form id="formAddBook" action="" method="POST">
      <strong>Book's title:</strong> <input type="text" name="Title" value= "<?php echo $currentTitle ?>" ><br>
      <p>
        <strong>Header Logo Image URL:</strong><br />
        <img class="header_logo" src="<?php echo get_option('header_logo'); ?>" height="300"/>
        <input class="header_logo_url" type="text" name="header_logo" size="60" value="<?php echo get_option('header_logo'); ?>">
        <a href="#" class="header_logo_upload">Upload</a>
      </p>
      <strong>Shop link:</strong> <input type="text" name="Shop" value= "<?php echo $currentShopUrl ?>"><br>
      <p><strong>Short description:</strong></p>
      <?php wp_editor( $currentShortDesc, "shortdesc")?>
      <p><strong>Normal description:</strong></p>
      <?php wp_editor( $currentNormalDesc, "normaldesc")?><br>
      <?php
        echo possibleGreyedCheck($rowcount);
      ?>
      <br><input type="submit" value="SUBMIT" name="submitButton"/>
    </form>
    <h2 class="successMessage">
      <?php echo $successMessage ?>
    </h2>
  </body>
</html>
