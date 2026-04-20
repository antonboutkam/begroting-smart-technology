-- Seed op basis van docs/boodschappen/*.xlsx
-- Geimporteerd op idempotente wijze: bestaande categorieen/producten blijven staan, dubbele seeds worden overgeslagen.
START TRANSACTION;

CREATE TEMPORARY TABLE temp_seed_categories (
    name VARCHAR(190) NOT NULL,
    parent_name VARCHAR(190) NULL,
    sort_order INT NOT NULL DEFAULT 0
);

INSERT INTO temp_seed_categories (name, parent_name, sort_order) VALUES
    ('3D printen', NULL, 10),
    ('Electronica', NULL, 20),
    ('Testen / experimenteren', 'Electronica', 10),
    ('Gereedschap', NULL, 30),
    ('Bevestigen', 'Gereedschap', 10),
    ('Buigen / knippen / afwerken', 'Gereedschap', 20),
    ('Meten', 'Gereedschap', 30),
    ('Opberg materialen', NULL, 40),
    ('Verbruiksartikelen', NULL, 50),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 10),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 20),
    ('M4 - constructie', 'Verbruiksartikelen', 30),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 40),
    ('M2.5 - elektronica', 'Verbruiksartikelen', 50),
    ('Extra''s', 'Verbruiksartikelen', 60);

INSERT INTO categories (parent_id, name, sort_order)
SELECT NULL, seed.name, seed.sort_order
FROM temp_seed_categories AS seed
WHERE seed.parent_name IS NULL
  AND NOT EXISTS (
      SELECT 1
      FROM categories AS existing
      WHERE existing.parent_id IS NULL
        AND existing.name = seed.name
  );

INSERT INTO categories (parent_id, name, sort_order)
SELECT parent.id, seed.name, seed.sort_order
FROM temp_seed_categories AS seed
JOIN categories AS parent
  ON parent.name = seed.parent_name
 AND parent.parent_id IS NULL
WHERE seed.parent_name IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM categories AS existing
      WHERE existing.parent_id = parent.id
        AND existing.name = seed.name
  );

CREATE TEMPORARY TABLE temp_seed_products (
    category_name VARCHAR(190) NOT NULL,
    parent_category_name VARCHAR(190) NULL,
    name VARCHAR(190) NOT NULL,
    description TEXT NULL,
    goal TEXT NULL,
    priority TINYINT UNSIGNED NOT NULL DEFAULT 0,
    is_asset TINYINT(1) NOT NULL DEFAULT 0,
    quantity_per_student DECIMAL(12,4) NOT NULL DEFAULT 1.0000
);

INSERT INTO temp_seed_products (
    category_name,
    parent_category_name,
    name,
    description,
    goal,
    priority,
    is_asset,
    quantity_per_student
) VALUES
    ('3D printen', NULL, 'PETG Filament zwart', 'Gewenste hoeveelheid: 15 kg. Uitgangspunt: 400-700 gram PETG per student.', 'Onderdelen printen, oefenen', 8, 0, 1.0),
    ('3D printen', NULL, 'PETG Filament wit / grijs', 'Gewenste hoeveelheid: 7 kg. Uitgangspunt: 400-700 gram PETG per student.', 'Onderdelen printen, oefenen', 8, 0, 1.0),
    ('3D printen', NULL, 'PETG Filament kleurmix', 'Gewenste hoeveelheid: 7 kg in blauw, rood, groen en transparant. Uitgangspunt: 400-700 gram PETG per student.', 'Onderdelen printen, oefenen', 8, 0, 1.0),
    ('3D printen', NULL, '3DLac - Hechtspray (400ml)', 'Gewenste hoeveelheid: 5 bussen.', 'Zorgen dat filament niet gaat schuiven', 6, 0, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Dupont kabels male/female', NULL, NULL, 6, 1, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Dupont kabels male/male', NULL, NULL, 6, 1, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Dupont kabels female/female', NULL, NULL, 6, 1, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Breadboards', NULL, NULL, 7, 1, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Krimpkous', NULL, NULL, 5, 0, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Tie-wraps', NULL, NULL, 5, 0, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Losse draad (solid + stranded)', NULL, NULL, 6, 0, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Buck converters', NULL, 'Voltage verlagen', 6, 0, 1.0),
    ('Testen / experimenteren', 'Electronica', 'Boost converters', NULL, 'Voltage verhogen', 6, 0, 1.0),
    ('Bevestigen', 'Gereedschap', 'Schroevendraaiers', 'Uitgangspunt: per groep een set.', NULL, 8, 1, 1.0),
    ('Bevestigen', 'Gereedschap', 'Imbus sleutel setjes', 'Uitgangspunt: per groep een set.', NULL, 7, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Zijsnijtangen', 'Uitgangspunt: per groep een set.', NULL, 7, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Punttangen', 'Uitgangspunt: per groep een set.', NULL, 7, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Combinatietang', 'Uitgangspunt: per groep een set.', NULL, 7, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Striptang', 'Uitgangspunt: per groep een set.', 'Draden strippen', 8, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Krimptang', 'Uitgangspunt: per groep een set.', 'Aansluiten connectoren', 8, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Pincetten', 'Uitgangspunt: per groep een set.', 'Kleine elektronica', 6, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Flush cutters (voor 3D prints)', 'Uitgangspunt: per groep een set.', 'Afwerking 3D prints', 6, 1, 1.0),
    ('Buigen / knippen / afwerken', 'Gereedschap', 'Heatgun / verf fohn', 'Uitgangspunt: per groep een set.', 'Afwerking 3D prints', 5, 1, 1.0),
    ('Meten', 'Gereedschap', 'Multimeters', 'Uitgangspunt: per groep een set.', 'Aansluiten electronica', 8, 1, 1.0),
    ('Meten', 'Gereedschap', 'Schuifmaten', 'Uitgangspunt: per groep een set.', 'Bij technisch tekenen is het belangrijk dat studenten heel precieze maten hebben.', 7, 1, 1.0),
    ('Opberg materialen', NULL, 'Gereedschapswagen', 'Gewenste hoeveelheid: 2. Voorbeeldlink: https://www.amazon.nl/dp/B07DC9ZBZD/ref=asc_df_B07DC9ZBZD?ie=UTF8&condition=new&tag=beslistmpnl-21&creative=380345&creativeASIN=B07DC9ZBZD&linkCode=asm&ascsubtag=651560&th=1', NULL, 2, 1, 1.0),
    ('Opberg materialen', NULL, 'Bakkenstrippenkast 340 bakken', 'Gewenste hoeveelheid: 1. Productnaam afgeleid uit de voorbeeldlink: https://povag.nl/producten/bakkenstrippenkast-2000x1000x450-mm-340-bakken-gmp-402-01?kies-kleur-kast=getiaanblauw-ral-5010-lichtgrijs-ral-7035&utm_medium=cpc&utm_source=google&utm_term=&utm_campaign=Pmax%20%7C%20Labelizer%20-%20heroes%20en%20sidekicks&hsa_acc=6013608846&hsa_cam=21475979458&hsa_grp=&hsa_ad=&hsa_src=x&hsa_tgt=&hsa_kw=&hsa_mt=&hsa_net=adwords&hsa_ver=3&gad_source=1&gad_campaignid=21465390138&gbraid=0AAAAACclZI4r_e4b34UIWbIvz4dzgubks&gclid=CjwKCAjwspPOBhB9EiwATFbi5MDTE5c2yafcogwPANbWPPc1rwXeMvf18IeNUfUdZJVy9Y9Ga-m4rBoCAHIQAvD_BwE', NULL, 2, 1, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Pvc buis 16 mm', 'Gewenste hoeveelheid: 30-50 meter.', NULL, 5, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Pvc buis 20 mm', 'Gewenste hoeveelheid: 30-50 meter.', NULL, 5, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Pvc buis 25 mm', 'Gewenste hoeveelheid: 20-30 meter.', NULL, 5, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Popnagels 3.2 mm', 'Gewenste hoeveelheid: 400 stuks. Benadert maat M3.', NULL, 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Popnagels 4.0 mm', 'Gewenste hoeveelheid: 600 stuks. Benadert maat M4.', NULL, 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Popnagels 4.8 mm', 'Gewenste hoeveelheid: 300 stuks. Benadert maat M5.', NULL, 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Tie-wraps (kabelbinders)', 'Gewenste hoeveelheid: heel veel.', 'Kabel bomen, snelle bevestigingen', 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Heat-set inserts M3', 'Gewenste hoeveelheid: 500. Specificatie uit bronbestand: M3, lengtes 6 / 10 / 15 mm.', NULL, 7, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Zelftappende schroeven', 'Gewenste hoeveelheid: 500.', 'Voor plastic behuizingen', 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Blindklinkmoeren (rivnuts) variant 1', 'Gewenste hoeveelheid: 150.', 'Schroefdraad in dun materiaal (plaat, profielen)', 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Blindklinkmoeren (rivnuts) variant 2', 'Gewenste hoeveelheid: 200.', 'Schroefdraad in dun materiaal (plaat, profielen)', 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Blindklinkmoeren (rivnuts) variant 3', 'Gewenste hoeveelheid: 150.', 'Schroefdraad in dun materiaal (plaat, profielen)', 6, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Klittenband', 'Gewenste hoeveelheid: 10 meter.', 'Batterijen, verwisselbare modules', 5, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Draadstangen M4 x 300 mm', 'Gewenste hoeveelheid: 50.', NULL, 5, 0, 1.0),
    ('Bevestigingsmateriaal', 'Verbruiksartikelen', 'Draadstangen M3 x 300 mm', 'Gewenste hoeveelheid: 50.', NULL, 5, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Bouten M3 x 6 mm (inbus)', 'Gewenste hoeveelheid: 500.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Bouten M3 x 8 mm (inbus)', 'Gewenste hoeveelheid: 500.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Bouten M3 x 10 mm (inbus)', 'Gewenste hoeveelheid: 800.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Bouten M3 x 12 mm (inbus)', 'Gewenste hoeveelheid: 800.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Bouten M3 x 16 mm (inbus)', 'Gewenste hoeveelheid: 500.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Moeren M3 standaard', 'Gewenste hoeveelheid: 2500.', NULL, 9, 0, 1.0),
    ('M3 - hoofdvoorraad', 'Verbruiksartikelen', 'Sluitringen M3', 'Gewenste hoeveelheid: 2500.', NULL, 8, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Bouten M4 x 10 mm (inbus)', 'Gewenste hoeveelheid: 300.', NULL, 7, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Bouten M4 x 12 mm (inbus)', 'Gewenste hoeveelheid: 300.', NULL, 7, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Bouten M4 x 16 mm (inbus)', 'Gewenste hoeveelheid: 400.', NULL, 7, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Bouten M4 x 20 mm (inbus)', 'Gewenste hoeveelheid: 300.', NULL, 7, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Moeren M4 standaard', 'Gewenste hoeveelheid: 1200.', NULL, 7, 0, 1.0),
    ('M4 - constructie', 'Verbruiksartikelen', 'Sluitringen M4', 'Gewenste hoeveelheid: 1200.', NULL, 6, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Bouten M5 x 12 mm (inbus)', 'Gewenste hoeveelheid: 150.', NULL, 5, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Bouten M5 x 16 mm (inbus)', 'Gewenste hoeveelheid: 150.', NULL, 5, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Bouten M5 x 20 mm (inbus)', 'Gewenste hoeveelheid: 200.', NULL, 5, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Bouten M5 x 25 mm (inbus)', 'Gewenste hoeveelheid: 150.', NULL, 5, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Moeren M5 standaard', 'Gewenste hoeveelheid: 700.', NULL, 5, 0, 1.0),
    ('M5 - grotere constructies', 'Verbruiksartikelen', 'Sluitringen M5', 'Gewenste hoeveelheid: 700.', NULL, 5, 0, 1.0),
    ('M2.5 - elektronica', 'Verbruiksartikelen', 'Bouten M2.5 x 6 mm', 'Gewenste hoeveelheid: 200.', NULL, 6, 0, 1.0),
    ('M2.5 - elektronica', 'Verbruiksartikelen', 'Bouten M2.5 x 8 mm', 'Gewenste hoeveelheid: 200.', NULL, 6, 0, 1.0),
    ('M2.5 - elektronica', 'Verbruiksartikelen', 'Moeren M2.5', 'Gewenste hoeveelheid: 500.', NULL, 6, 0, 1.0),
    ('M2.5 - elektronica', 'Verbruiksartikelen', 'Sluitringen M2.5', 'Gewenste hoeveelheid: 500.', NULL, 6, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Nyloc moeren M3', 'Gewenste hoeveelheid: 300.', NULL, 4, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Nyloc moeren M4', 'Gewenste hoeveelheid: 200.', NULL, 4, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Afstandsbusjes M3', 'Gewenste hoeveelheid: 200. Diverse lengtes.', NULL, 5, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Heat-set inserts M3 extra', 'Gewenste hoeveelheid: 300. Voor 3D prints.', NULL, 5, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Vleugelmoeren M3', 'Gewenste hoeveelheid: 100.', NULL, 3, 0, 1.0),
    ('Extra''s', 'Verbruiksartikelen', 'Assortimentsdozen', 'Gewenste hoeveelheid: 10-15. Gesorteerd per maat.', NULL, 3, 1, 1.0);

INSERT INTO products (
    category_id,
    name,
    description,
    goal,
    brand,
    priority,
    unit,
    is_asset,
    quantity_per_student
)
SELECT
    category_match.id,
    seed.name,
    NULLIF(seed.description, ''),
    NULLIF(seed.goal, ''),
    NULL,
    seed.priority,
    NULL,
    seed.is_asset,
    seed.quantity_per_student
FROM temp_seed_products AS seed
LEFT JOIN categories AS parent_match
  ON parent_match.name = seed.parent_category_name
 AND parent_match.parent_id IS NULL
JOIN categories AS category_match
  ON category_match.name = seed.category_name
 AND (
      (seed.parent_category_name IS NULL AND category_match.parent_id IS NULL)
      OR category_match.parent_id = parent_match.id
 )
WHERE NOT EXISTS (
    SELECT 1
    FROM products AS existing
    WHERE existing.category_id = category_match.id
      AND existing.name = seed.name
);

DROP TEMPORARY TABLE IF EXISTS temp_seed_products;
DROP TEMPORARY TABLE IF EXISTS temp_seed_categories;

COMMIT;
