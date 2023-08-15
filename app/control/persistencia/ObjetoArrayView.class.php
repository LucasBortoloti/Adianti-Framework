<?php

use Adianti\Control\TPage;

class ObjetoArrayView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');
            
            
            $filme = new Filme(5);
            var_dump($filme->toArray());

            $vetor = array();
            $vetor['titulo'] = 'Homem de ferro';
            $vetor['duracao'] = 130;
            $vetor['dt_lcto'] = '2008-09-21';
            $vetor['orcamento'] = '35000000';
            $vetor['distribuidor_id'] = '3';
            $vetor['genero_id'] = '1';
            
            $filme2 = new Filme;
            $filme2->fromArray($vetor);
            $filme2->store();

            new TMessage('info', 'Registro inserido com sucesso');

            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>