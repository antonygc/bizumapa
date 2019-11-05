CREATE TABLE custom_theme (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100));

CREATE TABLE custom_subject_matter (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100),
	custom_theme_id INT NOT NULL , 
	FOREIGN KEY(custom_theme_id) REFERENCES custom_theme(id));