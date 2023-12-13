<?php

use Adianti\Control\TPage;

class KanbanItemForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait;
    
    public function __construct()
    {
        parent::__construct();
        parent::setTargetContainer("adianti_right_panel");
        
        $this->setDatabase('kanban');    // defines the database
        $this->setActiveRecord('KanbanItem');   // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_City');
        $this->form->setFormTitle('Kanban item');
        
        // create the form fields
        $id          = new THidden('id');
        $title       = new TEntry('title');
        $content     = new THtmlEditor('content');
        $color       = new TColor('color');
        $item_order  = new THidden('item_order');
        $stage_id    = new THidden('stage_id');
        $id->setEditable(FALSE);
        $title->setSize('100%');
        $color->setSize('100%');
        $content->setSize('100%', 250);
        
        // add the form fields
        $this->form->addFields( [$id] );
        $this->form->addFields( [new TLabel('Title',null,null, 'b')], [$title], [new TLabel('Color', null, null, 'b')], [$color] );
        $this->form->addFields( [$content] );
        $this->form->addFields( [$item_order] );
        $this->form->addFields( [$stage_id] );
        
        // define the form action
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        $this->setAfterSaveAction( new TAction( ['KanbanView', 'onLoad'] ) );
        $this->setUseMessages(FALSE);
        
        TScript::create('$("body").trigger("click")');
        TScript::create('$("[name=title]").focus()');
        
        parent::add($this->form);
    }

    public function onStartEdit($param)
    {
        $data = new stdClass;
        $data->stage_id = $param['id'];
        $data->item_order = 999;
        $this->form->setData($data);
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}