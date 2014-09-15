<?php

require dirname(__FILE__).'/vendor/autoload.php';

if ($_GET['cal'] == 'sis')
{
  // SIS Upcoming Events
  $feedURL = "http://www.american.edu/customcf/calendar/rss.cfm?h=89,321";
}
else
{
  // Alumni Events
  $feedURL = "http://alumniassociation.american.edu/controls/cms_v2/components/rss/rss.aspx?sid=1395&gid=1&calcid=1401&page_id=338";
}

$feed = new \SimplePie();
$feed->enable_cache(true);
$feed->enable_order_by_date(false);
$feed->set_feed_url($feedURL);
$feed->init();
$events = array();

foreach ($feed->get_items() as $item)
{
  if ($_GET['cal'] == 'sis')
  {
    $outputFilename = "sis.html";
    list($time, $title) = explode(': ', $item->get_title(), 2);
    $events[] = array('realDate' => date('Y-m-d H:i:s', strtotime($time)), 'date' => $time, 'title' => $title);
  }
  else
  {
    // Alumni Events
    $outputFilename = "alumni.html";
    // <span>Date:</span> 9/11/2014 5:45 PM to 7:30 PM
    if (preg_match('/<span>Date:<\/span>.*?((\d+\/\d+\/\d+)( \d+:\d+ \w+)?( to \d+:\d+ \w+)?)/', $item->get_description(), $matches))
    {
      if (strtotime($matches[2]) < (time()-86400))
        continue;
      $events[] = array('realDate' => date('l, F d, Y', strtotime($matches[2])), 'date' => date('l, F d, Y', strtotime($matches[2])), 'title' => $item->get_title());
    }
    else
      die($item->get_description());
    usort($events, 'cmp');
  }
}

function cmp($a, $b)
{
  return (strtotime($a['realDate']) < strtotime($b['realDate'])) ? -1 : 1;
}

ob_start();
// SIS Events Loop
$count = 0;
foreach ($events as $event)
{
  if (++$count > 5) 
    break;
  ?>
    <li>
      <div><?php echo $event['title']; ?></div>
      <?php echo $event['date']; ?>
    </li>
  <?php
}

file_put_contents('events/' . $outputFilename, ob_get_contents());
