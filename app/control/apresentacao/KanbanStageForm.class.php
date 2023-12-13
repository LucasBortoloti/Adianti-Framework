<?php

use Adianti\Control\TWindow;

class KanbanStageForm extends TWindow
{
    protected $form;
    
    // trait with onSave, onClear, onEdit
    use Adianti\Base\AdiantiStandardFormTrait;
    
    function __construct()
    {
        parent::__construct();
        parent::setSize(400, null);
        parent::removePadding();
        parent::setTitle('Kanban Stage');
        
        $this->setDatabase('kanban');    // defines the database
        $this->setActiveRecord('KanbanStage');   // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_City');
        
        // create the form fields
        $id          = new THidden('id');
        $title       = new TEntry('title');
        $stage_order = new THidden('stage_order');
        $id->setEditable(FALSE);
        
        // add the form fields
        $this->form->addFields( [$id] );
        $this->form->addFields( [new TLabel('Title', 'red')], [$title] );
        $this->form->addFields( [$stage_order] );
        
        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        
        $this->setAfterSaveAction( new TAction( ['KanbanView', 'onLoad'] ) );
        $this->setUseMessages(FALSE);
        
        TScript::create('$("body").trigger("click")');
        TScript::create('$("[name=title]").focus()');
        
        parent::add($this->form);
    }
    
}
