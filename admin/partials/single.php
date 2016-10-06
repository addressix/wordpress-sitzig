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
<h1>Event</h1>
<form id="event" name="event" method="post" class="event">
<div id="post-body" class="columns-2">
<div id="post-body-content">
    <label id="title-prompt-text" for="title">Titel</label>
    <input id="title" type="text" name="event_title" size="30" value="<?php echo $this->event->title; ?>">

<?php
$timesel = array();
      for($i=0; $i<24; $i++) {
	for ($j=0; $j<60; $j+=15)
	  $timesel[] = array(sprintf('%02d_%02d',$i,$j),sprintf('%02d:%02d',$i,$j));	
      }

      // get the date definitions
      try {
	$ta = new \DateTime($this->event->eventstart);
	$te = new \DateTime($this->event->eventend);
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
    <input id="startdate" type="text" name="startdate_pick" size="8" value="<?php echo $ta->format('Y-m-d') ?>">
	<?php $this->AddItemSelect('fromtime','',$timesel,$tdef); ?>
 bis <input id="enddate" type="text" name="enddate_pick" size="8" value="<?php echo $te->format('Y-m-d') ?>">
	<?php $this->AddItemSelect('untiltime','',$timesel,$tdefe); ?>

 <label id="place-prompt-text" for="place">Wo</label>
	<input id="place" type="text" name="place" size="30" value="<?php echo $this->event->place ?>">

 <label id="street-prompt-text" for="place">Strasse/Nr</label>
	<input id="street" type="text" name="street" size="30" value="<?php echo $this->event->address ?>">

 <label id="city-prompt-text" for="place">Ort</label>
	<input id="city" type="text" name="city" size="30" value="<?php echo $this->event->city ?>">

<label id="city-prompt-text" for="place">Beschreibung</label>

    <?php submit_button(); ?>

</div><!-- .post-body-content -->
<div id="postbox-container-1" class="postbox-container">
</div>
</div><!-- .post-body -->
</form>