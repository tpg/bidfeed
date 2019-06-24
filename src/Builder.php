<?php

namespace TPG\BidFeed;

use DateTime;

class Builder
{
    /**
     * @var Collection
     */
    protected $products;

    /**
     * @var string
     */
    protected $version = 'BidFeed Library v0.1.0';

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * Set the "plugin" version number.
     *
     * @param string $version
     * @return Builder
     */
    public function version(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set the time the feed was created.
     *
     * @param DateTime $created
     * @return Builder
     */
    public function created(DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function products(): Collection
    {
        return $this->products ?: $this->products = new Collection();
    }

    public function toArray(): array
    {
        return [
            'Version' => $this->version,
            'ExportCreated' => $this->created->format(DATE_ATOM),
            'Products' => $this->products()->toArray(),
        ];
    }

    public function toJson($pretty = false): string
    {
        return json_encode($this->toArray(), JSON_NUMERIC_CHECK + ($pretty ? JSON_PRETTY_PRINT : 0));
    }

    public function toXml(string $filename = null)
    {
        $document = new \DOMDocument();

        $document->encoding = 'UTF-8';

        $root = $document->createElement('ROOT');

        $document->appendChild($root);

        $this->addVersionNode($root);
        $this->addCreatedNode($root);

        $this->products()->toXml($root);

        if ($filename) {
            $document->save($filename);
        }

        return $document->saveXML();
    }

    protected function addVersionNode(\DOMElement $root)
    {
        $versionNode = new \DOMElement('Version');
        $root->appendChild($versionNode);
        $versionNode->textContent = $this->version;
    }

    protected function addCreatedNode(\DOMElement $root)
    {
        $createdNode = new \DOMElement('ExportCreated');
        $root->appendChild($createdNode);
        $createdNode->textContent = $this->created->format(DATE_ATOM);
    }
}
