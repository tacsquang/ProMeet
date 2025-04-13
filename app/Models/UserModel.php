<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class UserModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->log = new LogService();
    }

    public function findByEmail($email) {
        $this->log->logInfo("Fetching user by email: $email");
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
}
