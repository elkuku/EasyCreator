<?php
/**
 * User: elkuku
 * Date: 08.06.12
 * Time: 10:14
 */

echo date(DATE_ATOM);

$u = new EcrProjectUpdateserver($this->project);

$u->create($this->project);



echo '<pre>';
$f = new EcrProjectFeed;

$f->title = 'Der erste Feed';
$f->link = 'http://qqq';
$f->description = 'Die erste Beschreibung';

$i = new EcrProjectFeedItem;

$i->title = 'Item titel';
$i->description = 'Item Besxchreibung';
$i->link = 'http://yyyy';

$f->addItem($i);

echo htmlentities($f);

echo "\n\n";

echo htmlentities($f->printPretty());
echo '</pre>';
