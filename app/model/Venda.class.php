<?php

use Adianti\Database\TRecord;

/**
 * Venda Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Venda extends TRecord
{
    const TABLENAME = 'venda';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('data_venda');       
        parent::addAttribute('cliente'); 
    } 

}