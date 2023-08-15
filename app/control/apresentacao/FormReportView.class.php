<?php

use Adianti\Control\TPage;

class FormReportView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Relatório de Filmes');

        $titulo = new TEntry('titulo');
        $genero_id = new TDBCombo('genero_id', 'filme', 'Genero', 'id', 'nome');

        $this->form->addQuickField('Titulo', $titulo, 200);
        $this->form->addQuickField('Genero', $genero_id, 200);

        $action = new TAction( array( $this, 'onGenerate'));
        $this->form->addQuickAction('Gerar', $action, 'ico_apply.png' );

        parent::add($this->form);

    }

    public function onGenerate(){
        
        try{

            TTransaction::open('filme');

            $data = $this->form->getData();

            $this->form->validate();
        
            $this-> form->setData( $data ); 

            $repository = new TRepository('Filme');
            $criteria = new TCriteria;

            if($data->titulo){

                $criteria->add(new TFilter('titulo', 'like', "%{$data->titulo}%"));
            }
            if($data->genero_id){
                $criteria->add(new TFilter('genero_id', '=', "{$data->genero_id}" ));
            }
        
            $filmes = $repository->load( $criteria );

            if ($filmes){

                $widths = array(50,  150, 70, 100, 100);
                $table = new TTableWriterPDF( $widths );

                $table->addStyle('tittle', 'Arial', '10', '', '#ffffff', '#407B49');
                $table->addStyle('datap', 'Arial', '10', '', '#000000', '#869FBB');
                $table->addStyle('datai', 'Arial', '10', '', '#000000', '#869FBB');

                $table->addRow();
                $table->addCell('Id', 'left', 'tittle');
                $table->addCell('Titulo', 'left', 'tittle');
                $table->addCell('Duracao', 'left', 'tittle');
                $table->addCell('DtLcto', 'left', 'tittle');
                $table->addCell('Orcamento', 'left', 'tittle');

                $color = FALSE;
                foreach ($filmes as $filme){

                    $style = $color ? 'datap' : 'datai';

                    $table->addRow();
                    $table->addCell( $filme->id, 'left', $style);
                    $table->addCell( $filme->titulo, 'left', $style);
                    $table->addCell( $filme->duracao, 'left', $style);
                    $table->addCell( $filme->dt_lcto, 'left', $style);
                    $table->addCell( $filme->orcamento, 'left', $style);

                    $color = !$color;
                }

                $table->save('app/output/relatorio.pdf');

                parent::openFile('app/output/relatorio.pdf');

            }
            else{

            }

            TTransaction::close();
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }


}
?>