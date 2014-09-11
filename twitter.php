<?php

require dirname(__FILE__).'/vendor/autoload.php';
include('include/timeago.inc.php');

$settings = array(
    'oauth_access_token' => "1111691-pdg2QVWI7nZCK3kuXLr1N1H1eeZFU2JYyDjyDn6REp",
    'oauth_access_token_secret' => "pn6ZICOe8jhhMi6ea6ZWss2BmoLfH6g5sMgZuIDf8L5WB",
    'consumer_key' => "ghaP4TjZ2UxULEIjKawWN5xT3",
    'consumer_secret' => "qwPOsWaGHAYdiaQuzvDcl90sbSLf60Tvcc2bO3uVOb4jUMmZVB"
);
$twitter = new \TwitterAPIExchange($settings);

$tweets = json_decode($twitter->setGetField('?screen_name=AU_SIS&exclude_replies=false&include_rts=true&count=5&trim_user=true')->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')->performRequest(), true);

ob_start();
foreach ($tweets as $tweet)
{
  ?><li>
      <div><?php echo $tweet['text']; ?></div>
      <?php echo timeAgoInWords($tweet['created_at']); ?>
    </li>
<?php
}
file_put_contents('events/twitter.html', ob_get_contents());
