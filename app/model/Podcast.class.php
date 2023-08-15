<?php

use Adianti\Database\TRecord;

/**
 * Podcast Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Podcast extends TRecord
{
    const TABLENAME = 'podcast';
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
        parent::addAttribute('tipo');       
        parent::addAttribute('duracao'); 
    } 

}