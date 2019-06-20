# BidFeed

[![Build Status](https://travis-ci.org/tpg/bidfeed.svg?branch=master)](https://travis-ci.org/tpg/bidfeed)

__Bid or Buy XML Feed Library__

BidFeed is an XML generator for the Bid or Buy XML feed.

## Installation

BidFeed should be installed through Composer:

```bash
composer require thepublicgood/bidfeed
```

## Usage
You'll need to `require` the Composer autoloader if you're not using a framework that does so automatically.

Start by creating a new instance of `TPG\BidFeed\Builder`. The methods on the `Builder`.

```php
require __DIR__.'/vendor/autoload.php';

$feed = new TPG\BidFeed\Builder();
```

The `Builder` class provides a `products()` method that returns a `TPG\BidFeed\Collection` instance. You can add new products to the feed by pushing new instances of `TPG\BidFeed\Product`.

```php
$product = new Product();

$feed->products()->push($product);
```

## Generating the XML
At any point, you can get a copy of the XML by calling the `toXml()` method on the `Builder` instance.

```php
$xml = $feed->toXml();

// You can also save it directly to a file by passing a filename:
$feed->toXml('feed.xml');
```

Calling the `toXml()` method does not alter the instance in any way, so you can continue to build up products even after generating an XML output.

The `toXml()` method will also call the `verifyAttributes()` method on each `Product` instance. If there is anything missing, a `MissingRequiredAttribute` exception will be thrown.

## Adding Products
You build up a feed by adding products. The `Product` class provides a simple API for working with products.

A single product is represented by an instance of `Product`. the `Builder::products()` method always returns a `Collection` instance containing a set of `Product` instances.

```php
// You can...
$feed->products()->push((new Product())->...);

// or add multiple products at once...
$feed->products()->push([$product1, $product2]);
```

## Required Attributes

```php
$product->name($productName)
    ->code($productCode)
    ->category($category)
    ->price($price, $marketPrice)
    ->availableQuantity($quantity)
    ->description($description);
```

Some attributes are generally not required unless the product needs to appear in Google Ads posted by Bid or Buy, then these attributes MUST be supplied:

```php
$product->gtin($productGtin)        // GTIN (GTIN-12 or GTIN-13)
    ->mpn($mpn);                    // MPN
```

A number of attributes are required. All products must have a name, a product code, a category, price, quantity, and description. If any of these attributes are missing on a single product, a `MissingRequiredAttribute` exception will be thrown.

## Setting a product code

A product MUST have a unique product code and it cannot change. Any duplicate product codes will be ignored an the product will not be imported. The product code has a maximum length of 100 characters.

```php
$product->code('CODE-000111');
```

## Setting a product name
All products must be named. You can specify the product name using the `name` method. Product names cannot be longer than 100 characters:

```php
$product->name('My Product');
```

## Setting a description
The description of the product should include the details of the product. It cano be upto 8000 characters long and can include some limited HTML. The BidFeed library currently allows `P` and `BR` html elements.

```php
$product->description('<p>Product description</p>');
```

## Setting a category
A product category must be supplied with app products and preferably based on Google's taxonomy. See [https://support.google.com/merchants/answer/6324436](). It's recommended that the full category path be specified. The `category` method accepts an array of category names. So if the category should be `Electroncis - Laptops - Apple` you can do:

```php
$product->category(['Electronics', 'Laptops', 'Apple']);
```

## Setting Price
All products MUST have a price set. There are two prices that a product can have. The actual selling price, which is required, and a market price, which is optional. The market price is only for reference and not used as a selling price in any way.

You can set both prices by passing the selling price, and the market price at floats to the `price()` method, or set the market price separately using the `marketPrice()` method.

```php
// Set a selling price
$product->price(199.95);

// Set a selling price and market price
$product->price(199.95, 219.95);

// Set a marketing price
$product->marketPrice(211.50);
```

## Available Quantity
Bid or Buy require that an available quantity be specified. You can specify the quantities using the `availableQuantity()` method. Any product with a quantity of 0 will not be imported.

```php
$product->availableQuantity(10);
```

## Product Images
Images must included with all products. At least one image must be provided. You can provide more than one, but only the first image will be used as the product cover image. Images are displayed by Bid or Buy in the order they are provided.

You can provide a set of image URLs by passing an array to the `images()` method.

```php
$product->images([$image1, $image2]);
```

Images are stored as a `Collection` instance. You can get to the instance using the `imageCollection()` method.

```php
// Add an image to the collection
$product->imageCollection()->add($imageUrl3);
```

## Product Attributes
Products can have an optional set of custom attributes. It's common to add a `Brand` attribute here, but you can add any attributes needed. According to the Bid or Buy feed spec, attributes that do not match marketplace values are ignored, so you should only add attributes that are category specific.

```php
$product->productAttributes([
    'Brand' => 'Apple'
]);
```

## Guarantees and Warranties
Products can have a gaurantee and/or a warranty set. You can set gaurantees using the `guarantee` method, and warranties can be set using the `warranty` method. The signature for both methods is identical:

```php
$product->guarantee($type, $text);
$product->warranty($type, $text);
```

BidFeed provides a set of constants for gaurantee and warranty types:

```php
GUARANTEE_NOT_OFFERED;      // No guarantee
GUARANTEE_MONEY_BACK_7;     // 7 day money back guarantee
GUARANTEE_MONEY_BACK_10;    // 10 day
GAURANTEE_MONEY_BACK_15;    // 15 day
GUARANTEE_MONEY_BACK_30;    // 30 day
GUARANTEE_REPLACEMENT_7;    // 7 day replacement guarantee
GUARANTEE_REPLACEMENT_10;   // 10 day
GAURANTEE_REPLACEMENT_15;   // 15 day
GUARANTEE_REPLACEMENT_30;   // 30 day

WARRANTY_NOT_OFFERED;       // No warranty
WARRANTY_REPLACEMENT;       // Replacement warranty
WARRANTY_DEALER;            // Dealers warranty
WARRANTY_MANUFACTURER;      // Manufacturers warranty
```

The second parameter allows you to specify more detail around the guarantee or warranty. The text MUST be provided if the first parameter is anything but `GUARANTEE_


## Shipping Class
