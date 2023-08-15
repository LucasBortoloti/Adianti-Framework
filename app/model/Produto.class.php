<?php

use Adianti\Database\TRecord;

/**
 * Produto Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Produto extends TRecord
{
    const TABLENAME = 'produto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('nome');       
        parent::addAttribute('sale_price'); 
        parent::addAttribute('estoque');
    }
}

