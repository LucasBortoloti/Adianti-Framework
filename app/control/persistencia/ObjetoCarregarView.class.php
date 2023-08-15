<?php

use Adianti\Control\TPage;

class ObjetoCarregarView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            $filme = new Filme(6);
            print $filme->titulo . '<br>';
            print $filme->duracao . '<br>';
            
            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>