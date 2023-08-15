<?php

use Adianti\Control\TPage;

class RelacaoAssociacaoView extends TPage{

    public function __construct(){

        parent:: __construct();

        try{

        TTransaction::open('filme');

        $filme = new Filme(1);
        
        print $filme->titulo;
        print '<br>';
        print $filme->genero->nome;
        print '<br>';
        print $filme->distribuidor->nome;
        

        //$filme->genero = new Genero(1);
        //$filme->store();

        TTransaction::close();
        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }





    }

}

?>