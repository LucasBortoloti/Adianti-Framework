<?php

use Adianti\Control\TPage;

class TemplateView extends TPage
{

    private $html;

    public function __construct()
    {

        parent::__construct();

        try {
            parent::include_css('app/resources/style.css');

            $this->html = new THtmlRenderer('app/resources/filme.html');

            TTransaction::open('filme');
            $filme = new Filme(5);

            $replaces = array();
            $replaces['id'] = $filme->id;
            $replaces['titulo'] = $filme->titulo;
            $replaces['dt_lcto'] = $filme->dt_lcto;
            $replaces['duracao'] = $filme->duracao;
            $replaces['orcamento'] = $filme->orcamento;
            $replaces['genero'] = $filme->genero->nome;
            $replaces['distribuidor'] = $filme->distribuidor->nome;

            $replace_criticas = array();

            foreach ($filme->getCriticas() as $critica) {

                $replace_criticas[] = array(
                    'dt_publicacao' => $critica->dt_publicacao,
                    'veiculo' => $critica->veiculo,
                    'conteudo' => $critica->conteudo
                );
            }

            $this->html->enableSection('main', $replaces);
            $this->html->enableSection('criticas', $replace_criticas, TRUE);
            parent::add($this->html);

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
