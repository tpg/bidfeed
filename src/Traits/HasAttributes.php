<?php

namespace TPG\BidFeed\Traits;

use TPG\BidFeed\Exceptions\MissingRequiredAttribute;

trait HasAttributes
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $requiredAttributes = [];

    /**
     * @var null|string
     */
    protected $rootKey = null;

    /**
     * Verify the current attributes.
     *
     * @return bool
     * @throws MissingRequiredAttribute
     */
    public function verifyAttributes()
    {
        foreach ($this->requiredAttributes as $required) {
            if (! in_array($required, array_keys($this->attributes))) {
                throw new MissingRequiredAttribute($required);
            }
        }

        return true;
    }

    /**
     * Output an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
