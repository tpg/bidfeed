<?php

namespace TPG\BidFeed;

use TPG\BidFeed\Traits\Collectable;
use TPG\BidFeed\Traits\HasAttributes;
use TPG\BidFeed\Exceptions\MissingRequiredAttribute;

class Product
{
    use HasAttributes, Collectable;

    const CONDITION_NEW = 'New';
    const CONDITION_SECONDHAND = 'Secondhand';
    const CONDITION_REFURBISHED = 'Refurbished';

    const GUARANTEE_NOT_OFFERED = 0;
    const GUARANTEE_MONEY_BACK_7 = 7;
    const GUARANTEE_MONEY_BACK_10 = 10;
    const GUARANTEE_MONEY_BACK_15 = 15;
    const GUARANTEE_MONEY_BACK_30 = 30;
    const GUARANTEE_REPLACEMENT_7 = 1007;
    const GUARANTEE_REPLACEMENT_10 = 1010;
    const GUARANTEE_REPLACEMENT_15 = 1015;
    const GUARANTEE_REPLACEMENT_30 = 1030;

    const WARRANTY_NOT_OFFERED = 0;
    const WARRANTY_REPLACEMENT = 1;
    const WARRANTY_DEALER = 2;
    const WARRANTY_MANUFACTURER = 3;

    /**
     * @var Collection
     */
    protected $images;

    /**
     * @var Collection
     */
    protected $productAttributes;

    /**
     * @var array
     */
    protected $textFields = [
        'ProductName',
        'Category',
        'ProductMPN',
        'Description',
    ];

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->requiredAttributes = [
            'Category',
            'Price',
            'ProductName',
            'ProductCode',
            'AvailableQuantity',
        ];
    }

    /**
     * Set the product code.
     *
     * @param string $code
     * @return Product
     */
    public function code(string $code): self
    {
        $this->attributes['ProductCode'] = substr(strip_tags($code), 0, 100);

        return $this;
    }

    /**
     * Set the product GTIN.
     *
     * @param string $gtin
     * @return Product
     */
    public function gtin(string $gtin): self
    {
        $this->attributes['ProductGTIN'] = strip_tags($gtin);

        return $this;
    }

    /**
     * Set the product MPN.
     *
     * @param string $mpn
     * @return Product
     */
    public function mpn(string $mpn): self
    {
        $this->attributes['ProductMPN'] = strip_tags($mpn);

        return $this;
    }

    /**
     * Set a product name.
     *
     * @param string $name
     * @return Product
     */
    public function name(string $name): self
    {
        $this->attributes['ProductName'] = $name;

        return $this;
    }

    /**
     * Set a category.
     *
     * @param array $signature
     * @return Product
     */
    public function category(array $signature): self
    {
        $this->attributes['Category'] = implode(' - ', $signature);

        return $this;
    }

    /**
     * Set a selling price.
     *
     * @param float $price
     * @param float|null $marketPrice
     * @return Product
     */
    public function price(float $price, float $marketPrice = null): self
    {
        $this->attributes['Price'] = $price;

        if ($marketPrice) {
            $this->marketPrice($marketPrice);
        }

        return $this;
    }

    /**
     * Set a market price.
     *
     * @param float $marketPrice
     * @return Product
     */
    public function marketPrice(float $marketPrice): self
    {
        $this->attributes['MarketPrice'] = $marketPrice;

        return $this;
    }

    /**
     * Set if offers should be allowed.
     * @param bool $allow
     * @return Product
     */
    public function allowOffers($allow = true): self
    {
        $this->attributes['AllowOffer'] = $allow ? 'true' : 'false';

        return $this;
    }

    /**
     * Set the available quantity.
     *
     * @param int $quantity
     * @return Product
     */
    public function availableQuantity(int $quantity): self
    {
        $this->attributes['AvailableQuantity'] = $quantity;

        return $this;
    }

    /**
     * Set the product description.
     *
     * @param string $condition
     * @return Product
     */
    public function condition(string $condition): self
    {
        $this->attributes['Condition'] = $condition;

        return $this;
    }

    /**
     * Set a product description.
     *
     * @param string $description
     * @return Product
     */
    public function description(string $description): self
    {
        $this->attributes['Description'] = substr(strip_tags($description, 'p,br'), 0, 8000);

        return $this;
    }

    /**
     * Add images to the Product.
     *
     * @param array $images
     * @return Product
     */
    public function images(array $images): self
    {
        $this->images = new Collection($images);

        return $this;
    }

    /**
     * Get the images Collection instance.
     *
     * @return Collection
     */
    public function imageCollection(): Collection
    {
        return $this->images ?: $this->images = new Collection();
    }

    /**
     * Add optional product attributes.
     *
     * @param array $attributes
     * @return Product
     */
    public function productAttributes(array $attributes): self
    {
        $this->productAttributes = new Collection($attributes);

        return $this;
    }

    /**
     * @return Collection
     */
    public function productAttributesCollection(): Collection
    {
        return $this->productAttributes ?: $this->productAttributes = new Collection();
    }

    /**
     * Set the product guarantee type and description.
     *
     * @param int $type
     * @param string $text
     * @return $this
     */
    public function guarantee(int $type, string $text)
    {
        $this->attributes['GuaranteeType'] = $type;
        $this->attributes['GuaranteeText'] = substr($text, 0, 300);

        return $this;
    }

    /**
     * Set the product warranty type and description.
     *
     * @param int $type
     * @param string $text
     * @return $this
     */
    public function warranty(int $type, string $text)
    {
        $this->attributes['WarrantyType'] = $type;
        $this->attributes['WarrantyText'] = substr($text, 0, 300);

        return $this;
    }

    /**
     * Set the product shipping class.
     *
     * @param string $class
     * @return $this
     */
    public function shippingProductClass(string $class)
    {
        $this->attributes['ShippingProductClass'] = $class;

        return $this;
    }

    /**
     * Set the marketplace location.
     *
     * @param string $location
     * @return $this
     */
    public function location(string $location)
    {
        $this->attributes['Location'] = $location;

        return $this;
    }

    /**
     * Verify the images.
     *
     * @throws MissingRequiredAttribute
     */
    public function verifyImages()
    {
        if ($this->images->count() === 0) {
            throw new MissingRequiredAttribute('ImageURL');
        }
    }

    /**
     * Return the product as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter($this->attributes, function ($attributes) {
            return $attributes ? true : false;
        });
    }

    /**
     * Add attributes to the XML element.
     *
     * @param \DOMNode $node
     * @param array|null $attributes
     */
    protected function addAttributesToXmlElement(\DOMNode $node, array $attributes = null)
    {
        foreach ($attributes as $key => $value) {
            if ($value) {
                $productNode = new \DOMElement($key);

                $node->appendChild($productNode);

                if (is_array($value)) {
                    $this->addAttributesToXmlElement($productNode, $value);
                } else {
                    if (in_array($key, $this->textFields)) {
                        $value = $node->ownerDocument->createCDATASection($value);
                        $productNode->appendChild($value);
                    } else {
                        $productNode->textContent = $value;
                    }

                    $node->appendChild($productNode);
                }
            }
        }
    }

    /**
     * Add images to the XML element.
     *
     * @param \DOMNode $node
     */
    protected function addImagesToXmlElement(\DOMNode $node)
    {
        $imagesNode = new \DOMElement('Images');
        $node->appendChild($imagesNode);

        foreach ($this->images as $image) {
            $imageNode = new \DOMElement('ImageURL');
            $imagesNode->appendChild($imageNode);
            $value = $node->ownerDocument->createCDATASection($image);
            $imageNode->appendChild($value);
        }
    }

    /**
     * Add the product attributes to the XML element.
     *
     * @param \DOMNode $node
     */
    public function addProductAttributesToXmlElement(\DOMNode $node)
    {
        $attributesNode = new \DOMElement('ProductAttributes');
        $node->appendChild($attributesNode);

        foreach ($this->productAttributes as $key => $value) {
            $attribute = new \DOMElement($key);
            $attributesNode->appendChild($attribute);
            $attribute->textContent = $value;
        }
    }

    /**
     * @param \DOMNode $node
     * @return void
     * @throws MissingRequiredAttribute
     */
    public function toXmlNode(\DOMNode $node)
    {
        $this->verifyAttributes();
        $this->verifyImages();
        $this->addAttributesToXmlElement($node, $this->attributes);
        $this->addImagesToXmlElement($node);

        if ($this->productAttributes && $this->productAttributes->count()) {
            $this->addProductAttributesToXmlElement($node);
        }
    }
}
