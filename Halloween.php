<?php
/**
 * Plugin Name:Halloween 
 * Plugin URI :http://thecodestuff.com
 * Description:Halloween is a demo plugin develop by mahendra choudhary
 * Version:1.0 
 * Author:Mahendra Choudhary
 * Author URI:http://thecodestuff.com
 * Licence: GPLv2
 */

 /**
  * functions used in the hallowween plugin
  */

// create custom plugin settings menu
add_action( 'admin_menu', 'prowp_create_menu' );

function prowp_create_menu(){
//create new top-level menu
add_menu_page( 'Halloween Plugin Page', 'Halloween Plugin','manage_options', 'prowp_main_menu', 'prowp_main_plugin_page',plugins_url( '/images/icon.png', __FILE__ ) );

//create two sub-menus: settings and support
add_submenu_page( 'prowp_main_menu', 'Halloween Settings Page','Settings', 'manage_options', 'halloween_settings','prowp_settings_page' );

add_submenu_page( 'prowp_main_menu', 'Halloween Support Page','Support', 'manage_options', 'halloween_support', 'prowp_support_page' );
/** for menu options page */

add_action('admin_init' , 'prowp_admin_setting') ;
add_action('admin_init' , 'prowp_register_setting')  ;
add_action('admin_init' , 'prowp_support_setting') ;
}

/**
 *  register  custom page on plugin main menu 
 */
function prowp_admin_setting(){
    register_setting( 'prowp-settings-group', 'prowp_options','prowp_sanitize_options' ) ;
}

function prowp_main_plugin_page(){
    echo'<h1>Welcome to halloween plugin option page</h1>';
}

/** this function is evoke by action hook */
function prowp_register_setting(){
    //register the menu setting
    register_setting( 'prowp-settings-group', 'prowp_options','prowp_sanitize_options' );
}
/** 
 * display form on halloween > setting menu 
 */
function prowp_settings_page() {
?>
<div class="wrap">
    <h2>Halloween Plugin Options</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'prowp-settings-group' ); ?>

        <?php $prowp_options = get_option( 'prowp_options' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Name</th>
                    <td><input type="text" name="prowp_options[option_name]" value="<?php echo esc_attr( $prowp_options['option_name'] ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="text" name="prowp_options[option_email]" value="<?php echo esc_attr( $prowp_options['option_email'] ); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">URL</th>
                    <td><input type="text" name="prowp_options[option_url]" value="<?php echo esc_url( $prowp_options['option_url'] ); ?>" /></td>
                </tr>
            </table>
            <p class="submit">
            <input type="submit" class="button-primary" value="Save Changes" /></p>
    </form>
</div>
<?php
}

/**
 * Page for support option
 */
function prowp_support_setting(){
    //registering function to be exe
    register_setting( 'prowp-settings-group', 'prowp_options','prowp_sanitize_options' ) ;
}
function prowp_support_page(){
    echo '<h1>welcome to our support</h1>';
}

 /**
  * creating a  metabox 
  */

  //hook for creating metabox
  add_action('add_meta_boxes' , 'prowp_meta_box_init') ;

  /** function for meta box hook  */
  function prowp_meta_box_init(){
      //add metabox build in function
      add_meta_box( 'prowp-meta', 'Product Information','prowp_meta_box', 'post', 'side', 'default' ) ;
  }

  /*function prowp_meta_box($post ,  $box){
?>
    <form action="#" method="post">
        <input type="text"  name="author_name" placeholder="Author Name" style="width:250px">
        <select name="author" id="" style="width:250px">
            <option value="">choose auther image </option>
            <option value="alec">Alec</option>
            <option value="Jhonson">John</option>
        </select>
        <textarea name="author_about" id="" cols="37" rows="10">write about yourself</textarea>
        <input type="submit" name="submit" value="puch it">
    </form>
<?php
  }*/

function prowp_meta_box( $post, $box ) {
    // retrieve the custom meta box values
    $prowp_featured = get_post_meta( $post->ID, '_prowp_type', true );
    $prowp_price = get_post_meta( $post->ID, '_prowp_price', true );

    //nonce for security
    wp_nonce_field( plugin_basename( __FILE__ ), 'prowp_save_meta_box' );

    // custom meta box form elements
    echo '<p>Price: <input type="text" name="prowp_price"value="'.esc_attr( $prowp_price ).'" size="5" /></p>';
    echo '<p>Type:
        <select name="prowp_product_type" id="prowp_product_type">
            <option value="0" '
                .selected( $prowp_featured, 'normal', false ). '>Normal
            </option>
    
            <option value="special" '
                .selected( $prowp_featured, 'special', false ). '>Special
            </option>
    
            <option value="featured" '
                .selected( $prowp_featured, 'featured', false ). '>Featured
            </option>

            <option value="clearance" '
                .selected( $prowp_featured, 'clearance', false ). '>Clearance
            </option>
        </select>
        
        </p>';
 }

 # saving meta box data hook 
    add_action('save_post' , 'prowp_save_meta_box') ;
 # function evoke by save_post hook 
 function prowp_save_meta_box( $post_id ){
     //process metabox data if $_POST is submitted
     if( isset( $_POST['prowp_product_type'] ) ){
         // if auto saving skip saving our meta box data
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

        //check nonce for security
        check_admin_referer( plugin_basename( __FILE__ ), 'prowp_save_meta_box' );

        // save the meta box data as post meta using the post ID as a unique prefix
        update_post_meta( $post_id, '_prowp_type',sanitize_text_field( $_POST['prowp_product_type'] ) );
        update_post_meta( $post_id, '_prowp_price',sanitize_text_field ( $_POST['prowp_price'] ) );

     }
 }

  /**
   * creating a simple social media shortcode
   */

   add_shortcode('social' , 'prowp_twitter') ;

   function prowp_twitter($atts , $content=null ){
       
        extract( shortcode_atts(  array('platform'=>'fb'), $atts )  ) ;

        if( $platform == 'fb' )
            return '<a href="http://facebook.com/thecodestuff"> <image src="'.plugins_url('images/icon.png' , __FILE__ ).'" alt="image" /></a>' ;
        elseif( $platform == 'twitter' )
            return '<a href="http://twitter.com"> <image src="'.plugins_url('images/icon.png' , __FILE__ ).'" alt="image" /></a>' ;
   }

   /**
    * widget code
    */

    # hook 
    add_action('widgets_init' , 'prowp_register_widgets') ;
    # registring hook
    function prowp_register_widgets(){
        register_widget('prowp_widget') ; //'prowp_widget' is a unqiure name for widget
    }
    # class 
    class prowp_widget extends WP_widget{
        #constructor
        function prowp_widget(){
            #widget option
            $widget_ops = array(
                'classname' =>'prowp_widget_class' ,
                'description'=>'Author bio box widget | mahendra choudhary'
            );
            #passing data to parent class WP_widget by evoking consturctor
            $this->WP_widget( 'prowp_widget' , 'Author Bio' ,'description' ) ;
        }
        #widget form 
        function form( $instance ) {
            $defaults = array(
            'title' => 'your bio',
            'name' => 'mahendra choudhary',
            'bio' => 'hey I am programmer by the day and a blogger by the night' 
            );

            $instance = wp_parse_args( (array) $instance, $defaults );

            $title = $instance['title'];
            $name = $instance['name'];
            $bio = $instance['bio'];

            ?>
            <p>Title:
                <input class="widefat"name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>Name:
                <input class="widefat" name="<?php echo $this->get_field_name( 'name' ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>" />
            </p>

            <p>Bio:
                <textarea class="widefat" name="<?php echo $this->get_field_name( 'bio' ); ?>" >
                <?php echo esc_textarea( $bio ); ?>
                </textarea>
            </p>

            <?php
            }

            #save your widget settings using the update() widget class function:
            function update( $new_instance , $old_instance ){
                $instance = $old_instance;
                $instance['title'] = sanitize_text_field( $new_instance['title'] );
                $instance['name'] = sanitize_text_field( $new_instance['name'] );
                $instance['bio'] = sanitize_text_field( $new_instance['bio'] );
                
                return $instance;
            }

            #displaying widget
            function widget( $args, $instance ) {
                extract( $args );
                echo $before_widget;
                
                $title = apply_filters( 'widget_title', $instance['title'] );
                $name = ( empty( $instance['name'] ) ) ? '&nbsp;' : $instance['name'];
                $bio = ( empty( $instance['bio'] ) ) ? '&nbsp;' : $instance['bio'];
                
                    if ( !empty( $title ) ) { 
                        echo $before_title . esc_html( $title ).$after_title; 
                    }

                    echo '<p>Name: ' . esc_html( $name ) . '</p>';
                    echo '<p>Bio: ' . esc_html( $bio ) . '</p>';
                    echo $after_widget;
                }

            
    }//widget class end
    
    /**
     * WP dashboard widget
     */

    #hook 
    add_action('wp_dashboard_setup' , 'prowp_add_dashboard_widget') ;
    #registring dashboard widget
    function prowp_add_dashboard_widget(){
        #this function register dashboard widget 
        wp_add_dashboard_widget('prowp_dashboard_widget','PRO DASHBOARD WIDGET','prowp_create_dashboard') ;
    }
    #display dashbord widget
    function prowp_create_dashboard(){
        echo'<p>here we display the usefull information about products</p>';
    }

    /**
     * Create installation function
     */

     #registration  HOOK
     register_activation_hook(__FILE__ , 'prowp_install') ;
     #function
     function prowp_install(){
         #global database connector 
         global $wpdb ;
         #table name 
         $table_name=$wpdb->prefix.'prowp_data';

         $sql="CREATE TABLE $table_name(
             id  INTEGER   AUTO_INCREMENT ,
             uname VARCHAR(20) ,
             email VARCHAR(20),
             PRIMARY KEY(id)
        )" ;
        #this path is require by dbDelta
         require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
         dbDelta($sql) ;
         //$wpdb->query($sql) ;
         
         
         //set the table structure version
        $prowp_db_version = '1.0';
        //save the table structure version number
        add_option( 'prowp_db_version', $prowp_db_version );
     }
     /**
      * author box on post
      */

      add_shortcode('authorbox' , 'prowp_create_author_box') ;

      #function 
      function prowp_create_author_box(){
          $author_css='style="
                        background-color:red ; 
                        width:445px ;
                        height:100px;                        
                        padding:5px; 
                        
                        " ';
          ?>
          <div <?php echo $author_css  ; ?>>
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                lorem ipsum dollar sit amen 
                
          </div>
          <?php 
      }
?>