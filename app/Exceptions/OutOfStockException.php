<?php

namespace App\Exceptions;

use Exception;

class OutOfStockException extends Exception
{
    public $stock;
    public $inCart;

    public function __construct($message, $stock, $inCart)
    {
        parent::__construct($message);
        $this->stock = $stock;
        $this->inCart = $inCart;
    }
}
