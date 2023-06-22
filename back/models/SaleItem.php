<?php

class SaleItem
{
    public $id;
    public $product_id;
    public $quantity;
    public $value;
    public $sale_id;
    public $tax_value;
     
    private $pdo;

    public function __construct()
    {
        $this->pdo = require 'config/database.php'        ;
    }

    public function save()
    {
        $query = "INSERT INTO public.sales_items (product_id, quantity, value, tax_value, sale_id) VALUES (:product_id, :quantity, :value, :tax_value, :sale_id)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':product_id', $this->product_id);
        $statement->bindValue(':quantity', $this->quantity);
        $statement->bindValue(':value', $this->value);
        $statement->bindValue(':tax_value', $this->tax_value);
        $statement->bindValue(':sale_id', $this->sale_id);
        $statement->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
        ];
    }
    
}
