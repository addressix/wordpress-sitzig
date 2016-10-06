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
<h1>Events
<a class="page-title-action" href="<?php echo admin_url('admin.php?page=sitzig_events&new=1'); ?>">Erstellen</a>
</h1>
<p>
<?php
echo '<table class="wp-list-table widefat fixed striped">';
if (isset($this->events->events)) {
  foreach($this->events->events as $event) {
    echo '<tr class="eventitem">';
    $startd = new \Datetime($event->eventstart);
    $startd->setTimezone(new DateTimeZone('Europe/Zurich'));

    $startdate = date_i18n('d. M Y', $startd->getTimestamp());
//    $starttime = date_i18n('H:i', $startd->getTimestamp(), true);
    $starttime = $startd->format('H:i');
    ?>
      <td valign="top">
         <div class="datetime">
         <?php echo $startdate ?><br>
         <?php echo $starttime; ?>
         </td>
             <td valign="top" class="entry">
	     
	     <div class="title">
	     <?php if (isset($event->permission) && ($event->permission>1)) { ?>
									      <a href="<?php echo add_query_arg('event_id', $event->eventid); ?>"><?php if ($event->eventtitle) echo $event->eventtitle; else echo 'Kein Titel';?></a></div>										
										<?php } else { 
      echo $event->title;
    }
     ?> 
							       <?php
							       if (isset($event->comment)) { echo $event->comment . '<br>'; }
    if ($event->place) { echo $event->place . '<br>'; }
    if ($event->address) { echo $event->address; }
    if ($event->address && $event->city) { echo ', '; }
    if ($event->city) { echo $event->city; }
    ?>
      </td>
          <td class="owner" style="vertical-align: top">
          <?php echo $event->ownername; ?>
          </td>
	      </tr>
	      <?php } 
}
echo '</table></div>';
