<?php

use Adianti\Control\TPage;

class ObjetoNovoView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');
            TTransaction::setLogger(new TLoggerTXT('/tmp/log.txt'));
            TTransaction::log('Inserindo novo filme...');

            $filme = new Filme;
            $filme->titulo = 'De volta para o futuro';
            $filme->duracao = 116;
            $filme->dt_lcto = '1985-07-03';
            $filme->orcamento = 19000000;
            $filme->distribuidora_id = 2;
            $filme->genero_id = 3;
            $filme->store();

            new TMessage('info', 'Registro inserido com sucesso');


            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }

    }

}

?>