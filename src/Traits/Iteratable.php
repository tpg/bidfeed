<?php

namespace TPG\BidFeed\Traits;

trait Iteratable
{
    /**
     * Get the current item.
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * Move to the next item.
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * Return the key of the current item.
     *
     * @return int|string|null
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Check if the current item position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        $key = key($this->items);

        return $key !== null && $key !== false;
    }

    /**
     * Reset the iterator to the start position.
     */
    public function rewind()
    {
        reset($this->items);
    }
}
