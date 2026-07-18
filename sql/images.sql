USE office_equipment_store;

-- 1. Update the image for 'Ergo Mesh Chair'
UPDATE products 
SET image_url = 'Chair.jpg' 
WHERE name = 'Ergo Mesh Chair';

-- 2. Update the image for 'Executive Desk'
UPDATE products 
SET image_url = 'Desk.png' 
WHERE name = 'Executive Desk';

-- 3. Update the image for '3-Door Storage Cabinet'
UPDATE products 
SET image_url = 'Cabinet.jpg' 
WHERE name = '3-Door Storage Cabinet';

-- 4. Update the image for 'Compact Work Table'
UPDATE products 
SET image_url = 'Compact.jpg' 
WHERE name = 'Compact Work Table';