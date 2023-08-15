<?php

use Adianti\Control\TPage;

class FormInteractionView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Interacoes');

        $campoA = new TEntry('campoA');
        $campoB = new TEntry('campoB');

        $campoC = new TCombo('campoC');
        $campoD = new TCombo('campoD');

        $campoE = new TRadioGroup('campoE');
        $campoF = new TEntry('campoF');


        $campoC->addItems ( array('F' => 'Fiat', 'V' => 'VW', 'G' => 'GM', 'O' => 'Ford'));
        $campoE->addItems ( array('H' => 'Habilita', 'D' => 'Desabilita' ));


        $this->form->addQuickField('campo A', $campoA, 200);
        $this->form->addQuickField('campo B', $campoB, 200);
        $this->form->addQuickField('campo C', $campoC, 200);
        $this->form->addQuickField('campo D', $campoD, 200);
        $this->form->addQuickField('campo E', $campoE, 200);
        $this->form->addQuickField('campo F', $campoF, 200);

        $exitAction = new TAction( array($this, 'onExitAction'));
        $campoA->setExitAction($exitAction);

        $changeAction = new TAction( array($this, 'onChangeAction'));
        $campoC->setChangeAction( $changeAction );

        $enableAction = new TAction( array($this, 'onEnableAction'));
        $campoE->setChangeAction($enableAction);

        parent::add($this->form);

    }

    public static function onExitAction( $param ){

        $obj = new StdClass;
        $obj->campoB = "Você digitou no campoA: " . $param['campoA'];

        TForm::sendData('form1', $obj);
    }

    public static function onChangeAction( $param ){
        $carros['F'] = array('P'=>'Palio', 'U'=> 'Uno');
        $carros['G'] = array('C'=>'Celta', 'P'=> 'Prisma');

        TCombo::reload('form1', 'campoD', $carros [ $param ['campoC'] ]);

    }

    public static function onEnableAction( $param ){
        
        if ($param['campoE'] == 'H'){
            TEntry::enableField('form1', 'campoF');
        }
        else{
        TEntry::disableField('form1', 'campoF');
        TEntry::clearField('form1', 'campoF');
        }
    }

    }


?>