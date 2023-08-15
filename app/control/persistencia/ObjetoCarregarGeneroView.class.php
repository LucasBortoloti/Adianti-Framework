<?php

use Adianti\Control\TPage;

class ObjetoCarregarGeneroView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            $filme = new Filme(5);
            print $filme->titulo . '<br>';
            print $filme->duracao . '<br>';
            print $filme->nome_genero . '<br>';
            print $filme->genero->nome . '<br>';
            
            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>