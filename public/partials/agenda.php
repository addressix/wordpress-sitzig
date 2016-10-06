<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.meworla.com
 * @since      1.0.0
 *
 * @package    Sitzig
 * @subpackage Sitzig/public/partials
 */

echo '<table class="eventlist">';
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
	     
	     <div class="title"><?php echo $event->eventtitle;?></div>
							       <?php
							       if (isset($event->comment)) { echo $event->comment . '<br>'; }
    if ($event->place) { echo $event->place . '<br>'; }
    if ($event->address) { echo $event->address; }
    if ($event->address && $event->city) { echo ', '; }
    if ($event->city) { echo $event->city; }
    ?>
      </td>
          <td class="owner">
          <?php echo $event->ownername; ?>
          </td>
	      </tr>
	      <?php } 
}
echo '</table>';
