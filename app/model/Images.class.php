<?php

use Adianti\Database\TRecord;

/**
 * Images Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Images extends TRecord
{
    const TABLENAME = 'images';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('images');
    } 

}
