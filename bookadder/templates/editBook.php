<!DOCTYPE html>
<?php
  global $wpdb;

  $table_name = $wpdb->prefix . 'books';

  //RETRIEVE DATA FROM TABLE TO PUT IN TEXT FIELD
  //Get ID number
  if( isset( $_REQUEST['book'] )){ //Verify if key exists
    $id = $_REQUEST['book'];

    //Run query
    $data = $wpdb->get_results('SELECT * FROM ' . $table_name . ' WHERE id = ' . $id, ARRAY_A);

    //Extract data
    $currentTitle = $data['0']['title'];
    $currentShortDesc = $data['0']['shortdesc'];
    $currentNormalDesc = $data['0']['normaldesc'];
    $currentPicUrl = $data['0']['picurl'];
    $currentShopUrl = $data['0']['shopurl'];
  }

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

      $wpdb->update($table_name, array(
        "title" => $title,
        "shortdesc" => $smallDesc,
        "normaldesc" => $normalDesc,
        "picurl" => $picture,
        "shopurl" => $shop
      ),
      array('id' => $id)
    );

      $successMessage = "Done!";
      echo "<meta http-equiv='refresh' content='0'>";
    } else {
      $successMessage = "Error in input";
    }
  }
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <div id="icon-users" class="icon32"><br/></div>
    <title>Edit Book</title>
     <script type="text/javascript" src="<?php echo plugins_url( '../assets/imageUploader.js', __FILE__ ) ?>"></script>
  </head>
  <body>
    <div class="titleContainer">
      <h1>Book Adder Plugin</h1>
    </div>
    <h2>Edit Book</h2>
    <form id="formAddBook" action="" method="POST">
      <strong>Book's title:</strong> <input type="text" name="Title" value= "<?php echo $currentTitle ?>" ><br>
      <p>
        <strong>Header Logo Image URL:</strong><br />
        <img class="header_logo" src="<?php echo get_option('header_logo', $currentPicUrl); ?>" height="300"/>
        <input class="header_logo_url" type="text" name="header_logo" size="60" value="<?php echo get_option('header_logo', $currentPicUrl); ?>">
        <a href="#" class="header_logo_upload">Upload</a>
      </p>
      <strong>Shop link:</strong> <input type="text" name="Shop" value= "<?php echo $currentShopUrl ?>"><br>
      <p><strong>Short description:</strong></p>
      <?php wp_editor( $currentShortDesc, "shortdesc")?>
      <p><strong>Normal description:</strong></p>
      <?php wp_editor( $currentNormalDesc, "normaldesc")?>
      <input type="submit" value="SUBMIT" name="submitButton"/>
    </form>
    <h2 class="successMessage">
      <?php echo $successMessage ?>
    </h2>
  </body>
</html>
