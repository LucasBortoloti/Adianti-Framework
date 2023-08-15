<?php

use Adianti\Database\TRecord;

class KanbanItem extends TRecord
{
    const TABLENAME = 'item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('title');       
        parent::addAttribute('content');       
        parent::addAttribute('color');
        parent::addAttribute('item_order');
        parent::addAttribute('stage_id');
    }
    
}