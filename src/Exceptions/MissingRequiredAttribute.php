<?php

namespace TPG\BidFeed\Exceptions;

class MissingRequiredAttribute extends \Exception
{
    /**
     * MissingRequiredAttribute constructor.
     *
     * @param string $attribute
     */
    public function __construct(string $attribute)
    {
        $message = 'Required attribute '.$attribute.' is missing';

        parent::__construct($message);
    }
}
