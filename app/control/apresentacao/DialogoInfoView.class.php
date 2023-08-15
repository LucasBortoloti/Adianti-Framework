<?php

use Adianti\Control\TPage;

class DialogoInfoView extends TPage{

public function __construct(){

    parent:: __construct();


    $action = new TAction( array( 'ContainerNotebookView', 'teste' ));
    
    new TMessage('info', 'Teste de mensagem informativa', $action);




    }
}
?>