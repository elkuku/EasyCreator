<?php
/**
 * User: elkuku
 * Date: 07.06.12
 * Time: 11:27
 */

/**
 * Feed class.
 */
class EcrProjectFeed
{
    public $title = '';

    public $subtitle = '';

    public $description;

    public $link = '';

    public $author = '';

    public $id = '';

    public $updated = '';

    /**
     * @var array EcrFeedItem
     */
    protected $items = array();

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path = '')
    {
        if('' != $path)
        {
            $xml = EcrProjectHelper::getXML($path);

            if($xml)
            {
                $this->title = (string)$xml->title;
                $this->link = (string)$xml->link->attributes()->href;
                $this->author = (string)$xml->author->name;

                foreach($xml->entry as $entry)
                {
                    $i = new EcrProjectFeedItem;

                    $i->title = (string)$entry->title;
                    $i->link =($entry->link) ? (string)$entry->link->attributes()->href : '';
                    $i->id = (string)$entry->id;
                    $i->summary = (string)$entry->summary;
                    $i->updated = (string)$entry->updated;
                    $i->author = (string)$entry->author->name;

                    $this->items[] = $i;
                }
            }
        }
    }

    /**
     * @param EcrProjectFeedItem $item
     *
     * @return Feed
     */
    public function addItem(EcrProjectFeedItem $item)
    {
        array_unshift($this->items, $item);

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
        $xml = simplexml_load_string('<feed xmlns="http://www.w3.org/2005/Atom"/>');

        $updated = date(DATE_ATOM);

        $xml->addChild('title', $this->title);
        $xml->addChild('id', $this->link);
        $xml->addChild('updated', $updated);

        /* @var SimpleXMLElement $l */
        $l = $xml->addChild('link');
        $l->addAttribute('href', $this->link);
        $l->addAttribute('rel', 'self');

        $e = $xml->addChild('author');
        $e->addChild('name', $this->author);

        /* @var EcrProjectFeedItem $item */
        foreach($this->items as $item)
        {
            $updated = $item->updated ? : date(DATE_ATOM);

            /* @var SimpleXMLElement $i */
            $i = $xml->addChild('entry');

            $i->addChild('title', $item->title);

            if($item->link)
            {
                $l = $i->addChild('link');
                $l->addAttribute('href', $item->link);
            }

            $i->addChild('id', $item->id);

            $a = $i->addChild('author');
            $a->addChild('name', 'x');

            $i->addChild('updated', $updated);

            $s = $i->addChild('summary', $item->summary);
            $s->addAttribute('type', 'html');

            $s = $i->addChild('content', $item->content);
            $s->addAttribute('type', 'html');
        }

        return $xml;
    }
}
