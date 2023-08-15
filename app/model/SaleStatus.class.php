<?php

use Adianti\Database\TRecord;

/**
 * SaleStatus Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class SaleStatus extends TRecord
{
    const TABLENAME = 'sale_status';
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
        parent::addAttribute('color');      
    
    }
}