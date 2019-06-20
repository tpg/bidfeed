<?php

namespace TPG\Test;

use DateTime;
use TPG\BidFeed\Builder;
use TPG\BidFeed\Product;
use TPG\BidFeed\Collection;

class BuilderTest extends TestCase
{
    /**
     * @param int $count
     * @return Product[]
     */
    private function createProduct($count = 1)
    {
        $products = [];
        for ($i = 0; $i < $count; $i++) {
            $products[] = (new Product())
                ->code($code = rand(1000, 2000))
                ->name('Test Product '.$code)
                ->category(['Electronics', 'Laptops', 'Apple'])
                ->price(199.95)
                ->availableQuantity(10)
                ->images([
                    'https://test.test/images/image1.jpg',
                ])
                ->description('The product description');
        }

        return $products;
    }

    /**
     * @test
     */
    public function it_can_have_version_information()
    {
        $feed = new Builder();

        $now = new DateTime();
        $feed->version('BidFeed v1.0.0');
        $feed->created($now);

        $this->assertEquals('BidFeed v1.0.0', $feed->toArray()['Version']);
        $this->assertEquals($now->format(DATE_ATOM), $feed->toArray()['ExportCreated']);
    }

    /**
     * @test
     */
    public function it_can_create_a_new_feed()
    {
        $feed = new Builder();

        $feed->products()->push($product = $this->createProduct()[0]);

        $this->assertCount(1, $feed->products());
        $this->assertEquals($product->toArray()['ProductName'], $feed->products()->first()->toArray()['ProductName']);
    }

    /**
     * @test
     */
    public function a_product_can_have_an_image_collection()
    {
        $product = $this->createProduct()[0];

        $this->assertInstanceOf(Collection::class, $product->imageCollection());
        $this->assertCount(1, $product->imageCollection());
    }

    /**
     * @test
     */
    public function collection_items_have_the_collection_as_a_parent()
    {
        $builder = new Builder();

        $builder->products()->push($this->createProduct());

        $this->assertInstanceOf(Collection::class, $builder->products()->first()->parent());
    }

    /**
     * @test
     */
    public function a_product_can_be_deleted_from_the_collection()
    {
        $builder = new Builder();

        $builder->products()->push($this->createProduct());

        $builder->products()->first()->delete();

        $this->assertCount(0, $builder->products());
    }

    /**
     * @test
     */
    public function a_product_can_have_custom_attributes()
    {
        $builder = new Builder();

        $product = $this->createProduct()[0];
        $product->productAttributes([
            'Brand' => 'Products Inc.',
        ]);

        $builder->products()->push($product);

        $this->assertEquals('Products Inc.', $builder->products()->first()->productAttributesCollection()->find('Brand'));
    }

    /**
     * @test
     */
    public function the_builder_can_generate_xml()
    {
        $builder = new Builder();

        $products = $this->createProduct(2);
        $products[0]->productAttributes([
            'Brand' => 'Products Inc.',
        ]);

        $builder->products()->push($products);

        $xml = simplexml_load_string($builder->toXml(__DIR__.'/test.xml'));

        $this->assertEquals($products[0]->toArray()['ProductName'], (string) $xml->Products->Product[0]->ProductName);
        $this->assertEquals($products[1]->toArray()['ProductCode'], (string) $xml->Products->Product[1]->ProductCode);

        $this->assertEquals('Products Inc.', $xml->Products->Product[0]->ProductAttributes->Brand);

        $this->assertFileExists(__DIR__.'/test.xml');
        unlink(__DIR__.'/test.xml');
    }

    /**
     * @test
     */
    public function products_can_have_guarantees_and_warranties()
    {
        $product = $this->createProduct()[0];
        $product->guarantee(Product::GUARANTEE_MONEY_BACK_10, '10 day money back guarantee');
        $product->warranty(Product::WARRANTY_DEALER, 'Dealer warranty');

        $builder = new Builder();
        $builder->products()->push($product);

        $this->assertEquals(10, $builder->products()->first()->toArray()['GuaranteeType']);
        $this->assertEquals('10 day money back guarantee', $builder->products()->first()->toArray()['GuaranteeText']);
        $this->assertEquals(2, $builder->products()->first()->toArray()['WarrantyType']);
        $this->assertEquals('Dealer warranty', $builder->products()->first()->toArray()['WarrantyText']);
    }
}
