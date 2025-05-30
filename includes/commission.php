<?php
require_once 'db_config.php';

class CommissionSystem {
    private $conn;
    private $commission_rates = [
        1 => 0.50, // Level 1: 50%
        2 => 0.30, // Level 2: 30%
        3 => 0.20, // Level 3: 20%
        4 => 0.10, // Level 4: 10%
        5 => 0.05  // Level 5: 5%
    ];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function calculateCommission($user_id, $amount) {
        $user_path = $this->getUserTreePath($user_id);
        $ancestors = $this->getAncestorsFromPath($user_path);
        
        foreach ($ancestors as $level => $ancestor_id) {
            if ($level > 5) break; // Only 5 levels
            
            $commission_amount = $amount * $this->commission_rates[$level];
            $this->recordCommission($ancestor_id, $user_id, $commission_amount, $level);
        }
    }

    private function getUserTreePath($user_id) {
        $stmt = $this->conn->prepare("SELECT tree_path FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['tree_path'];
    }

    private function getAncestorsFromPath($path) {
        $ancestors = array_filter(explode('/', trim($path, '/')));
        return array_reverse($ancestors); // Start from direct sponsor
    }

    private function recordCommission($sponsor_id, $user_id, $amount, $level) {
        $stmt = $this->conn->prepare("INSERT INTO commissions 
            (sponsor_id, user_id, amount, level, created_at) 
            VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iidi", $sponsor_id, $user_id, $amount, $level);
        $stmt->execute();
    }
}

// Usage example:
// $commissionSystem = new CommissionSystem($conn);
// $commissionSystem->calculateCommission($purchasing_user_id, $purchase_amount);
?>
