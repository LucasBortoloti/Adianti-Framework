<?php

use Adianti\Control\TPage;

class ObjetoDeleteView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            $filme = new Filme;
            $filme->delete(6);

            TTransaction::close();

        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>