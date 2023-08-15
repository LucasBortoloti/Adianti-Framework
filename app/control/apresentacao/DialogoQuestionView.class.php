<?php

use Adianti\Control\TPage;

class DialogoQuestionView extends TPage{

    public function __construct(){

        parent:: __construct();

        $action1 = new TAction ( array($this, 'onSim'));
        $action2 = new TAction ( array($this, 'onNao'));

        $action1->setParameter('codigo', 5000);
        $action2->setParameter('codigo', 6000);

        new TQuestion('Você gostaria de excluir este registro?', $action1, $action2);

    }

    //para rodar no navegado usar o seguinte url: 
    //http://localhost/template/index.php?class=DialogoQuestionView&method=onTeste
    
    public function onSim( $param ){

        //echo 'Você escolheu sim';
        //$this->label->setValue('Você escolheu sim');

        new TMessage('info', 'Você escollheu sim: '. $param['codigo']);
    }

    public function onNao( $param ){

        //echo 'Você escolheu não';
        //$this->label->setValue('Você escolheu não');
        new TMessage('info', 'Você escollheu não: '. $param['codigo']);
    }

    //para rodar no navegado usar o seguinte url: 
    //http://localhost/template/index.php?class=DialogoQuestionView&method=onTeste&codigo=100
   
    public function onTeste( $param ){
        echo "Você executou onTeste: " . $param['codigo'];

    }

}
?>