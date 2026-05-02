<?php

/**
 * Product Class
 * Simple product management for school project
 */
class Product {
    private $jsonFile;
    private $products;

    public function __construct() {
        $this->jsonFile = 'products.json';
        $this->loadProducts();
    }

    private function loadProducts() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->products = json_decode($jsonContent, true) ?: [];
        } else {
            $this->products = [];
        }
    }

    private function saveProducts() {
        $jsonContent = json_encode($this->products, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get all products
    public function getAll() {
        return $this->products;
    }

    // Get one product by ID
    public function getById($id) {
        foreach ($this->products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        return null;
    }

    // Add new product
    public function add($name, $price, $category, $image, $description, $isBestSeller = false, $isFeatured = false) {
        // Get next ID
        $maxId = 0;
        foreach ($this->products as $product) {
            if ($product['id'] > $maxId) {
                $maxId = $product['id'];
            }
        }
        
        $newProduct = [
            'id' => $maxId + 1,
            'name' => $name,
            'price' => $price,
            'category' => $category,
            'image' => $image,
            'thumbnails' => [$image],
            'description' => $description,
            'isBestSeller' => $isBestSeller,
            'isFeatured' => $isFeatured
        ];

        $this->products[] = $newProduct;
        return $this->saveProducts();
    }

    // Update product
    public function update($id, $name, $price, $category, $image, $description, $isBestSeller = false, $isFeatured = false) {
        for ($i = 0; $i < count($this->products); $i++) {
            if ($this->products[$i]['id'] == $id) {
                $this->products[$i] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'category' => $category,
                    'image' => $image,
                    'thumbnails' => [$image],
                    'description' => $description,
                    'isBestSeller' => $isBestSeller,
                    'isFeatured' => $isFeatured
                ];
                return $this->saveProducts();
            }
        }
        return false;
    }

    // Delete product
    public function delete($id) {
        for ($i = 0; $i < count($this->products); $i++) {
            if ($this->products[$i]['id'] == $id) {
                array_splice($this->products, $i, 1);
                return $this->saveProducts();
            }
        }
        return false;
    }

    // Get best sellers
    public function getBestSellers() {
        $result = [];
        foreach ($this->products as $product) {
            if ($product['isBestSeller']) {
                $result[] = $product;
            }
        }
        return $result;
    }

    // Get featured products
    public function getFeatured() {
        $result = [];
        foreach ($this->products as $product) {
            if ($product['isFeatured']) {
                $result[] = $product;
            }
        }
        return $result;
    }

    // Search products
    public function search($query) {
        $result = [];
        $query = strtolower($query);
        foreach ($this->products as $product) {
            if (strpos(strtolower($product['name']), $query) !== false || 
                strpos(strtolower($product['description']), $query) !== false) {
                $result[] = $product;
            }
        }
        return $result;
    }
}
?>
