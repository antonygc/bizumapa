CREATE TABLE working_directory (
	wd_id INT PRIMARY KEY NOT NULL, 
	wd_content JSON NULL DEFAULT NULL,
	system_user_id INT NOT NULL , 
	FOREIGN KEY(system_user_id) REFERENCES system_user(id));


CREATE TABLE custom_theme (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100));