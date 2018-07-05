<!DOCTYPE html>
<?php
  global $wpdb;

  $table_name = $wpdb->prefix . 'books';

  $successMessage = "";

  if (isset ( $_POST['submitButton'] ) ){
    // retrieve the form data by using the element's name attributes value as key
    $title = $_POST['Title'];
    $picture = $_POST['header_logo'];
    $shop = $_POST['Shop'];
    $smallDesc = $_POST['shortdesc'];
    $normalDesc = $_POST['normaldesc'];

    //check if evey field not NULL
    if( !empty($title) && !empty($picture) && !empty($shop) && !empty($smallDesc) && !empty($normalDesc) ){

      $wpdb->insert($table_name, array(
        "title" => $title,
        "shortdesc" => $smallDesc,
        "normaldesc" => $normalDesc,
        "picurl" => $picture,
        "shopurl" => $shop
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
      <?php wp_editor( $currentNormalDesc, "normaldesc")?>
      <input type="submit" value="Submit" name="submitButton"/>
    </form>
    <h2 class="successMessage">
      <?php echo $successMessage ?>
    </h2>
  </body>
</html>
