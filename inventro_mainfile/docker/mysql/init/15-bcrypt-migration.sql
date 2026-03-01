-- =====================================================
-- 15 - Add password_bcrypt column for transparent migration from MD5
-- =====================================================
-- Users will be migrated transparently on next login:
-- 1. Login attempt → verify with bcrypt (password_bcrypt) first
-- 2. If no bcrypt hash, verify with legacy MD5 (password)
-- 3. If MD5 matches, generate bcrypt hash and save to password_bcrypt
-- 4. Over time all users migrate without downtime

ALTER TABLE `user`
ADD COLUMN `password_bcrypt` VARCHAR(255) NULL DEFAULT NULL
AFTER `password`;
