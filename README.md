# m3-keresheto-archivum

## Telepítés / futtatás
CodeIgniter 3.1 keretrendszert használ a program. (PHP 5.5+ szükséges)  
Az adatbázis beállításai a [database.php](application/config/database.php#L76) fájlban találhatók, a séma és tábla szerkezetek exportja a [m3.skeleton.sql](.sql/m3.skeleton.sql) fájlban. Ez MySQL/MariaDB formátumban van de használható más, CI által támogatott adatbázis is.  
Ha az oldal valamilyen almappán keresztül érhető el az adott hoszton, a [.htaccess](.htaccess#L10) fájlban a `RewriteBase /` sort szerkeszteni kell és átírni a megfelelő útvonalra.  

## Frontend
Egyetlen oldala van, a kereső és lista.  
A kereső minden rögzített adatban keres, nem csupán abban ami megjelenik, a műsorok sorrendje a közzétételük időpontjának megfelelő.  

### Layout
A material-components-web CSS framework m3 színeivel tematizált változata: [m3-material-components-web](http://github.com/a-sync/m3-material-components-web)

## Backend
Két cronjob-ból áll, amik letöltik, feldolgozzák és az adatbázisban rögzítik az aktuálisan elérhető műsorokat.  

#### Napi
A naponta frissülő műsorlistát ellenőrzi.  
Útvonal: **/cron/daily**

#### Heti
A hetente frissülő műsorlistát ellenőrzi.  
Útvonal: **/cron/weekly**

---

#### TODO:
 * lista helyett kártyák használata, és minden elérhető adat megjelenítése (kép, besorolás, készítők, felirat stb.)
 * adatbázis export funkció, ami lehetővé teszi új oldal esetén a már feldolgozott műsorok importálását
 
