ALTER TABLE system_group ENGINE=InnoDB;
ALTER TABLE system_program ENGINE=InnoDB;
ALTER TABLE system_unit ENGINE=InnoDB;
ALTER TABLE system_preference ENGINE=InnoDB;
ALTER TABLE system_user ENGINE=InnoDB;
ALTER TABLE system_user_unit ENGINE=InnoDB;
ALTER TABLE system_user_group ENGINE=InnoDB;
ALTER TABLE system_group_program ENGINE=InnoDB;
ALTER TABLE system_user_program ENGINE=InnoDB;

CREATE TABLE custom_theme (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100) NOT NULL
    ) ENGINE=InnoDB;

CREATE TABLE custom_subject_matter (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	theme_id INT NOT NULL, 
	FOREIGN KEY(theme_id) REFERENCES custom_theme(id)
	) ENGINE=InnoDB;

CREATE TABLE custom_public_mind_map (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	content TEXT, # 65,535 chars
	theme_id INT NOT NULL , 
	subject_matter_id INT NOT NULL , 
	FOREIGN KEY(theme_id) REFERENCES custom_theme(id),
	FOREIGN KEY(subject_matter_id) REFERENCES custom_subject_matter(id)
    ) ENGINE=InnoDB;

CREATE TABLE custom_folder (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	parent_id INT,
	user_id INT NOT NULL,
	FOREIGN KEY(user_id) REFERENCES system_user(id),
	FOREIGN KEY(parent_id) REFERENCES custom_folder(id)
    ) ENGINE=InnoDB;

-- UPDATE custom_folder SET parent_id = NULL WHERE id = 1;
-- ALTER TABLE custom_folder MODIFY parent_id INT;

CREATE TABLE custom_private_mind_map (
	id INT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	content TEXT, # 65,535 chars
	user_id INT NOT NULL, 
	folder_id INT NOT NULL,
	theme_id INT, 
	subject_matter_id INT,
	FOREIGN KEY(user_id) REFERENCES system_user(id),
	FOREIGN KEY(folder_id) REFERENCES custom_folder(id),	 
	FOREIGN KEY(theme_id) REFERENCES custom_theme(id),
	FOREIGN KEY(subject_matter_id) REFERENCES custom_subject_matter(id)
    ) ENGINE=InnoDB;


INSERT INTO custom_folder VALUES(1, 'Minhas Pastas', NULL, 1);


-- DROP TABLE custom_theme;
-- DROP TABLE custom_subject_matter;
-- DROP TABLE custom_public_mind_map;
-- DROP TABLE custom_folder;
-- DROP TABLE custom_private_mind_map;

CREATE OR REPLACE VIEW view_folder_contents AS
SELECT f.id as item_id, 
	   f.name as item_name, 
	   f.user_id as user_id, 
	   'folder' as item_type, 
	   f.parent_id as parent_id
FROM custom_folder as f
UNION ALL
SELECT pmm.id as item_id, 
	   pmm.name as item_name, 
	   pmm.user_id as user_id, 
	   'mindmap' as item_type, 
	   pmm.folder_id as parent_id
FROM custom_private_mind_map pmm;






