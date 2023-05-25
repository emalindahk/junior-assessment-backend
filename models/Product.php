<?php

class Product {
    private $conn;
    private $table = 'products';

    public $id;
    public $sku;
    public $name;
    public $price;
    public $category_id;
    public $category_name;
    public $attribute_name;
    public $attribute_value;
    public $ids;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll() {
        $query = 'SELECT 
                p.id, 
                p.sku, 
                p.name, 
                p.price, 
                c.name AS category_name, 
                a.name AS attribute_name, 
                pa.value
                FROM ' . $this->table . ' p
                JOIN categories c ON p.category_id = c.id
                JOIN product_attributes pa ON pa.product_id = p.id
                JOIN attributes a ON pa.attribute_id = a.id
                ORDER BY p.id;';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                sku = :sku,
                name = :name,
                price = :price,
                category_id = :category_id';

        $stmt = $this->conn->prepare($query);

        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category_id', $this->category_id);

        if ($stmt->execute()) {
            $productId = $this->conn->lastInsertId();

            $query = 'INSERT INTO product_attributes
                SET
                    product_id = :product_id,
                    attribute_id = (
                        SELECT attribute_id
                        FROM category_attributes
                        WHERE id = :category_id
                    ),
                    value = :value';

            $stmt = $this->conn->prepare($query);

            $this->attribute_value = htmlspecialchars(strip_tags($this->attribute_value));

            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':value', $this->attribute_value);

            $stmt->execute();

            return true;
        }

        return false;
    }

    public function delete()
    {
        if (empty($this->ids)) {
            return false;
        }

        $placeholders = rtrim(str_repeat('?,', count($this->ids)), ',');

        $deleteAttributesQuery = "DELETE FROM product_attributes WHERE product_id IN ($placeholders)";
        $deleteProductsQuery = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare($deleteAttributesQuery);
            $stmt->execute($this->ids);

            $stmt = $this->conn->prepare($deleteProductsQuery);
            $stmt->execute($this->ids);

            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->conn->rollBack();
            return false;
        }
    }
}

?>
