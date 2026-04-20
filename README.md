# Smart Technology Begroting
.
Webapplicatie voor het beheren van een productcatalogus, leveranciers, categorieen en een begrotingscalculator voor een Smart Technology lessenserie.

## Functies

- Loginbeveiligde beheeromgeving
- Productcatalogus met categorie, doel, beschrijving, prioriteit, merk, eenheid en benodigde hoeveelheid per student
- Ondersteuning voor meerdere productafbeeldingen
- Leveranciersbeheer met verzendkosten incl./excl. btw, bestandsuploads en productkoppelingen inclusief prijs en verpakkingsinformatie
- Categoriebeheer als boomstructuur
- Calculator met filters op prioriteit, aantal studenten, leveranciers, categorieen en btw-weergave
- API met aparte endpoints voor producten, leveranciers, categorieen en leverancier-productkoppelingen
- Symfony Console-commando voor gebruikersbeheer
- SQL-script om de database op te bouwen

## Installatie

1. Installeer dependencies:

```bash
composer install
```

2. Maak de database aan en voer vervolgens [database/create_tables.sql](/home/anton/Documents/sites/begroting/database/create_tables.sql:1) uit.

3. Controleer `.env` en vul juiste databasegegevens in.

4. Stel API-keys in via `config/api_keys.php`.

5. Maak een eerste gebruiker aan:

```bash
php bin/console app:user create beheer beheer@example.com sterk-wachtwoord "Beheerder"
```

6. Configureer de webserver zodat `public_html/` de document root is.

## Console

Gebruikers beheren:

```bash
php bin/console app:user list
php bin/console app:user create beheer beheer@example.com wachtwoord "Beheerder"
```

## API

Authenticatie verloopt via `?api_key=JOUW_SLEUTEL`.

- `GET|POST /api/products`
- `GET|POST /api/suppliers`
- `GET|POST /api/categories`
- `GET|POST /api/supplier-products`

Meer details staan in de UI onder `/api/docs`.

## Structuur

- `src/classes`: infrastructuur, repositories, services en console commands
- `src/modules`: controllers en views per paginatype
- `public_html`: front controller, uploads en assets
- `database/create_tables.sql`: database-opbouw
