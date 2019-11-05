CREATE TABLE custom_theme (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100));

CREATE TABLE custom_subject_matter (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100),
	theme_id INT NOT NULL, 
	FOREIGN KEY(theme_id) REFERENCES custom_theme(id));

CREATE TABLE custom_public_mind_map (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	content TEXT, # 65,535 chars
	theme_id INT NOT NULL , 
	subject_matter_id INT NOT NULL , 
	FOREIGN KEY(theme_id) REFERENCES custom_theme(id),
	FOREIGN KEY(subject_matter_id) REFERENCES custom_subject_matter(id));