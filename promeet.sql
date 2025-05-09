DROP DATABASE IF EXISTS pro_meet;
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS pro_meet;
USE pro_meet;
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE,
    birth_date DATE,
    sex BOOLEAN,     -- 0: men, 1: women
    address VARCHAR(255),
    avatar_url VARCHAR(255),
    role TINYINT NOT NULL DEFAULT 0, -- 0: user, 1: admin
    is_ban BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS rooms (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    html_description TEXT NOT NULL,
    price FLOAT UNSIGNED NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    location_name VARCHAR(255) NOT NULL,
    latitude DOUBLE NOT NULL,
    longitude DOUBLE NOT NULL,
    category TINYINT NOT NULL DEFAULT 0, -- 0: Basic, 1: Standard, 2: Premium
    average_rating FLOAT UNSIGNED DEFAULT 0,
    review_count INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id CHAR(36) PRIMARY KEY,
    room_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    booking_code CHAR(14),
    total_price INT UNSIGNED NOT NULL,
    contact_email VARCHAR(191),
    contact_name VARCHAR(100),
    contact_phone VARCHAR(20),
    payment_method TINYINT NOT NULL DEFAULT 0, -- 0: bank, 1:  momo 
    status TINYINT NOT NULL DEFAULT 0, -- 0: pending, 1: paid, 2: confirmed, 3: completed, 4: canceled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS booking_slots (
    id CHAR(36) PRIMARY KEY,
    booking_id CHAR(36) NOT NULL,
    booking_date DATE NOT NULL,
    time_slot TIME NOT NULL
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS booking_status_history (
    id CHAR(36) PRIMARY KEY,
    booking_id CHAR(36),
    status TINYINT NOT NULL DEFAULT 0, -- 0: pending, 1: paid, 2: confirmed, 3: completed, 4: canceled 
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    note TEXT,
    label VARCHAR(255)
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id CHAR(36) PRIMARY KEY,
    room_id CHAR(36),
    user_id CHAR(36),
    booking_id CHAR(36),
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS images (
    id CHAR(36) PRIMARY KEY,
    room_id CHAR(36),
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS remember_tokens (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    remember_token CHAR(64) NOT NULL,
    expiry_time INT NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS room_stats (
    room_id CHAR(36) PRIMARY KEY,
    view_count INT UNSIGNED DEFAULT 0,
    favorite_count INT UNSIGNED DEFAULT 0,
    booking_count INT UNSIGNED DEFAULT 0,
    total_hours INT UNSIGNED DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
-- FOREIGN KEYS – thêm sau khi đã tạo bảng
-- --------------------------------------------------------

-- bookings liên kết với users và rooms
ALTER TABLE bookings
    ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE;

-- booking_status_history liên kết với bookings
ALTER TABLE booking_status_history
    ADD CONSTRAINT fk_booking_status_history_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;

-- reviews liên kết với rooms, users, bookings
ALTER TABLE reviews
    ADD CONSTRAINT fk_reviews_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL;

-- images liên kết với rooms
ALTER TABLE images
    ADD CONSTRAINT fk_images_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE;

-- remember_tokens liên kết với users
ALTER TABLE remember_tokens
    ADD CONSTRAINT fk_remember_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- booking_slots liên kết với bookings
ALTER TABLE booking_slots
    ADD CONSTRAINT fk_booking_slots_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;

ALTER TABLE room_stats
    ADD CONSTRAINT fk_room_id FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE;

-- --------------------------------------------------------
-- TRIGGER –
-- --------------------------------------------------------

DROP TRIGGER IF EXISTS trg_after_insert_review;
DELIMITER $$

CREATE TRIGGER trg_after_insert_review
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE rooms
    SET 
        review_count = (
            SELECT COUNT(*) FROM reviews WHERE room_id = NEW.room_id
        ),
        average_rating = (
            SELECT AVG(rating) FROM reviews WHERE room_id = NEW.room_id
        )
    WHERE id = NEW.room_id;
END$$

DELIMITER ;
-- --------------------------------------------------------
DROP TRIGGER IF EXISTS trg_after_update_booking_status;
DELIMITER $$

CREATE TRIGGER trg_after_update_booking_status
AFTER UPDATE ON bookings
FOR EACH ROW
BEGIN
    DECLARE slot_count INT DEFAULT 0;

    IF OLD.status <> 3 AND NEW.status = 3 THEN
        -- Tính số slot (mỗi slot 30 phút)
        SELECT COUNT(*) INTO slot_count
        FROM booking_slots
        WHERE booking_id = NEW.id;

        -- Cập nhật room_stats
        INSERT INTO room_stats (room_id, booking_count, total_hours, updated_at)
        VALUES (NEW.room_id, 1, slot_count * 0.5, NOW())
        ON DUPLICATE KEY UPDATE 
            booking_count = booking_count + 1,
            total_hours = total_hours + (slot_count * 0.5),
            updated_at = NOW();
    END IF;
END$$

DELIMITER ;
-- --------------------------------------------------------

-- Admin / Pass: 123456
-- User 1: Bob / Pass: 123456
-- User 2: Alice / Pass: 123456
INSERT INTO users (id, name, email, password_hash, phone, birth_date, sex, address, role)
VALUES
(UUID(), 'Admin User', 'admin@promeet.com', '$2a$12$V6ynkz36/c19MNtMaXD/oeBiMicWpe02GMKOkqO/hILypZajQG07W', '0123456789', '1990-01-01', 0, '123 Admin St', 1),
(UUID(), 'Bob Smith', 'bob@example.com', '$2a$12$V6ynkz36/c19MNtMaXD/oeBiMicWpe02GMKOkqO/hILypZajQG07W', '0987654321', '1992-05-15', 0 , '456 User Rd', 0),
(UUID(), 'Alice Johnson', 'alice@example.com', '$2a$12$V6ynkz36/c19MNtMaXD/oeBiMicWpe02GMKOkqO/hILypZajQG07W', '0999888777', '1995-08-20', 1, '789 User Ln', 0);


