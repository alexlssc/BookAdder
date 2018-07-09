<?php

  if(!class_exists('WP_List_Table')){
      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }

  Class List_Table_Books extends WP_List_Table {

        function __construct(){
            global $status, $page;

            //Set parent defaults
            parent::__construct( array(
                'singular'  => 'book',     //singular name of the listed records
                'plural'    => 'books',    //plural name of the listed records
                'ajax'      => false        //does this table support ajax?
            ) );

        }

        function column_default($item, $column_name){
            switch($column_name){
                case 'title':
                case 'shopurl':
                case 'on_main_page':
                    return $item[$column_name];
                default:
                    return print_r($item,true); //Show the whole array for troubleshooting purposes
            }
        }

        function column_title($item){

            //Build row actions
            $actions = array(
                'edit'      => sprintf('<a href="?page=bookadderEdit&action=%s&book=%s">Edit</a>','edit',$item['id']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
            );

            //Return the title contents
            return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
                /*$1%s*/ $item['title'],
                /*$2%s*/ $item['id'],
                /*$3%s*/ $this->row_actions($actions)
            );
        }

        function column_cb($item){
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
            );
        }

        function get_columns(){
            $columns = array(
                'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
                'title'     => 'Title',
                'shopurl'    => 'Shop URL',
                'on_main_page' => 'On main page'
            );
            return $columns;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'title'     => array('title',false),     //true means it's already sorted
                'shopurl'    => array('shopurl',false),
                'on_main_page'    => array('on_main_page',false)
            );
            return $sortable_columns;
        }

        function get_bulk_actions() {
            $actions = array(
                'delete'    => 'Delete'
            );
            return $actions;
        }

        function process_bulk_action() {

            //Detect when a bulk action is being triggered...
            if( 'delete'===$this->current_action() ) {
            }

        }

        function prepare_items() {
            global $wpdb; //This is used only if making any database queries

            $table_name = $wpdb->prefix . 'books';

            /**
             * First, lets decide how many records per page to show
             */
            $per_page = 5;


            /**
             * REQUIRED. Now we need to define our column headers. This includes a complete
             * array of columns to be displayed (slugs & titles), a list of columns
             * to keep hidden, and a list of columns that are sortable. Each of these
             * can be defined in another method (as we've done here) before being
             * used to build the value for our _column_headers property.
             */
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();


            /**
             * REQUIRED. Finally, we build an array to be used by the class for column
             * headers. The $this->_column_headers property takes an array which contains
             * 3 other arrays. One for all columns, one for hidden columns, and one
             * for sortable columns.
             */
            $this->_column_headers = array($columns, $hidden, $sortable);

            /**
             * Optional. You can handle your bulk actions however you see fit. In this
             * case, we'll handle them within our package just to keep things clean.
             */
            //$this->process_bulk_action();

            /**
             * Instead of querying a database, we're going to fetch the example data
             * property we created for use in this plugin. This makes this example
             * package slightly different than one you might build on your own. In
             * this example, we'll be using array manipulation to sort and paginate
             * our data. In a real-world implementation, you will probably want to
             * use sort and pagination data to build a custom query instead, as you'll
             * be able to use your precisely-queried data immediately.
             */
            //$data = $this->example_data;


            /**
             * This checks for sorting input and sorts the data in our array accordingly.
             *
             * In a real-world situation involving a database, you would probably want
             * to handle sorting by passing the 'orderby' and 'order' values directly
             * to a custom query. The returned data will be pre-sorted, and this array
             * sorting technique would be unnecessary.
             */
            function usort_reorder($a,$b){
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
            }
            usort($data, 'usort_reorder');



            /***********************************************************************
             * ---------------------------------------------------------------------
             * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
             *
             * In a real-world situation, this is where you would place your query.
             *
             * For information on making queries in WordPress, see this Codex entry:
             * http://codex.wordpress.org/Class_Reference/wpdb
             *
             * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
             * ---------------------------------------------------------------------
             **********************************************************************/

             $data = $wpdb->get_results('SELECT * FROM ' . $table_name , ARRAY_A);
             $wpdb->flush();


            /**
             * REQUIRED for pagination. Let's figure out what page the user is currently
             * looking at. We'll need this later, so you should always include it in
             * your own package classes.
             */
            $current_page = $this->get_pagenum();

            /**
             * REQUIRED for pagination. Let's check how many items are in our data array.
             * In real-world use, this would be the total number of items in your database,
             * without filtering. We'll need this later, so you should always include it
             * in your own package classes.
             */
            $total_items = count($data);


            /**
             * The WP_List_Table class does not handle pagination for us, so we need
             * to ensure that the data is trimmed to only the current page. We can use
             * array_slice() to
             */
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



            /**
             * REQUIRED. Now we can add our *sorted* data to the items property, where
             * it can be used by the rest of the class.
             */
            $this->items = $data;



            /**
             * REQUIRED. We also have to register our pagination options & calculations.
             */
            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
        }

        function pre_load(){
          $allowed_delete = 'delete';
          if ( isset($_REQUEST['action']) ) {

            $actionValue = $_REQUEST['action'];

            //target remove function
            if(strcmp($allowed_delete, $actionValue) == 0){
              $this->delete_row();
            }
          }
        }

        function delete_row(){
          global $wpdb;
          $table_name = $wpdb->prefix . 'books';
          $id = $_REQUEST['book'];
          $wpdb->delete( $table_name, array( 'id' => $id ) );
          $wpdb->flush();
        }

  }

  $debugString = "";

  //Create an instance of our package class...
  $testListTable = new List_Table_Books();

  $testListTable->pre_load();
  //Fetch, prepare, sort, and filter our data...
  $testListTable->prepare_items();


  if( isset($_POST['submitButtonList']) ){ //if buttom dropdown menu pressed
    global $wpdb;
    $table_name = $wpdb->prefix . 'books'; //Retrieve table full name
    $idTarget = $_POST['selectList']; //Retrive value linked to select list
    $wpdb->query($wpdb->prepare("UPDATE $table_name SET on_main_page=%d WHERE on_main_page=%d", 0, 1)); //Remove actual main page book from main page
    $wpdb->flush();
    $wpdb->query($wpdb->prepare("UPDATE $table_name SET on_main_page=%d WHERE id=%d", 1, $idTarget));//Add new book to main page
    $wpdb->flush();
  }

 ?>




 <div class="wrap">
     <div class="titleContainer">
       <h1>Book Adder Plugin</h1>
     </div>
     <div id="icon-users" class="icon32"><br/></div>
     <h2>Books List</h2>
     <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
     <form id="movies-filter" method="get">
         <!-- For plugins, we also need to ensure that the form posts back to our current page -->
         <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
         <!-- Now we can render the completed list table -->
         <?php $testListTable->display() ?>
     </form>

    <div id="icon-users" class="icon32"><br/></div>
    <h2>Select book displayed on main page</h2>
    <form id="formAddBook" action="" method="POST">
      <select name='selectList'>
        <?php
          foreach ($testListTable->items as $object){
            echo "<option value = $object[id] name='selectValue'>$object[title]</option>";
          }
        ?>
      </select>
      <input type="submit" value="SUBMIT" name="submitButtonList"/>
    </form>
    <?php echo $debugString; ?>



 </div>
