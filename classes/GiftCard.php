<?php

/**
 * GiftCard Class
 * Simple gift card management for school project
 */
class GiftCard {
    private $jsonFile;
    private $giftCards;

    public function __construct() {
        $this->jsonFile = '../config/gift_cards.json';
        $this->loadGiftCards();
    }

    private function loadGiftCards() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->giftCards = json_decode($jsonContent, true) ?: [];
        } else {
            $this->giftCards = [];
        }
    }

    private function saveGiftCards() {
        $jsonContent = json_encode($this->giftCards, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get all gift cards
    public function getAll() {
        return $this->giftCards;
    }

    // Get one gift card by ID
    public function getById($id) {
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['id'] == $id) {
                return $giftCard;
            }
        }
        return null;
    }

    // Get gift card by code
    public function getByCode($code) {
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['code'] === $code) {
                return $giftCard;
            }
        }
        return null;
    }

    // Add new gift card
    public function add($code, $amount, $recipientEmail, $message = '', $expiryDate = null) {
        // Check if code already exists
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['code'] === $code) {
                return false;
            }
        }

        // Get next ID
        $maxId = 0;
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['id'] > $maxId) {
                $maxId = $giftCard['id'];
            }
        }
        
        // Set expiry date to 1 year from now if not provided
        if ($expiryDate === null) {
            $expiryDate = date('Y-m-d', strtotime('+1 year'));
        }
        
        $newGiftCard = [
            'id' => $maxId + 1,
            'code' => $code,
            'amount' => $amount,
            'balance' => $amount,
            'recipient_email' => $recipientEmail,
            'message' => $message,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'expiry_date' => $expiryDate,
            'used_at' => null
        ];

        $this->giftCards[] = $newGiftCard;
        return $this->saveGiftCards();
    }

    // Update gift card
    public function update($id, $code, $amount, $recipientEmail, $message = '', $expiryDate = null, $status = null) {
        for ($i = 0; $i < count($this->giftCards); $i++) {
            if ($this->giftCards[$i]['id'] == $id) {
                $this->giftCards[$i]['code'] = $code;
                $this->giftCards[$i]['amount'] = $amount;
                $this->giftCards[$i]['recipient_email'] = $recipientEmail;
                $this->giftCards[$i]['message'] = $message;
                
                if ($expiryDate !== null) {
                    $this->giftCards[$i]['expiry_date'] = $expiryDate;
                }
                
                if ($status !== null) {
                    $this->giftCards[$i]['status'] = $status;
                }
                
                return $this->saveGiftCards();
            }
        }
        return false;
    }

    // Delete gift card
    public function delete($id) {
        for ($i = 0; $i < count($this->giftCards); $i++) {
            if ($this->giftCards[$i]['id'] == $id) {
                array_splice($this->giftCards, $i, 1);
                return $this->saveGiftCards();
            }
        }
        return false;
    }

    // Use gift card (reduce balance)
    public function use($code, $amount) {
        for ($i = 0; $i < count($this->giftCards); $i++) {
            if ($this->giftCards[$i]['code'] === $code) {
                if ($this->giftCards[$i]['status'] === 'active' && $this->giftCards[$i]['balance'] >= $amount) {
                    $this->giftCards[$i]['balance'] -= $amount;
                    
                    if ($this->giftCards[$i]['balance'] <= 0) {
                        $this->giftCards[$i]['status'] = 'used';
                        $this->giftCards[$i]['used_at'] = date('Y-m-d H:i:s');
                    }
                    
                    return $this->saveGiftCards();
                }
                return false;
            }
        }
        return false;
    }

    // Get active gift cards
    public function getActive() {
        $result = [];
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['status'] === 'active') {
                $result[] = $giftCard;
            }
        }
        return $result;
    }

    // Get expired gift cards
    public function getExpired() {
        $result = [];
        $today = date('Y-m-d');
        foreach ($this->giftCards as $giftCard) {
            if ($giftCard['expiry_date'] < $today && $giftCard['status'] === 'active') {
                $result[] = $giftCard;
            }
        }
        return $result;
    }

    // Generate unique gift card code
    public function generateCode() {
        do {
            $code = 'GC' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while ($this->getByCode($code) !== null);
        
        return $code;
    }
}
?>
