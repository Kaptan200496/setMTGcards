// Выражение для создания таблицы mtg_cards
CREATE TABLE mtg_cards (
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	name TEXT, 
	manaCost VARCHAR(64), 
	text TEXT, 
	power VARCHAR(32), 
	toughness VARCHAR(32)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_types
CREATE TABLE mtg_types(
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name TEXT
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_subtypes
CREATE TABLE mtg_subtypes(
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name TEXT
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_supertypes
CREATE TABLE mtg_supertypes(
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name TEXT
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_cardTypes 
CREATE TABLE mtg_cardTypes (
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	card INTEGER(64) UNSIGNED,
	type INTEGER(64) UNSIGNED
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_cardSubtypes
CREATE TABLE mtg_cardSubtypes (
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	card INTEGER(64) UNSIGNED,
	subtype INTEGER(64) UNSIGNED
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// Выражение для создания таблицы mtg_cardSupertypes
CREATE TABLE mtg_cardSupertypes (
	id INTEGER(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	card INTEGER(64) UNSIGNED,
	supertype INTEGER(64) UNSIGNED
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
