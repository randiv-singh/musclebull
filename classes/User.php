<?php

/**
 * User Class
 * Simple user management for school project
 */
class User {
    private $jsonFile;
    private $users;

    public function __construct() {
        $this->jsonFile = '../config/users.json';
        $this->loadUsers();
    }

    private function loadUsers() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->users = json_decode($jsonContent, true) ?: [];
        } else {
            $this->users = [];
        }
    }

    private function saveUsers() {
        $jsonContent = json_encode($this->users, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get all users
    public function getAll() {
        return $this->users;
    }

    // Get one user by ID
    public function getById($id) {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    // Authenticate user
    public function authenticate($email, $password) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    // Add new user
    public function add($name, $email, $password, $role = 'customer') {
        // Check if email already exists
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return false;
            }
        }

        // Get next ID
        $maxId = 0;
        foreach ($this->users as $user) {
            if ($user['id'] > $maxId) {
                $maxId = $user['id'];
            }
        }
        
        $newUser = [
            'id' => $maxId + 1,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];

        $this->users[] = $newUser;
        return $this->saveUsers();
    }

    // Update user
    public function update($id, $name, $email, $role = null, $status = null) {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]['id'] == $id) {
                $this->users[$i]['name'] = $name;
                $this->users[$i]['email'] = $email;
                
                if ($role !== null) {
                    $this->users[$i]['role'] = $role;
                }
                
                if ($status !== null) {
                    $this->users[$i]['status'] = $status;
                }
                
                return $this->saveUsers();
            }
        }
        return false;
    }

    // Delete user
    public function delete($id) {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]['id'] == $id) {
                array_splice($this->users, $i, 1);
                return $this->saveUsers();
            }
        }
        return false;
    }

    // Update password
    public function updatePassword($id, $newPassword) {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]['id'] == $id) {
                $this->users[$i]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                return $this->saveUsers();
            }
        }
        return false;
    }

    // Get users by role
    public function getByRole($role) {
        $result = [];
        foreach ($this->users as $user) {
            if ($user['role'] === $role) {
                $result[] = $user;
            }
        }
        return $result;
    }
}
?>
