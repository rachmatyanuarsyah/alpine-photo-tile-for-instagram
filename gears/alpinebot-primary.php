<?php
/**
 * AlpineBot Primary
 * 
 * Holds paramaters and settings specific to this plugin
 * Some universal functions, but mostly unique
 * 
 */
class PhotoTileForInstagramPrimary {  

  /* Set constants for plugin */
  private $url;
  private $dir;
  private $cacheUrl;
  private $cacheDir;
  private $ver = '1.2.5';
  private $vers = '1-2-5';
  private $domain = 'APTFINbyTAP_domain';
  private $settings = 'alpine-photo-tile-for-instagram-settings'; // All lowercase
  private $name = 'Alpine PhotoTile for Instagram';
  private $info = 'http://thealpinepress.com/alpine-phototile-for-instagram/';
  private $wplink = 'http://wordpress.org/extend/plugins/alpine-photo-tile-for-instagram/';
  private $donatelink = 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eric%40thealpinepress%2ecom&lc=US&item_name=Alpine%20PhotoTile%20for%20Instagram%20Donation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted';
  private $page = 'AlpineTile: Instagram';
  private $src = 'instagram';
  private $hook = 'APTFINbyTAP_hook';
  private $plugins = array('pinterest','tumblr','flickr','picasa-and-google-plus','smugmug');
  private $termsofservice = "By using this plugin, you are agreeing to the Instagram API <a href='http://instagram.com/about/legal/terms/api/' target='_blank'>Terms of Use</a>. This is why the plugin is limited to displaying 30 photos.";

  private $root = 'AlpinePhotoTiles';
  private $wjs = 'AlpinePhotoTiles_script';
  private $wcss = 'AlpinePhotoTiles_style';
  private $ajs = 'AlpinePhotoTiles_menu_script';
  private $acss = 'AlpinePhotoTiles_admin_style';
  private $wdesc = 'Add images from Instagram to your sidebar';
//####### DO NOT CHANGE #######//
  private $short = 'alpine-phototile-for-instagram';
  private $id = 'APTFIN_by_TAP';
//#############################//
  private $expiryInterval = 360; //1*60*60;  1 hour
  private $cleaningInterval = 1209600; //14*24*60*60;  2 weeks
   
  // Output Constants
  private $options = array(); // includes 'rel'
  private $results = array('photos'=>array(),'feed_found'=>false,'success'=>false,'userlink'=>'','hidden'=>'','message'=>'');
  private $output = '';
  private $wid; // Widget id
  
  private $userlink = '';
  private $cacheLimit = 2;
  private $cacheAttempts = 0;  
  
  function __construct() {
    $this->url = untrailingslashit( plugins_url( '' , dirname(__FILE__) ) );
    $this->dir = untrailingslashit( plugin_dir_path( dirname(__FILE__) ) );
    
    $this->cacheUrl = $this->url . '/cache';
    $this->cacheDir = $this->dir . '/cache';
  }
/**
 * Prevent errors by avoiding direct calls to functions
 *  
 * @ Since 1.2.5
 * 
 */
  function do_alpine_method($function, $input=array()){
    //echo $function.'() called<br>';
    if( method_exists( $this, $function )){
      if( empty($input) ){
        $this->$function();
      }else{
        $this->$function($input);
      }  
    }
  }
/**
 * Prevent errors by avoiding direct calls to functions
 *  
 * @ Since 1.2.5
 * 
 */
  function get_alpine_method($function, $input=array()){
    echo $function.'() with return called<br>';
    if( method_exists( $this, $function )){
      if( empty($input) ){
        $return = $this->$function();
      }else{
        $return = $this->$function($input);
      }
    }
    if( isset($return) ){
      return $return;
    }
    return null;
  }   
/**
 * Simple get function
 *  
 * @ Since 1.2.5
 * 
 */
  function get_private($string){
    if(isset($this->$string)){
      return $this->$string;
    }else{
      return null;
    }
  }
/**
 * Simple set function
 *  
 * @ Since 1.2.5
 * 
 */
  function set_private($string,$val){
    $this->$string = $val;
  }
/**
 * Simple set function
 *  
 * @ Since 1.2.5
 * 
 */
  function check_private($string){
    if( !empty($this->$string)){
      return true;
    }else{
      return false;
    }
  }  
/**
 * Simple get function
 *  
 * @ Since 1.2.5
 * 
 */
  function get_active_option($string){
    if(isset($this->options[$string])){
      return $this->options[$string];
    }else{
      return false;
    }
  }
/**
 * Simple set function
 *  
 * @ Since 1.2.5
 * 
 */
  function set_active_option($string,$val){
    $this->options[$string] = $val;
  }  
/**
 * Simple check function
 *  
 * @ Since 1.2.5
 * 
 */
  function check_active_option($string){
    if(!empty($this->options[$string])){
      return true;
    }else{
      return false;
    }
  }  
/**
 * Simply get function for search results that returns content
 *  
 * @ Since 1.2.5
 * 
 */
  function get_active_result($string){
    if(isset($this->results[$string])){
      return $this->results[$string];
    }else{
      return '';
    }
  }
/**
 * Simple set function
 *  
 * @ Since 1.2.5
 * 
 */
  function set_active_result($string,$val){
    $this->results[$string] = $val;
  }
/**
 * Simply check function for search results that returns boolean
 *  
 * @ Since 1.2.5
 * 
 */
  function check_active_result($string){
    if(empty($this->results[$string])){
      return false;
    }else{
      return true;
    }
  }
/**
 * Function for appending to specific result
 *  
 * @ Since 1.2.5
 * 
 */
  function append_active_result($string,$add){
    if(isset($this->results[$string])){
      $this->results[$string] = ($this->results[$string]).$add;
    }
  }
/**
 * Push photo to results
 *  
 * @ Since 1.2.5
 * 
 */
  function push_photo($array){
    $this->results['photos'][] = $array;
  } 
/**
 * Get photo information
 *  
 * @ Since 1.2.5
 * 
 */  
  function get_photo_info($i,$string){
    if( isset($this->results['photos'][$i][$string]) ){
      return $this->results['photos'][$i][$string];
    }
    return null;
  }
/**
 * Append to output
 *  
 * @ Since 1.2.5
 * 
 */
  function add($string){
    $this->output = ($this->output).$string;
  }  
//////////////////////////////////////////////////////////////////////////////////////
/////////////////////      Style/Script Functions        /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////  
/**
 * Register styles and scripts
 *  
 * @ Since 1.2.3
 * @ Updated 1.2.5
 *
 */
  function register_style_and_script(){
    wp_register_script($this->get_private('wjs'),$this->get_script('widget'),'',$this->get_private('ver'));
    wp_register_style($this->get_private('wcss'),$this->get_style('widget'),'',$this->get_private('ver'));  
   
    $lightbox = $this->get_option('general_lightbox');
    $prevent = $this->get_option('general_lightbox_no_load');
    
    $script = $this->get_script( $lightbox );
    $css = $this->get_style( $lightbox );
    
    if( !empty( $script ) && !empty( $css ) && empty($prevent) ){
      wp_register_script( $lightbox, $script, '', '', true );
      wp_register_style( $lightbox.'-stylesheet', $css, false, '', 'screen' );
    }
    
    // Load scripts in header
    $headerload = $this->get_option('general_load_header');
    if( !empty($headerload) ){
      if( !empty( $script ) && !empty( $css ) && empty($prevent) ){
        wp_enqueue_script( $lightbox );
        wp_enqueue_style( $lightbox.'-stylesheet' );
      }
      wp_enqueue_script($this->get_private('wjs'));      
      wp_enqueue_style($this->get_private('wcss'));
    }
  }
/**
 * Enqueue styles and scripts
 *  
 * @ Since 1.2.3
 * @ Updated 1.2.5
 *
 */
  function enqueue_style_and_script(){
    // Check link destination
    $link = $this->get_active_option( $this->get_private('src').'_image_link_option' );
    if( !empty($link) && $link == 'fancybox' ){
      $lightbox = $this->get_option('general_lightbox');
      $prevent = $this->get_option('general_lightbox_no_load');
      if( empty($prevent) ){
        wp_enqueue_script( $lightbox );
        wp_enqueue_style( $lightbox.'-stylesheet' );
      }
    }
    wp_enqueue_style( $this->get_private('wcss') );
    wp_enqueue_script( $this->get_private('wjs') );
  }     
/**
 * Simply get function for JS files
 *  
 * @ Since 1.2.5
 * 
 */
  function get_script($string){
    if( 'admin' == $string ){
      return $this->url.'/js/'.$this->ajs.'.js?ver='.$this->ver;
    }elseif( 'widget' == $string ){
      return $this->url.'/js/'.$this->wjs.'.js?ver='.$this->ver;
    }elseif( 'fancybox' == $string ){
      return $this->url.'/js/fancybox/jquery.fancybox-1.3.4.pack.js?ver=1.3.4';
    }elseif( 'prettyphoto' == $string ){
      return $this->url.'/js/prettyphoto/js/jquery.prettyPhoto.js?ver=3.1.4';
    }elseif( 'colorbox' == $string ){
      return $this->url.'/js/colorbox/jquery.colorbox-min.js?ver=1.4.5';	
    }elseif( 'alpine-fancybox' == $string ){
      return $this->url.'/js/fancybox-alpine-safemode/jquery.fancyboxForAlpine-1.3.4.pack.js?ver=1.3.4';
    }
    return false;
  }
/**
 * Simply get function for CSS files
 *  
 * @ Since 1.2.5
 * 
 */
  function get_style($string){
    if( 'admin' == $string ){
      return $this->url.'/css/'.$this->acss.'.css?ver='.$this->ver;
    }elseif( 'widget' == $string ){
      return $this->url.'/css/'.$this->wcss.'.css?ver='.$this->ver;
    }elseif( 'fancybox' == $string ){
      return $this->url.'/js/fancybox/jquery.fancybox-1.3.4.css?ver=1.3.4';
    }elseif( 'prettyphoto' == $string ){
      return $this->url.'/js/prettyphoto//css/prettyPhoto.css?ver=3.1.4';
    }elseif( 'colorbox' == $string ){
      return $this->url.'/js/colorbox/colorbox.css?ver=1.3.21';	
    }elseif( 'alpine-fancybox' == $string ){
      return $this->url.'/js/fancybox-alpine-safemode/jquery.fancyboxForAlpine-1.3.4.css?ver=1.3.4';
    }
    return false;
  }  
//////////////////////////////////////////////////////////////////////////////////////
/////////////////////////      Option Functions      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
/**
 *  Simple function to get option setting
 *  
 *  @ Since 1.2.0
 *  @ Updated 1.2.5
 */
  function get_option( $option_string ){
    $options = get_option( $this->settings );
    // No need to initialize options since defaults are applied as needed
    $this->options[$option_string] = ( isset($options[$option_string]) ? $options[$option_string] : $this->set_default_option( $options, $option_string ) );
    return $this->options[$option_string];
  }
/**
 *  Simple function to array of all option settings
 *  
 *  @ Since 1.2.0
 *  @ Updated 1.2.5
 */
  function get_all_options(){
    $options = get_option( $this->settings );
    $defaults = $this->option_defaults(); 
    foreach( $defaults as $option_string => $details ){
      if( !isset($options[$option_string]) && !empty($defaults[$option_string]) && isset($defaults[$option_string]['default']) ){
        $options[$option_string] = $defaults[$option_string]['default'];
      }elseif( !isset($options[$option_string]) && !empty($defaults[$option_string]) && !isset($defaults[$option_string]['default']) ){
        $options[$option_string] = '';
      }
    }
    update_option( $this->settings, $options ); //Unnecessary since options will soon be updated if this fuction was called
    return $options;
  }
/**
 *  Correctly set and save the option's default setting
 *  
 *  @ Since 1.2.0
 */
  function set_default_option( $options, $option_string ){
    $default_options = $this->option_defaults();
    if( !empty($default_options[$option_string]) && isset($default_options[$option_string]['default']) ){
      $options[$option_string] = $default_options[$option_string]['default'];
      update_option( $this->settings, $options );
      return $options[$option_string];
    }else{
      return '';
    }
  }
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////      Admin Option Functions       /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////  
/**
 * Option positions for widget page
 *  
 * @ Since 1.2.0
 * 
 */
  function admin_widget_positions(){
      $options = array(
      'top' => '',
      'left' => 'Instagram Settings',
      'right' => 'Style Settings',
      'bottom' => 'Format Settings'
    );
    return $options;
  }
  
/**
 * Option positions for settings pages
 *  
 * @ Since 1.2.0
 * @ Updated 1.2.5
 */
  function admin_option_positions(){
    $positions = array(
      'generator' => array(
        'left' => array( 'title' => 'Instagram Settings' ),
        'right' => array( 'title' => 'Style Settings' ),
        'bottom' => array( 'title' => 'Format Settings' )
      ),
      'add' => array(
        'top' => array( 'title' => 'Available Users' ),
        'center' => array( 'title' => 'Add New User (See Instructions Below)' )
      ),
      'plugin-settings' => array(
        'top' => array( 'title' => 'Global Style Options', 'description' => 'Below are style settings that will be applied to every instance of the plugin.' ),
        'center' => array( 'title' => 'Hidden Options', 'description' => 'Below are additional options that you can choose to enable by checking the box. <br>Once enabled, the option will appear in the Widget Menu and Shortcode Generator.' ),
        'bottom' => array( 'title' => 'Cache Options', 'description' => 'The plugin is capable of storing the url addresses to the photos in your feed. Please note that the plugin does not store the image files and that if your website has a cache plugin like WP Super Cache or W3 Total Cache, the cache feature of the Alpine PhotoTile will have no effect.')
      )
    );
    return $positions;
  }
/**
 * Plugin Admin Settings Page Tabs
 *  
 * @ Since 1.2.0
 *
 */
  function admin_settings_page_tabs() {
    $tabs = array( 
      'general' => array(
        'name' => 'general',
        'title' => 'General',
      ),
      'add' => array(
        'name' => 'add',
        'title' => 'Add Instagram User',
      ),   
      'generator' => array(
        'name' => 'generator',
        'title' => 'Shortcode Generator',
      ),
      'plugin-settings' => array(
        'name' => 'plugin-settings',
        'title' => 'Plugin Settings',
      )
    );
    return $tabs;
  }  
/**
 * Retrieve Available Users
 *  
 * @ Since 1.2.0
 *
 */
  function get_instagram_users(){
    $return = array(
          'none' => array(
            'name' => 'none',
            'title' => 'Please Add a User'
          ));
    // Do not use $this->get_option('users'); since function is secondary, not primary
    // Additionally there is no default value to set and settings aren't update based on this call
    $options = get_option( $this->settings ); 
    if( !empty( $options['users'] ) ){
      $return = $options['users'];
    }
    return $return;
  }  
/**
 * Option Parameters and Defaults
 *  
 * @ Since 1.0.0
 * @ Updated 1.2.3
 */
  function option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'description' => '',
        'since' => '1.1',
        'widget' => true,
        'tab' => '',
        'position' => 'top',
        'default' => ''
      ),
      'instagram_user_id' => array(
        'name' => 'instagram_user_id',
        'short' => 'user',
        'title' => 'Select User: ',
        'type' => 'select',
        'valid_options' => $this->get_instagram_users(),
        'description' => 'Add an Instagram user <a href="options-general.php?page='.$this->get_private('settings').'&tab=add">here</a>.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),      
      'instagram_source' => array(
        'name' => 'instagram_source',
        'short' => 'src',
        'title' => 'Retrieve Photos From : ',
        'type' => 'select',
        'valid_options' => array(
          'user_recent' => array(
            'name' => 'user_recent',
            'title' => 'User Recent'
          ),
          'user_feed' => array(
            'name' => 'user_feed',
            'title' => 'User Feed'
          ),
          'user_liked' => array(
            'name' => 'user_liked',
            'title' => 'User Liked'
          ),
          'user_tag' => array(
            'name' => 'user_tag',
            'title' => 'User Tag'
          ),
          'global_popular' => array(
            'name' => 'global_popular',
            'title' => 'Global Popular'
          ),
          'global_tag' => array(
            'name' => 'global_tag',
            'title' => 'Global Tag'
          )   
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'instagram_source',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'user'
      ),
      'instagram_tag' => array(
        'name' => 'instagram_tag',
        'short' => 'tag',
        'title' => 'Tag (without "#"): ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'remove' => '#',
        'description' => '',
        'child' => 'instagram_source', 
        'hidden' => 'user_recent user_feed user_liked global_popular',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',            
        'default' => ''
      ),       
      
      'instagram_image_link_option' => array(
        'name' => 'instagram_image_link_option',
        'short' => 'imgl',
        'title' => 'Image Links : ',
        'type' => 'select',
        'valid_options' => array(
          'none' => array(
            'name' => 'none',
            'title' => 'Do not link images'
          ),
          'instagram' => array(
            'name' => 'instagram',
            'title' => 'Link to Instagram Page'
          ),
          'link' => array(
            'name' => 'link',
            'title' => 'Link to URL Address'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Use Lightbox'
          )               
        ),
        'description' => '*Privacy settings may prevent linking to Instagram page.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'instagram_image_link_option',
        'default' => 'instagram'
      ),   
      'custom_lightbox_rel' => array(
        'name' => 'custom_lightbox_rel',
        'short' => 'crel',
        'title' => 'Custom Lightbox "rel" (Optional): ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'encode' => array("["=>"{ltsq}","]"=>"{rtsq}"),
        'description' => '',
        'child' => 'instagram_image_link_option', 
        'hidden' => 'none original instagram link',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_lightbox_custom_rel',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => ''
      ),        
      'custom_link_url' => array(
        'name' => 'custom_link_url',
        'title' => 'Custom Link URL : ',
        'short' => 'curl',
        'type' => 'text',
        'sanitize' => 'url',
        'description' => '',
        'child' => 'instagram_image_link_option', 
        'hidden' => 'none original instagram fancybox',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => ''
      ),
      'photo_feed_shuffle' => array(
        'name' => 'photo_feed_shuffle',
        'short' => 'shuffle',
        'title' => 'Shuffle/Randomize Photos',
        'type' => 'checkbox',
        'description' => '<br>Likely to increase the loading time of the plugin.',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_photo_feed_shuffle',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.4',
        'default' => ''
      ),   
      'instagram_display_link' => array(
        'name' => 'instagram_display_link',
        'short' => 'dl',
        'title' => 'Display link to Instagram page.',
        'type' => 'checkbox',
        'description' => '',
        'child' => 'instagram_source',
        'hidden' => 'community',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_display_link',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => ''
      ),    
      'instagram_display_link_text' => array(
        'name' => 'instagram_display_link_text',
        'short' => 'dltext',
        'title' => 'Text for display link: ',
        'type' => 'text',
        'sanitize' => 'html',
        'description' => '',
        'child' => 'instagram_source', 
        'hidden' => 'community',
        'widget' => true,
        'hidden-option' => true,
        'check' => 'hidden_display_link',
        'tab' => 'generator',
        'position' => 'left',
        'since' => '1.2.3',
        'default' => 'Instagram'
      ),      

      'style_option' => array(
        'name' => 'style_option',
        'short' => 'style',
        'title' => 'Style : ',
        'type' => 'select',
        'valid_options' => array(
          'vertical' => array(
            'name' => 'vertical',
            'title' => 'Vertical'
          ),
          'windows' => array(
            'name' => 'windows',
            'title' => 'Windows'
          ),
          'wall' => array(
            'name' => 'wall',
            'title' => 'Wall'
          ),
          'cascade' => array(
            'name' => 'cascade',
            'title' => 'Cascade'
          ),
          'gallery' => array(
            'name' => 'gallery',
            'title' => 'Gallery'
          )           
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'style_option',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'vertical'
      ),      
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'short' => 'row',
        'title' => 'Photos per row : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '30',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'short' => 'col',
        'title' => 'Number of columns : ',
        'type' => 'range',
        'min' => '1',
        'max' => '10',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '2'
      ),     
      'style_gallery_ratio_width' => array(
        'name' => 'style_gallery_ratio_width',
        'short' => 'grwidth',
        'title' => 'Aspect Ratio Width : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "",
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'since' => '1.2.3',
        'default' => '800'
      ),      
      'style_gallery_ratio_height' => array(
        'name' => 'style_gallery_ratio_height',
        'short' => 'grheight',
        'title' => 'Aspect Ratio Height : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the Aspect Ratio of the gallery display. <br>(Default: 800 by 600)",
        'widget' => true,
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift cascade',
        'tab' => 'generator',
        'position' => 'right',
        'since' => '1.2.3',
        'default' => '600'
      ),
      'instagram_photo_size' => array(
        'name' => 'instagram_photo_size',
        'short' => 'size',
        'title' => 'Photo Size : ',
        'type' => 'select',
        'valid_options' => array(
          'Th' => array(
            'name' => 'Th',
            'title' => 'Thumb'
          ),
          'M' => array(
            'name' => 'M',
            'title' => 'Medium'
          ),
          'L' => array(
            'name' => 'L',
            'title' => 'Large'
          )  
        ),
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'M'
      ),       
      'instagram_photo_number' => array(
        'name' => 'instagram_photo_number',
        'short' => 'num',
        'title' => 'Number of photos : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '30',
        'description' => 'Maximum of 30.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'photo_feed_offset' => array(
        'name' => 'photo_feed_offset',
        'short' => 'offset',
        'title' => 'Photo Offset: ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '500',
        'description' => 'Skip over this many photos.',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'hidden-option' => true,
        'check' => 'hidden_photo_feed_offset',
        'default' => '0'
      ),     
      'style_shadow' => array(
        'name' => 'style_shadow',
        'short' => 'shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'short' => 'border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_highlight' => array(
        'name' => 'style_highlight',
        'short' => 'highlight',
        'title' => 'Highlight when hovering.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'short' => 'curve',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),          
      'widget_alignment' => array(
        'name' => 'widget_alignment',
        'short' => 'align',
        'title' => 'Photo alignment : ',
        'type' => 'select',
        'valid_options' => array(
          'left' => array(
            'name' => 'left',
            'title' => 'Left'
          ),
          'center' => array(
            'name' => 'center',
            'title' => 'Center'
          ),
          'right' => array(
            'name' => 'right',
            'title' => 'Right'
          )            
        ),
        'hidden-option' => true,
        'check' => 'hidden_widget_alignment',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'since' => '1.2.3',
        'default' => 'center'
      ),    
      'widget_max_width' => array(
        'name' => 'widget_max_width',
        'short' => 'max',
        'title' => 'Maximum widget width : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'max' => '100',
        'description' => "Percentage (%) between 1 and 100.",
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'short' => 'nocredit',
        'title' => 'Disable the tiny "TAP" link in the bottom left corner, though I would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => ''
      ), 
      'general_disable_right_click' => array(
        'name' => 'general_disable_right_click',
        'title' => 'Disable Right-Click: ',
        'type' => 'checkbox',
        'description' => 'Prevent visitors from right-clicking and downloading images.',
        'since' => '1.2.4',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),
      'general_loader' => array(
        'name' => 'general_loader',
        'title' => 'Disable Loading Icon: ',
        'type' => 'checkbox',
        'description' => 'Remove the icon that appears while images are loading.',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ), 
      'general_highlight_color' => array(
        'name' => 'general_highlight_color',
        'title' => 'Highlight Color:',
        'type' => 'color',
        'description' => 'Click to choose link color.',
        'section' => 'settings',
        'tab' => 'general',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => '#64a2d8'
      ),
      'general_hide_message' => array(
        'name' => 'general_hide_message',
        'title' => 'Hide error messages: ',
        'type' => 'checkbox',
        'description' => 'Prevent the plugin from displaying error messages.',
        'since' => '1.2.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),       
      'general_load_header' => array(
        'name' => 'general_load_header',
        'title' => 'Load Styles and <br>Scripts in Header: ',
        'type' => 'checkbox',
        'description' => 'For themes without a wp_footer() call, load the plugin CSS styles and JS scripts in the head of every page.',
        'since' => '1.2.5',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ),       
      'general_lightbox_no_load' => array(
        'name' => 'general_lightbox_no_load',
        'title' => 'Prevent Lightbox Loading: ',
        'type' => 'checkbox',
        'description' => 'Already using the below jQuery Lightbox Plugin? Prevent this plugin from loading it again.',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'since' => '1.2.3',
        'default' => ''
      ),      
      'general_lightbox' => array(
        'name' => 'general_lightbox',
        'title' => 'Choose jQuery Lightbox Plugin : ',
        'type' => 'select',
        'valid_options' => array(
          'alpine-fancybox' => array(
            'name' => 'alpine-fancybox',
            'title' => 'Fancybox (Safemode)'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Fancybox'
          ),
          'colorbox' => array(
            'name' => 'colorbox',
            'title' => 'ColorBox'
          ),
          'prettyphoto' => array(
            'name' => 'prettyphoto',
            'title' => 'prettyPhoto'
          )      
        ),
        'tab' => 'plugin-settings',
        'position' => 'top',
        'since' => '1.2.3',
        'default' => 'alpine-fancybox'
      ),
      'general_lightbox_params' => array(
        'name' => 'general_lightbox_params',
        'title' => 'Custom Lightbox Parameters:',
        'type' => 'textarea',
        'sanitize' => 'css',
        'description' => 'Add custom parameters to the lighbox call.',
        'section' => 'settings',
        'tab' => 'general',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ), 

      'hidden_display_link' => array(
        'name' => 'hidden_display_link',
        'title' => 'Link Below Widget: ',
        'type' => 'checkbox',
        'description' => 'Place a link with custom text below widget display.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ), 
      'hidden_widget_alignment' => array(
        'name' => 'hidden_widget_alignment',
        'title' => 'Photo Alignment: ',
        'type' => 'checkbox',
        'description' => 'Align photos to the left, right, or center.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => true
      ), 
      'hidden_lightbox_custom_rel' => array(
        'name' => 'hidden_lightbox_custom_rel',
        'title' => 'Custom "rel" for Lightbox: ',
        'type' => 'checkbox',
        'description' => 'Set custom "rel" to widget options.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ), 
      'hidden_photo_feed_offset' => array(
        'name' => 'hidden_photo_feed_offset',
        'title' => 'Photo Offset: ',
        'type' => 'checkbox',
        'description' => 'Skip over certain number of photos.',
        'since' => '1.2.3',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ), 
      'hidden_photo_feed_shuffle' => array(
        'name' => 'hidden_photo_feed_shuffle',
        'title' => 'Photo Shuffle: ',
        'type' => 'checkbox',
        'description' => 'Randomize photos. Please note that this feature may significantly increase the loading time of the plugin.',
        'since' => '1.2.4',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ), 
      'cache_disable' => array(
        'name' => 'cache_disable',
        'title' => 'Disable feed caching: ',
        'type' => 'checkbox',
        'description' => 'Fetch the photo feed each time someone visits your website.',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'bottom',
        'default' => ''
      ), 
      'cache_time' => array(
        'name' => 'cache_time',
        'title' => 'Cache time (hours) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the number of hours that a feed will be stored.",
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'bottom',
        'default' => '4'
      ), 
      
      'client_id' => array(
        'name' => 'client_id',
        'title' => 'Client ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'tab' => 'add',
        'position' => 'center',
        'default' => ''
      ),  
      'client_secret' => array(
        'name' => 'client_secret',
        'title' => 'Client Secret : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'tab' => 'add',
        'position' => 'center',
        'default' => ''
      )
      
    );
    return $options;
  }
}

?>
