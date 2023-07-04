ALTER TABLE todos 
ADD is_complete INT NOT NULL DEFAULT '0' 
AFTER description;