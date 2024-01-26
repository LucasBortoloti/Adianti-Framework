<?php

use Adianti\Database\TRecord;

/**
 * Product Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Product extends TRecord
{
    const TABLENAME = 'product';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('nome');
        parent::addAttribute('preco');
    }
}
