<?php

use Adianti\Database\TRecord;

class Calendar extends TRecord
{
    const TABLENAME = 'calendar';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('id');
        parent::addAttribute('start_time');
        parent::addAttribute('end_time');
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('color');    
    }

}