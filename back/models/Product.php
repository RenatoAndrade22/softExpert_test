<?php

require_once 'helpers/MonetaryConverter.php';

class Product
{
    public $id;
    public $name;
    public $price;
    public $products_type_id;
    private $pdo;

    public function __construct()
    {
        $this->pdo = require 'config/database.php'        ;
    }

    public function getAll()
    {
 
        $query = '
            SELECT 
                products.*,
                COALESCE(SUM(sales_items.quantity), 0) AS total_sales
            FROM 
                public.products
            LEFT JOIN 
                public.sales_items ON products.id = sales_items.product_id
            GROUP BY 
                products.id
            ORDER BY 
                products.id DESC;
        ';
        $statement = $this->pdo->query($query);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['price_formatted'] = MonetaryConverter::centsToDolar($product['price']);
        }

        return $products;
    
    }

    public function getAllActive()
    {
        $query = '
            SELECT 
                products.*, products_types.name as name_product_type, products_types.tax_percentage 
            FROM public.products
                JOIN public.products_types 
                ON products.products_type_id = products_types.id
            WHERE products.status IS TRUE
        ';
        $statement = $this->pdo->query($query);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['price_formatted'] = MonetaryConverter::centsToDolar($product['price']);
        }

        return $products;
    
    }
    
    public function getById($id)
    {
  
        $query = "
            SELECT 
                prod.*, 
                prodTyps.name as type_name,
                prodTyps.tax_percentage 
            FROM public.products prod
                JOIN products_types prodTyps
                    on prod.products_type_id = prodTyps.id
            WHERE prod.id = :id"
        ;
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $product = $statement->fetchObject();

        return $product;
    }
    
    public function save()
    {
        $query = "INSERT INTO public.products (name, price, products_type_id) VALUES (:name, :price, :products_type_id)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':price', $this->price);
        $statement->bindValue(':products_type_id', $this->products_type_id);
        $statement->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $this->name,
            'price' => $this->price,
            'price_formatted' => MonetaryConverter::centsToDolar($this->price),
            'products_type_id' => $this->products_type_id,
            'total_sales' => 0
        ];
    }
    
    public function update()
    {

        $query = "UPDATE public.products SET name=:name, price=:price, products_type_id=:products_type_id WHERE id=:id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $this->id);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':price', $this->price);
        $statement->bindValue(':products_type_id', $this->products_type_id);
        $statement->execute();

        return [
            'name' => $this->name,
            'price' => $this->price,
            'price_formatted' => MonetaryConverter::centsToDolar($this->price),
            'products_type_id' => $this->products_type_id,
        ];
    }
    
    public function delete($id)
    {
        $query = "DELETE FROM public.products WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
