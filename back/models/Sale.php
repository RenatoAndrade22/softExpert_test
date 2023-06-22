<?php
require_once 'helpers/MonetaryConverter.php';

class Sale
{
    public $id;
    public $observation;
    private $pdo;

    public function __construct()
    {
        $this->pdo = require 'config/database.php'        ;
    }

    public function getAll()
    {
 
        $query = '
            SELECT 
                sl.id, sl_items.product_id, sl_items.quantity, sl_items.value, sl_items.tax_value, pr.name, pr.price
            FROM public.sales sl
                JOIN sales_items sl_items
                    ON sl_items.sale_id = sl.id
                JOIN products pr
                    ON pr.id = sl_items.product_id
            ORDER BY 
                sl.id DESC;
        ';

        $statement = $this->pdo->query($query);
        $sales = $statement->fetchAll(PDO::FETCH_ASSOC);

        $arr_sales = array();

        foreach($sales as $sale){

            $total_current = isset($arr_sales[$sale['id']]['total']) ? $arr_sales[$sale['id']]['total'] : 0;
            $tax_current = isset($arr_sales[$sale['id']]['tax_value']) ? $arr_sales[$sale['id']]['tax_value'] : 0;

            $arr_sales[$sale['id']]['id'] = $sale['id'];
            $arr_sales[$sale['id']]['total'] =  $total_current + $sale['value'];
            $arr_sales[$sale['id']]['tax_value'] =  $tax_current + $sale['tax_value'];
            $arr_sales[$sale['id']]['products'][] = $sale;

        }

        foreach($arr_sales as &$sale){
            $sale['total'] = MonetaryConverter::centsToDolar($sale['total']);
            $sale['tax_value'] = MonetaryConverter::centsToDolar($sale['tax_value']);
            $sale['count_products'] = count($sale['products']);

            foreach($sale['products'] as &$product){
                $product['value'] =  MonetaryConverter::centsToDolar($product['value']);
                $product['tax_value'] =  MonetaryConverter::centsToDolar($product['tax_value']);
                $product['price'] =  MonetaryConverter::centsToDolar($product['price']);
            }
        }
        
        return $arr_sales;
    
    }

    public function save()
    {
        $query = "INSERT INTO public.sales (observation) VALUES (:observation)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':observation', $this->observation);
        $statement->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
        ];
    }

    public function delete($id)
    {
        $this->deleteCascateSaleItems($id);

        $query = "DELETE FROM public.sales WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function deleteCascateSaleItems($id)
    {
        $query = "DELETE FROM public.sales_items WHERE sale_id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
    

    
}
