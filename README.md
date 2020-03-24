# m3-keresheto-archivum

## Telepítés / futtatás
CodeIgniter 3.1 keretrendszert használ az alkalmazás. (minimum PHP 5.6 javasolt)  

Az adatbázis beállításai a [database.php](application/config/database.php#L76) fájlban találhatók.

Ha az oldal valamilyen almappán keresztül érhető el az adott hoszton, a [.htaccess](.htaccess#L10) fájlban a `RewriteBase /` sort szerkeszteni kell és átírni a megfelelő útvonalra.  

## Adatbázis
A séma és tábla szerkezetek exportja az [m3.skeleton.sql](.sql/m3.skeleton.sql) fájlban található. Ez MySQL/MariaDB formátumban van de használható más, CI által támogatott adatbázis is.  

Az éles oldalról elérhető naponta frissülő adatbázis export a [/public/m3-db.gz](https://m3.devs.space/public/m3-db.gz) útvonalon.

## Frontend
Egyetlen oldala van, a kereső és lista.  
A kereső minden rögzített adatban keres, nem csupán abban ami megjelenik, a műsorok sorrendje a közzétételük időpontjának megfelelő.  

### Layout
A material-components-web CSS framework m3 színeivel tematizált változata: [m3-material-components-web](http://github.com/a-sync/m3-material-components-web).

## Backend
Két cronjob letölti, feldolgozza és az adatbázisban rögzíti az aktuálisan elérhető műsorokat.  
Egy pedig adatbázis biztonsági mentést hoz létre.  

#### Napi
A naponta frissülő műsorlistát ellenőrzi.  
Útvonal: **/cron/daily**

#### Heti
A hetente frissülő műsorlistát ellenőrzi.  
Útvonal: **/cron/weekly**

#### Biztonsági mentés (napi)
A teljes adatbázist exportálja a **/public/m3-db.gz** tömörített fájlba.  
Útvonal: **/cron/backup**

---

#### TODO:
 * lista helyett kártya layout használata, és minden elérhető adat megjelenítése (kép, besorolás, készítők, felirat stb.)  
 * rádió műsorok rögzítése (RADIO-*)  
