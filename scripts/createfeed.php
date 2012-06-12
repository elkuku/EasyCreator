<?php
/**
 * User: elkuku
 * Date: 07.06.12
 * Time: 11:27
 */

$f = new Feed;

$f->title = 'Der erste Feed';
$f->link = 'http://qqq';
$f->description = 'Die erste Beschreibung';

$i = new FeedItem;

$i->title = 'Item titel';
$i->description = 'Item Besxchreibung';
$i->link = 'http://yyyy';

$f->addItem($i);

echo $f;

echo "\n\n";

echo $f->printPretty();

/**
 * Feed class.
 */
class Feed
{
    public $title = '';

    public $link = '';

    public $description = '';

    public $language = '';

    /**
     * @var array FeedItem
     */
    protected $items = array();

    /**
     * @param FeedItem $item
     *
     * @return Feed
     */
    public function addItem(FeedItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Generate the feed.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toXml()->asXML();
    }

    /**
     * Pretty print for debugging.
     *
     * @return string
     */
    public function printPretty()
    {
        $dom = dom_import_simplexml($this->toXml())->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    /**
     * Convert to XML object.
     *
     * @return SimpleXMLElement
     */
    protected function toXml()
    {
        /* @var SimpleXMLElement $xml */
        $xml = simplexml_load_string('<rss version="2.0"/>');

        /* @var SimpleXMLElement $c */
        $c = $xml->addChild('channel');

        $c->addChild('title', $this->title);
        $c->addChild('link', $this->link);
        $c->addChild('description', $this->description);
        $c->addChild('language', $this->language);

        /* @var FeedItem $item */
        foreach($this->items as $item)
        {
            /* @var SimpleXMLElement $i */
            $i = $c->addChild('item');

            $i->addChild('title', $item->title);
            $i->addChild('link', $item->link);
            $i->addChild('description', '<!CDATA['.$item->description.']]>');
        }

        return $xml;
    }
}

/**
 * Feed item class.
 */
class FeedItem
{
    public $title = '';

    public $link = '';

    public $description = '';
}
