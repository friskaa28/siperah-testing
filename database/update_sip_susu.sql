-- SQL Update Script for SIP-SUSU (Idempotent Version)
-- Use this to manually update your 'siperah_db' to match the new schema

-- 1. Update 'users' table (Add PIN if not exists)
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'pin';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname
       AND TABLE_NAME = @tablename
       AND COLUMN_NAME = @columnname) > 0,
    'SELECT "Column pin already exists"',
    'ALTER TABLE users ADD COLUMN pin CHAR(6) NULL AFTER password'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Update 'peternak' table (Add Status Mitra if not exists)
SET @tablename = 'peternak';
SET @columnname = 'status_mitra';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname
       AND TABLE_NAME = @tablename
       AND COLUMN_NAME = @columnname) > 0,
    'SELECT "Column status_mitra already exists"',
    'ALTER TABLE peternak ADD COLUMN status_mitra ENUM(\'peternak\', \'sub_penampung\') DEFAULT \'peternak\' AFTER koperasi_id'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Create 'katalog_logistik' table
CREATE TABLE IF NOT EXISTS katalog_logistik (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    harga_satuan DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 4. Create 'kasbon' table
CREATE TABLE IF NOT EXISTS kasbon (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idpeternak BIGINT UNSIGNED NOT NULL,
    idlogistik BIGINT UNSIGNED NULL,
    nama_item VARCHAR(255) NOT NULL,
    qty DECIMAL(10,2) NOT NULL,
    harga_satuan DECIMAL(15,2) NOT NULL,
    total_rupiah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (tanggal),
    CONSTRAINT fk_kasbon_peternak FOREIGN KEY (idpeternak) REFERENCES peternak(idpeternak) ON DELETE CASCADE,
    CONSTRAINT fk_kasbon_logistik FOREIGN KEY (idlogistik) REFERENCES katalog_logistik(id) ON DELETE SET NULL
);

-- 5. Create 'harga_susu_history' table
CREATE TABLE IF NOT EXISTS harga_susu_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    harga DECIMAL(15,2) NOT NULL,
    tanggal_berlaku DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (tanggal_berlaku)
);

-- 6. Create 'pengumuman' table
CREATE TABLE IF NOT EXISTS pengumuman (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isi TEXT NOT NULL,
    id_admin BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_pengumuman_admin FOREIGN KEY (id_admin) REFERENCES users(iduser) ON DELETE CASCADE
);

