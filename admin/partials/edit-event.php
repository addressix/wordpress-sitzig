<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.meworla.com
 * @since      1.0.0
 *
 * @package    Sitzig
 * @subpackage Sitzig/admin/partials
 */
?>
<div class="wrap">
<h1>Event
<a class="page-title-action" href="<?php echo admin_url('admin.php?page=sitzig_events&new=1'); ?>">Erstellen</a>
</h1>
    <?php if ($_GET['m']) {
    $msg = '';
    switch($_GET['m']) {
    case 1:
      $msg = 'Event erfolgreich geändert';      
      break;
    case 2:
      $msg = 'Fehler: Konnte Datum nicht berechnen';
      break;
    case 11:
      $msg = 'Event erfolgreich erstellt';      
      break;
    case 401:
    case 403:
      $msg = 'Keine Berechtigung';
      break;
    default:
      $msg = 'Es ist ein Fehler aufgetreten: ' . $_GET['m'];
      break;
    }
    echo '<div id="message" class="updated fade"><p><strong>' . $msg . '</strong></p></div>';
  }
?>
<form id="event" name="event" method="post" class="event" action="admin-post.php">
    <?php wp_nonce_field('sitzig_event_verify') ?>
    <input type="hidden" name="action" value="sitzig_edit">
    <input type="hidden" name="event_id" value="<?php echo $this->event->eventid; ?>">
<div id="post-body" class="columns-2">
<div id="post-body-content">
    <label id="title-prompt-text" for="eventtitle">Titel</label>
    <input id="eventtitle" type="text" name="eventtitle" size="30" value="<?php echo $this->event->eventtitle; ?>">

<?php
$timesel = array();
      for($i=0; $i<24; $i++) {
	for ($j=0; $j<60; $j+=15)
	  $timesel[] = array(sprintf('%02d_%02d',$i,$j),sprintf('%02d:%02d',$i,$j));	
      }

      // get the date definitions
      try {
	$ta = new \DateTime($this->event->eventstart);
	$ta->setTimezone(new DateTimeZone('Europe/Zurich'));
	$te = new \DateTime($this->event->eventend);
	$te->setTimezone(new DateTimeZone('Europe/Zurich'));
      }
      catch(Exception $e) {
	$ta = new \DateTime();
	$te = new \DateTime();
      }
      try {
	$tr = new \DateTime($this->event->replyuntil);
      }
      catch(Exception $e) {
	$tr = $ta;
      }
      // time select
      $tdef = $ta->format('H_i');
      $tdefe = $te->format('H_i');    
?>

    <label id="title-prompt-text" for="startdate">Zeit</label>
    <input id="startdate" type="text" name="startdate" size="8" value="<?php echo $ta->format('Y-m-d') ?>">
	<?php $this->AddItemSelect('fromtime','',$timesel,$tdef); ?>
 bis <input id="enddate" type="text" name="enddate" size="8" value="<?php echo $te->format('Y-m-d') ?>">
	<?php $this->AddItemSelect('untiltime','',$timesel,$tdefe); ?>

 <label id="place-prompt-text" for="place">Wo</label>
	<input id="place" type="text" name="place" size="30" value="<?php echo $this->event->place ?>">

 <label id="street-prompt-text" for="address">Strasse/Nr</label>
	<input id="street" type="text" name="address" size="30" value="<?php echo $this->event->address ?>">

 <label id="city-prompt-text" for="city">Ort</label>
	<input id="city" type="text" name="city" size="30" value="<?php echo $this->event->city ?>">
<?php /*
<label id="city-prompt-text" for="place">Beschreibung</label>
<textarea id="description" name="description" cols="30" rows="20">
<?php echo $this->event->description ?>
</textarea>
      */ ?>
    <?php submit_button(); ?>

</div><!-- .post-body-content -->
<div id="postbox-container-1" class="postbox-container">
</div>
</div><!-- .post-body -->
</form>