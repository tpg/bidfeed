<?php

namespace TPG\BidFeed;

use Countable;
use ArrayAccess;
use TPG\BidFeed\Traits\Arrayable;
use TPG\BidFeed\Traits\Iteratable;

class Collection implements ArrayAccess, Countable, \Iterator
{
    use Arrayable, Iteratable;


    /**
     * @var array
     */
    protected $items;

    /**
     * Collection constructor.
     * @param ArrayAccess|array|null $items
     */
    public function __construct($items = null)
    {
        $this->items = $items ?: [];
    }

    /**
     * Count the collection items
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Check if a key exists in the collection
     *
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Get the first item in the collection
     *
     * @return mixed|null
     */
    public function first()
    {
        return $this->count() ?
            $this->items[array_keys($this->items)[0]] :
            null;
    }

    /**
     * Get the last item in the collection
     *
     * @return mixed|null
     */
    public function last()
    {
        return $this->count() ?
            $this->items[array_reverse(array_keys($this->items))[0]] :
            null;
    }

    /**
     * Find an item by its key
     *
     * @param mixed $key
     * @return mixed|null
     */
    public function find($key)
    {
        return $this->has($key) ? $this->items[$key] : null;
    }

    /**
     * Push an item onto the collection
     *
     * @param $item
     * @return $this
     */
    public function push($item)
    {
        if (!is_array($item)) {
            $item = [$item];
        }
        $this->items = array_merge($this->items, $item);

        $this->updateParent();

        return $this;
    }

    /**
     * Insert an item at the top of the collection
     *
     * @param $item
     * @return $this
     */
    public function unshift($item)
    {
        array_unshift($this->items, $item);

        return $this;
    }

    /**
     * Pop the last item off the collection
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Shift the first item off the top of the collection
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Delete an item from the collection
     *
     * @param $item
     */
    public function delete($item)
    {
        $index = array_search($item, $this->items);

        if ($index !== false) {
            $this->offsetUnset($index);
        }
    }

    /**
     * Loop other each item in the collection
     *
     * @param callable $callback
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    /**
     * Loop over each item in the collection and apply the result of the callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function map(callable $callback): Collection
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $callback($item);
        }

        return new self($items);
    }

    /**
     * Filter the items in the collection by the result of the callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function filter(callable $callback): Collection
    {
        $items = [];
        foreach ($this->items as $item) {
            if ($callback($item)) {
                $items[] = $item;
            }
        }

        return new self($items);
    }

    /**
     * Return array of collected items
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Products' => array_map(function (Product $product) {
                return $product->toArray();
            }, $this->items)
        ];
    }

    public function toJson($pretty = false): string
    {
        return json_encode($this->toArray(), JSON_NUMERIC_CHECK + ($pretty ? JSON_PRETTY_PRINT : 0));
    }

    public function toXml(\DOMElement $root)
    {
        $element = $root->appendChild(new \DOMElement('Products'));
        foreach ($this->items as $product) {
            $productElement = $element->appendChild(new \DOMElement('Product'));
            $root->appendChild($element);
            $product->toXmlNode($productElement);
        }

        return $root;
    }

    /**
     * Update the parent on collected items
     */
    protected function updateParent()
    {
        $this->each(function ($item) {
            if (method_exists($item, 'parent')) {
                $item->parent($this);
            }
        });
    }
}
