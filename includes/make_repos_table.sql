CREATE TABLE repositories (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	repo_id VARCHAR(63),
	name VARCHAR(255),
	url VARCHAR(1024),
	created DATETIME,
	lastPushed DATETIME,
	description TEXT,
	stars INT UNSIGNED
)