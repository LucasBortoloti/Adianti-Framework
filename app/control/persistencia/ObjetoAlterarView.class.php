<?php

use Adianti\Control\TPage;

class ObjetoAlterarView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            $filme = new Filme;
            $objeto = $filme->load(6);
            if($objeto){

                $objeto->titulo = 'Vingadores Ultimato ' . $objeto->titulo;
                $objeto->store();
            }

            TTransaction::close();

        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>