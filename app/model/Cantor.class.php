<?php

use Adianti\Database\TRecord;

/**
 * Cantor Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Cantor extends TRecord
{
    const TABLENAME = 'cantor';
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
        parent::addAttribute('idade'); 
    } 

}
