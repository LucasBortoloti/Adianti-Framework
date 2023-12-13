<?php

use Adianti\Database\TRecord;

class KanbanStage extends TRecord
{
    const TABLENAME = 'stage';
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
        parent::addAttribute('stage_order');
    }
    
}