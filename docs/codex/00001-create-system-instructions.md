# Begroting systeem "Smart Technology"
Maak een systeem waarin ik een inkooplijst aan kan maken voor het opzetten van een lessenserie over Smart Technology. Deze lessenserie zal initieel aan een groep van circa 50 studenten gegeven gaan worden maar zal in de komende jaren terug gaan komen. 

Ik wil graag een API waarmee ik een dump uit het systeem kan halen en gegevens aan het systeem toe kan voegen. Voor elk soort gegeven wil ik een apart endpoint hebben met heldere documentatie. Authenticatie mag met een eenvoudige key die als GET variabele meegegeven kan worden, meerdere keys moeten ondersteund worden dus graag ergens een array neerzetten.

## Product informatie
Het systeem moet een product catalogus gaan bevatten.

1. Per artikel wil ik aan kunnen geven of het gaat een duurzaam bedrijfsmiddel of om een grondstof die eenmalig te gebruiken is. 
2. Per artikel moet het mogelijk zijn om meerdere mogelijke leveranciers toe te kunnen voegen.
3. Per artikel wil ik aan kunnen geven hoeveel we er per student nodig zullen hebben, dit mag een positieve waarde zijn of een fractie wanneer het bijvoorbeeld om een 3d printer of soldeerbout gaat, artikelen waarvan we er per groep studenten een nodig hebben. 
4. Per artikel moet er een beschrijving in te voeren zijn.
5. Per artikel moet er een doel in te voeren zijn.
6. Per artikel moet er een categorie in te voeren zijn.
7. Per artikel moeten er meerdere afbeeldingen kunnen worden toegevoegd. 
8. Per artikel moet er optioneel een merk toegevoegd kunnen worden.
9. Per artikel wil ik een prioritering aan kunnen geven. (0-10)
10. Per artikel wil ik een eenheid in kunnen voeren (optioneel).

## Leveranciers
1. Graag een lijst met leveranciers die vanuit het hoofdmenu bereikbaar is.
2. Per leverancier graag een koppeltabel naar producten, in de koppeltabel moet ook de inkoopprijs van het product komen en additionele informatie over de verpakking.
3. Per leverancier wil ik meerdere bestanden kunnen uploaden
   - een document met de leveranciersgegevens
   - meerdere documenten met product catalogussen
- Per leverancier wil ik een koppeling kunnen maken met producten waarbij ik per artikel een prijs op kan geven.
- Per leverancier wil ik verzendkosten in kunnen voeren, ik wil dit zowel inclusief als exclusief btw kunnen invoeren waarbij het systeem vanzelf de andere variant brekend op basis van het ingevoerde btw tarief (standaard 21%)
- Per leverancier / product wil ik zowel de inclusief als de exclusief btw prijs in kunnen voeren, op basis van het btw tarief moet de andere variant dan automatisch gegenereerd worden.

## Categorisering
1. Ik wil graag een beheer hebben voor de categorisering van de producten. Dit moet een boomstructuur worden en ik zou graag willen dat die boomstructuur ook als zodanig zichtbaar wordt.

## Calculator
1. In de calculator wil ik een prioriteit aan kunnen geven, op basis van deze prioriteit zal het systeem een selectie maken van de benodigde artikelen.
2. In de calculator wil ik het aantal studenten op kunnen geven, dit aantal moet worden gebruikt om de begroting mee te berekenen.
3. De calculator moet een lijst met leveranciers tonen, ik wil in de lijst leveranciers kunnen kiezen, ook producten zonder leverancier moeten in de begroting komen.
4. Ik wil in de calculator graag de boomstructuur met categorieën zien, in deze structuur wil ik categorieën aan of uit kunnen vinken.
5. Telkens wanneer ik iets wijzig aan de variabelen die in de calculator worden gebruikt moet het systeem de begroting opnieuw berekenen.
6. Ik wil graag inclusief of exclusief btw in een dropdown zien waarmee ik de weergave van de begroting aan kan passen.


### Begroting
1. Ik wil graag dat de begroting bestaat uit een tabel met artikelen, de beste leverancier en een prijs.
2. Ik wil graag dat de begroting op basis van de inclusief exclusief btw dropdown de prijzen exclusief btw laat zien met onderin een btw berekening of inclusief met onderin geen btw berekening. 
3. Ik wil graag dat de begroting ook een indicatie geeft van de verzendkosten, dit mag gewoon op basis van wat ik heb ingevuld bij de leveranciers en het aantal leveranciers, door de bedragen bij elkaar op te tellen.

## Codebase
- Maak gebruik maken van het Symfony console commando voor taken die op de commandline moeten worden uitgevoerd.
- Initialiseer een composer.json bestand initialiseren met psr4 autoloading, namespace Roc\SmartTech\Begroting.
- De php code in een src map stoppen.
- Gebruik maken van twig/twig als template engine.
- Een .htaccess bestand toevoegen die alle requests via index.php laat lopen.
- Maak gebruik van symfony/routing voor routing.
- In src een sub map classes en een map modules maken. De modules map moet zowel de controllers als de views bevatten. 
- De controllers moeten een parse method gebruiken en standaard een run functie hebben. 
- Een controller per pagina type, leveranciers en leverancier zijn aparte controllers.

## Overige requirements
1. Het systeem achter een login plaatsen.
2. Een zakelijke look met een lichte achtergrond, geen gradients
3. Een console commando toevoegen voor het beheren van gebruikers
4. Een create table script aanmaken voor de database.
5. Een README.md met informatie over het systeem

