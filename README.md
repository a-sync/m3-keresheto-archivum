# m3-keresheto-archivum

## Telepítés / futtatás
CodeIgniter 3.1 keretrendszert használ az alkalmazás. (minimum PHP 5.6 javasolt)  

Az adatbázis beállításai a [database.php](application/config/database.php#L76) fájlban találhatók.

Ha az oldal valamilyen almappán keresztül érhető el az adott hoszton, a [.htaccess](.htaccess#L10) fájlban a `RewriteBase /` sort szerkeszteni kell és átírni a megfelelő útvonalra.  

## Adatbázis
A séma és tábla szerkezetek exportja az [m3.skeleton.sql](.sql/m3.skeleton.sql) fájlban található. Ez MySQL/MariaDB formátumban van de használható más, CI által támogatott adatbázis is.  

Az éles oldalról elérhető hetente frissülő adatbázis export a [/public/m3-db.gz](https://m3.devs.space/public/m3-db.gz) útvonalon, illetve naponta frissülő részleges CSV export a [/public/m3-db.csv.gz](https://m3.devs.space/public/m3-db.csv.gz) útvonalon.

## Frontend
Egyetlen oldala van, a kereső és lista.  
A kereső minden rögzített adatban keres, nem csupán abban ami megjelenik, a műsorok sorrendje a közzétételük időpontjának megfelelő.  

### Layout
A material-components-web CSS framework m3 színeivel tematizált változata: [m3-material-components-web](http://github.com/a-sync/m3-material-components-web).

## Backend
Két cronjob letölti, feldolgozza és az adatbázisban rögzíti az aktuálisan elérhető műsorokat.  
Két másik adatbázis biztonsági mentést, és részleges CSV exportot hoz létre.  
Egy aloldal pedig műsorok hozzáadását teszi lehetővé M3-* azonosító megadásával.   

#### Napi műsorok frissítése
Útvonal: **/cron/daily**  
A naponta frissülő műsorlistát ellenőrzi.  

#### Heti műsorok frissítése
Útvonal: **/cron/weekly**  
A hetente frissülő műsorlistát ellenőrzi.  

#### Biztonsági mentés (heti)
Útvonal: **/cron/backup**  
A teljes adatbázist exportálja a **/public/m3-db.gz** tömörített fájlba.  

#### CSV export (napi)
Útvonal: **/cron/csv**  
Az alábbi mezőket exportálja a **/public/m3-db.csv.gz** tömörített fájlba.  
 * program_id
 * title
 * subtitle
 * episode
 * episodes
 * seriesId
 * quality
 * year
 * duration
 * short_description

#### Műsor hozzáadása azonosító segítségével
Útvonal: **/cron/add?id=**  
Egy vagy több műsor adatait kéri le és rögzíti az adatbázisban. Több azonosító esetén, vesszővel (`,`) elválasztott listát vár.  
pl.: *https://m3.devs.space/cron/add/?id=M3-123456789,M3-987654321*

---

#### TODO:
 * lista helyett kártya layout használata, és minden elérhető adat megjelenítése (kép, besorolás, készítők, felirat stb.)  
 * rádió műsorok rögzítése (RADIO-*)  
