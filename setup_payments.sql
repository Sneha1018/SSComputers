-- Create payments table if it doesn't exist
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cod', 'upi') NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'awaiting_payment') NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add payment_method column to orders table if it doesn't exist
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method 
    ENUM('cod', 'upi') NOT NULL DEFAULT 'cod' AFTER total_amount;

-- Add payment_status column to orders table if it doesn't exist
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status 
    ENUM('pending', 'completed', 'failed', 'awaiting_payment') 
    NOT NULL DEFAULT 'pending' AFTER payment_method; 