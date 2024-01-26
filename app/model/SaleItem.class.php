<?php

use Adianti\Database\TRecord;

class SaleItem extends TRecord
{
    const TABLENAME = 'sale_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('sale_id');
        parent::addAttribute('product_id');
        parent::addAttribute('quantidade');
        parent::addAttribute('sale_price');
        parent::addAttribute('discount');
    }

    public function set_product(Product $object)
    {
        $this->product = $object;
    }

    public function get_product()
    {
        if (empty($this->product)) {
            $this->product = new Product($this->product_id);
        }

        return $this->product;
    }

    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
    }

    public function get_cliente()
    {
        if (empty($this->cliente)) {
            $this->cliente = new Cliente($this->cliente_id);
        }

        return $this->cliente;
    }
}
