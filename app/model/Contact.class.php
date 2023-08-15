<?php

use Adianti\Database\TRecord;

/**
 * Contact Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Contact extends TRecord
{
    const TABLENAME = 'contact';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('name');       
        parent::addAttribute('email');       
        parent::addAttribute('number');       
        parent::addAttribute('address');       
        parent::addAttribute('notes'); 
    } 

}