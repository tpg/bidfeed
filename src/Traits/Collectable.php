<?php

namespace TPG\BidFeed\Traits;

use TPG\BidFeed\Collection;

/**
 * Trait Collectable
 * @package TPG\BidFeed\Traits
 */
trait Collectable
{
    /**
     * @var Collection
     */
    protected $parent;

    /**
     * Set or get the parent collection
     *
     * @param Collection|null $collection
     * @return Collection|null
     */
    public function parent(Collection $collection = null): ?Collection
    {
        if ($collection) {
            $this->parent = $collection;
        }

        return $this->parent;
    }

    public function delete()
    {
        if ($this->parent) {

            $this->parent->delete($this);

        }
    }
}
