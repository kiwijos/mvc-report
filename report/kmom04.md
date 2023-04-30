# kmom04
### Berätta hur du upplevde att skriva kod som testar annan kod med PHPUnit och hur du upplever phpunit rent allmänt.
Att skriva testerna gav ett par tankeställare. Jag råkade som exempel upptäcka två saker i källkoden som inte fungerade som jag hade tänkt mig. När något var svårtestat fick man verkligen anledning att gå tillbaka och fundera på hur man hade kunnat göra koden bättre.

Kollar vi på PHPUnit så påminner det en hel del om när vi gjorde unittester i Python, fast namngivningen och syntaxen är lite annorlunda förstås. Nu när vi blivit varma i de objektorienterade kläderna gick det fort att komma igång.

### Hur väl lyckades du med kodtäckningen av din kod, lyckades du nå mer än 90% kodtäckning?
Jag lyckades täcka in det mesta rätt bra och nådde även 90% på totalen. Problemen uppstod när det var dags att testa den överordnade spelklassen (se nästa stycke). 

### Upplever du din egen kod som “testbar kod” eller finns det delar i koden som är mer eller mindre testbar och finns det saker som kan göras för att förbättra kodens testbarhet?
Den överordnade spelklassen som styr över spelets gång var svår att testa. Den har många beroenden i form av andra klasser, varav en del också beror på något. Nästan alla metoder uppdaterar dessutom bara klassens state utan att returnera något. För att inte tala om att flera av dem gör anrop till andra metoder. De här sakerna gjorde det svårt att testa enskilda delar av klassen.

En förhållandevis enkel förbättring som skulle göra klassen mer testbar är att ta bort metodanropen inuti andra metoder och istället göra anropen utanför. Kanske hade man även vunnit på att returnera ett faktiskt värde istället för att tyst uppdatera klassens state. Då hade man kunnat ta det där returvärdet och stoppa in det i klassen igen fast med en settermetod så att det blir tydligt vad man gör.

### Valde du att skriva om delar av din kod för att förbättra den eller göra den mer testbar, om så berätta lite hur du tänkte.
Vissa delar skulle jag vilja skriva om från grunden. Men jag har inte känt att det funnits tid till det just nu så jag har nöjt mig med att göra småändringar och att se det hela som en läxa inför kommande projekt. En sak som jag ändrade är metoden som kollar om någon har vunnit. Innan var den privat och anropades inuti metoderna för att dela ut kort till spelaren och bankiren. Då den jämför spelarens och bankirens poäng var jag tvungen att ha instanser av både även om jag bara ville testa den ena. Så jag gjorde metoden publik och anropar den efter anropet till metoderna som drar kort istället.

### Fundera över om du anser att testbar kod är något som kan identifiera “snygg och ren kod”.
Många saker som gör kod testbar gör onekligen också att den blir ”snygg”. Ett exempel har med tätt kopplad kod att göra. Som jag redan varit inne på kan sådan kod vara svår att testa (som i fallet med metoderna som i sin tur anropar andra metoder). Tätt kopplad kod kan också vara svår att underhålla i längden, för om någon del slutar fungera gör även de andra delarna det. Så det här är något man borde undvika i allmänhet. Ett annat exempel är komplex kod. Många villkor kräver minst lika många testfall och gör ofta koden svår att tolka.
Det finns även saker som gör kod testbar utan att direkt göra den snyggare. Som exempel hade väl det enklaste varit om alla properties i klassen man testar var publika så att man hela tiden hade full tillgång till dem. Men det här är sällan önskvärt i andra fall då man försöker vara noga med vilka delar som är tillgängliga.

### Vilken är din TIL för detta kmom?
Den här gångens TIL får bli att komma igång med PHPUnit. Det blev en del ”men hur tänkte jag här?” och ”jag önskar att jag gjort det här annorlunda…”. Men det är alltid kul när man lär sig något och förhoppningsvis lyckas man förbättra sin kod.
