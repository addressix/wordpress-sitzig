<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sitzig
 * @subpackage Sitzig/admin
 * @author     Meworla GmbH <info@meworla.com>
 */
class Sitzig_Admin 
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

    $this->setup_hooks();
  }

  function init() {
    $this->api = AddressixAPI::init();	  
  }

  function plugin_menu()
  {
    add_menu_page('Events', 'Events', 'edit_others_posts', 'sitzig_events', array($this, 'draw_plugin_admin'), 20);
    add_options_page(
      'Sitzig Options',
      'Sitzig Events', 
      'manage_options', 
      'eventgroup_admin', 
      array($this, 'draw_option_page')
      );
  }

  function draw_option_page() {
	?>
	  <div class="wrap">
   <h2>Sitzig Events</h2>
   <form method="post" action="options.php">
   <?php 
      settings_fields('eventgroup_group');
    do_settings_sections('eventgroup_admin');
    submit_button();
?>
</form>
</div><?php
    }

  function draw_plugin_admin()
  {
    if (isset($_GET['event_id']) && ((int)$_GET['event_id'])) {
	    
      $url = '/events/v1/events/' . (int)$_GET['event_id'];
      $response = $this->api->fetch($url);
      if ($response->code==200) {
	$this->event = $response->body;
	require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/edit-event.php');
      }
      else {
	error_log('could not open event ' . $_GET['event_id'] . ' Code(' . $response->code .')');
      }
      $this->event = $response->body;	    

    } else if (isset($_GET['new'])) {
      require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/new-event.php');
    }
    else {

      // nothing other matched - draw  events overview
	    
      $groupid = $this->options['groupid'];
      if (!$groupid) {
	require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/nogroupset.php');
	return;
      }
      $url = '/events/v1/groups/' . $groupid;
      $response = $this->api->fetch($url);
      if ($response->code==200) {
	$this->events = $response->body;
	require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/agenda.php');
      }
      else {
	error_log('could not open events for group ' . $groupid . ' Code(' . $response->code .')');
      }
    }

  }

  function process_edit()
  {
    // Check nonce field
    check_admin_referer('sitzig_event_verify');

    $m=0;

    // do the update
    $params = array();
    $fields = array('eventtitle','place','address','city');
    foreach($fields as $field) {
      $params[$field] = sanitize_text_field($_POST[$field]);
    }
    
    $fromtime = explode('_', $_POST['fromtime']);
    $untiltime = explode('_', $_POST['untiltime']);

    try {
      $eventstart =  new DateTime($_POST['startdate'] . ' ' . $fromtime[0] . ':' . $fromtime[1], new DateTimeZone('Europe/Zurich'));
      $eventend =  new DateTime($_POST['enddate'] . ' ' . $untiltime[0] . ':' . $untiltime[1], new DateTimeZone('Europe/Zurich'));
      $params['eventstart'] = $eventstart->format('c');
      $params['eventend'] = $eventend->format('c');
    }
    catch(Event $e) {
      $m = 2;
    }

    if ($m==0) {
      $url = '/events/v1/events/' . (int)$_POST['event_id'];
      $response = $this->api->fetch($url, $params, 'PUT');
      if ($response->code==200) {
	$m=1;
      }
      else {
	$m = $response->code;
	error_log('could not open event ' . $_GET['event_id'] . ' Code(' . $response->code .')');
      }
    }

    wp_redirect(admin_url('admin.php?page=sitzig_events&event_id=' . $_POST['event_id'] . '&m='.$m));
    exit;
  }

  function process_new()
  {
    // Check nonce field
    check_admin_referer('sitzig_event_verify');

    $m=0;

    // do the update
    $params = array();
    $fields = array('eventtitle','place','address','city');
    foreach($fields as $field) {
      $params[$field] = sanitize_text_field($_POST[$field]);
    }
    
    $fromtime = explode('_', $_POST['fromtime']);
    $untiltime = explode('_', $_POST['untiltime']);

    try {
      $eventstart =  new DateTime($_POST['startdate'] . ' ' . $fromtime[0] . ':' . $fromtime[1], new DateTimeZone('Europe/Zurich'));
      $eventend =  new DateTime($_POST['enddate'] . ' ' . $untiltime[0] . ':' . $untiltime[1], new DateTimeZone('Europe/Zurich'));
      $params['eventstart'] = $eventstart->format('c');
      $params['eventend'] = $eventend->format('c');
    }
    catch(Event $e) {
      $m = 2;
    }

    $params['group'] = $this->options['groupid'];
    if (!$params['group']) {
      $m = 3;
    }
    
    if ($m==0) {

      $params['title'] = $params['eventtitle'];
      $params['comment'] = '';
      $url = '/events/v1/';

      $response = $this->api->fetch($url, $params, 'POST');
      if ($response->code==200) {
	$m=11;
	$this->event = $response->body;
      }
      else {
	$m = $response->code;
	error_log('could not open event ' . $_GET['event_id'] . ' Code(' . $response->code .')');
      }
    }

    if ($m==11) {
      wp_redirect(admin_url('admin.php?page=sitzig_events&event_id=' . $this->event->eventid . '&m='.$m));
    } else {
      wp_redirect(admin_url('admin.php?page=sitzig_events&new=1&m='.$m));
    }
    exit;
  }

  function setup_hooks()
  {
    add_action('init', array($this, 'init'));
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_menu', array($this, 'plugin_menu'));
    add_action('admin_post_sitzig_edit', array($this, 'process_edit'));
    add_action('admin_post_sitzig_new', array($this, 'process_new'));
  }

  function register_settings() {
    register_setting('eventgroup_group', 'eventgroup');
    
    add_settings_section(
      'eventgroup_main',
      'Main Settings', 
      function() { 
	echo '<p>Event Group ID </p>';
      }, 
      'eventgroup_admin');
    
    add_settings_field(
      'groupid',
      'Group ID', 
      array($this, 'groupid_form'),
      'eventgroup_admin', 
      'eventgroup_main'
      );
    $this->options = get_option('eventgroup');
  }

  function groupid_form()
  {
    printf('<input type="text" id="groupid" name="eventgroup[groupid]" value="%s">',
	   isset($this->options['groupid']) ? esc_attr($this->options['groupid']) : '');
  }
  
  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

//    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sitzig-admin.css', array(), $this->version, 'all' );

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

//    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sitzig-admin.js', array( 'jquery' ), $this->version, false );

  }

	
  function AddItemSelect($name, $desc, $options, $checked='', $box=0, $plain=0, $script='', $opt=0)
  {
    $optgrp = false;
	  
    echo '<select name="';
    echo $name;
    if ($box)
    {
      if ((count($options) < $box) && !(($opt & 2)==2))
	$box = count($options);
      echo '" size="' . $box;
      if (($opt & 1)==1)
	echo '" multiple="multiple';
    }
    echo '"';
    if (strlen($script))
      echo ' ' . $script . ' ';
    echo '>';
    foreach($options as $option)
    {
      if (isset($option[2]) && ($option[2]===false)) {
	if ($optgrp)
	  echo '</optgroup>';			    
	echo '<optgroup label="' . $option[1] . '">';
      }
      else {
	echo '<option value="';
	echo $option[0];
	echo '"';
	if (!strcmp($option[0], $checked))
	  echo ' selected';
	if (isset($option['enabled']) && ($option['enabled']===false)) {
	  echo ' disabled="disabled"';
	} 
	echo '>';
	echo $option[1];
	echo '</option>';
      }
    }
    if ($optgrp)
      echo '</optgroup>';	
    echo '</select>';
  }

}

