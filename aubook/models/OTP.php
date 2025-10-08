<?php
// models/OTP.php

class OTP {
    private $conn;
    private $table = "otp_codes";

    public $phone;
    public $otp_code;
    public $expires_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo mã OTP
    public function generate() {
        // Tạo mã OTP 6 số
        $this->otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Thời gian hết hạn sau 5 phút
        $this->expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $query = "INSERT INTO " . $this->table . " 
                  SET phone=:phone, otp_code=:otp_code, expires_at=:expires_at";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":otp_code", $this->otp_code);
        $stmt->bindParam(":expires_at", $this->expires_at);

        if($stmt->execute()) {
            return $this->otp_code;
        }
        return false;
    }

    // Xác thực OTP
    public function verify($input_otp) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE phone = :phone 
                  AND otp_code = :otp_code 
                  AND expires_at > NOW() 
                  AND is_verified = 0
                  ORDER BY created_at DESC 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":otp_code", $input_otp);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            // Đánh dấu OTP đã được xác thực
            $update_query = "UPDATE " . $this->table . " 
                           SET is_verified = 1 
                           WHERE phone = :phone 
                           AND otp_code = :otp_code 
                           AND is_verified = 0";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":phone", $this->phone);
            $update_stmt->bindParam(":otp_code", $input_otp);
            $update_stmt->execute();
            
            return true;
        }
        return false;
    }

    // Gửi OTP qua SMS (demo - thực tế cần tích hợp API SMS)
    public function sendSMS($otp_code) {
        // Trong thực tế, tích hợp với nhà cung cấp SMS như Twilio, Nexmo, etc.
        // Đây là demo nên chỉ log ra
        error_log("OTP for {$this->phone}: {$otp_code}");
        
        // Trả về true để giả lập gửi thành công
        return true;
    }
}
?>