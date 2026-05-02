<?php

/**
 * Category Class
 * Simple category management for school project
 */
class Category {
    private $jsonFile;
    private $categories;

    public function __construct() {
        $this->jsonFile = '../config/categories.json';
        $this->loadCategories();
    }

    private function loadCategories() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->categories = json_decode($jsonContent, true) ?: [];
        } else {
            $this->categories = [];
        }
    }

    private function saveCategories() {
        $jsonContent = json_encode($this->categories, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get all categories
    public function getAll() {
        return $this->categories;
    }

    // Get one category by ID
    public function getById($id) {
        foreach ($this->categories as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }
        return null;
    }

    // Get category by name
    public function getByName($name) {
        foreach ($this->categories as $category) {
            if ($category['name'] === $name) {
                return $category;
            }
        }
        return null;
    }

    // Add new category
    public function add($name, $description = '', $image = '') {
        // Check if name already exists
        foreach ($this->categories as $category) {
            if ($category['name'] === $name) {
                return false;
            }
        }

        // Get next ID
        $maxId = 0;
        foreach ($this->categories as $category) {
            if ($category['id'] > $maxId) {
                $maxId = $category['id'];
            }
        }
        
        $newCategory = [
            'id' => $maxId + 1,
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'product_count' => 0
        ];

        $this->categories[] = $newCategory;
        return $this->saveCategories();
    }

    // Update category
    public function update($id, $name, $description = '', $image = '', $status = null) {
        for ($i = 0; $i < count($this->categories); $i++) {
            if ($this->categories[$i]['id'] == $id) {
                $this->categories[$i]['name'] = $name;
                $this->categories[$i]['description'] = $description;
                $this->categories[$i]['image'] = $image;
                
                if ($status !== null) {
                    $this->categories[$i]['status'] = $status;
                }
                
                return $this->saveCategories();
            }
        }
        return false;
    }

    // Delete category
    public function delete($id) {
        for ($i = 0; $i < count($this->categories); $i++) {
            if ($this->categories[$i]['id'] == $id) {
                array_splice($this->categories, $i, 1);
                return $this->saveCategories();
            }
        }
        return false;
    }

    // Get active categories
    public function getActive() {
        $result = [];
        foreach ($this->categories as $category) {
            if ($category['status'] === 'active') {
                $result[] = $category;
            }
        }
        return $result;
    }

    // Update product count for a category
    public function updateProductCount($categoryId, $count) {
        for ($i = 0; $i < count($this->categories); $i++) {
            if ($this->categories[$i]['id'] == $categoryId) {
                $this->categories[$i]['product_count'] = $count;
                return $this->saveCategories();
            }
        }
        return false;
    }

    // Get categories with product counts
    public function getWithProductCounts($productClass) {
        $categories = $this->getAll();
        $products = $productClass->getAll();
        
        // Count products per category
        $productCounts = [];
        foreach ($products as $product) {
            $categoryName = $product['category'];
            if (!isset($productCounts[$categoryName])) {
                $productCounts[$categoryName] = 0;
            }
            $productCounts[$categoryName]++;
        }
        
        // Update categories with product counts
        foreach ($categories as &$category) {
            $category['product_count'] = $productCounts[$category['name']] ?? 0;
        }
        
        return $categories;
    }
}
?>
