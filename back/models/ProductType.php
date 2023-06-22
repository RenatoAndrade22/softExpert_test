<?php

class ProductType
{
    public $id;
    public $name;
    public $tax_percentage;
    public $status;
    private $pdo;

    public function __construct()
    {
        $this->pdo = require 'config/database.php'        ;
    }

    public function getAll()
    {
 
        $query = '
            SELECT prodTyp.*, COUNT(prod.id) AS product_count 
            FROM public.products_types prodTyp 
                LEFT JOIN public.products prod
                    on prod.products_type_id = prodTyp.id
            GROUP BY prodTyp.id
            ORDER BY prodTyp.id DESC
        ';
        $statement = $this->pdo->query($query);
        $productTypes = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $productTypes;
    
    }

    public function getAllActive()
    {
 
        $query = '
            SELECT id, name FROM public.products_types WHERE status IS TRUE
        ';
        $statement = $this->pdo->query($query);
        $productTypes = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $productTypes;
    
    }
    
    public function getById($id)
    {
  
        $query = "SELECT * FROM public.products_types WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $productType = $statement->fetchObject();

        return $productType;
    }
    
    public function save()
    {
        $query = "INSERT INTO public.products_types(name, tax_percentage, status) VALUES (:name, :tax_percentage, :status);";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':tax_percentage', $this->tax_percentage);
        $statement->bindValue(':status', (int)$this->status);
        $statement->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $this->name,
            'tax_percentage' => $this->tax_percentage,
            'product_count' => 0,
            'status' => $this->status
        ];
    }
    
    public function update()
    {

        $query = "UPDATE public.products_types SET name=:name, tax_percentage=:tax_percentage, status=:status WHERE id=:id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $this->id);
        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':tax_percentage', $this->tax_percentage);
        $statement->bindValue(':status', (int)$this->status);
        $statement->execute();

        return [
            'name' => $this->name,
            'tax_percentage' => $this->tax_percentage,
            'status' => $this->status,
        ];
    }
    
    public function delete($id)
    {
        $query = "DELETE FROM public.products_types WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
