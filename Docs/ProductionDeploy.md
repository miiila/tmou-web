# Production deploy

Produkční nasazení není organizováno pomocí Docker kompozice, ale přímo "nativně" (tzn. v Apache + PHP).


## Předpoklady

Nezbytné předpoklady jsou:

- Základní programátorské IT vzdělání, bez toho se nemá smysl do tohoto pouštět.
- GIT
- PHP alespoň ve verzi 7.1
- MySQL databázi ve verzi alespoň 5.6 (např. MariaDB 10)
- Nainstalovaný balíčkovací nástroj [Composer](https://getcomposer.org/) na PATH.

## Postup instalace

1. Naklonujte si repozitář:  
   `git checkout git@github.com:InstruktoriBrno/tmou-web.git`
2. Nainstalujte závislosti spuštěním `composer install --no-dev --optimize-autoloader --no-progress --no-interaction --no-suggest` v rootu webu.
3. Získejte údaje pro připojení do databáze (adresa server, port, jméno, heslo, jméno databáze).  
   Pokud databázi nemáte, vytvořte si ji **s kódování `utf8mb4-czech`**!
4. Nastavte možnost zápisu do adresářů `temp` a `log`: `chmod -R 777 temp log` (ujistěte se, že oprávnění má i proces webového serveru).
5. Vytvořte lokální konfiguraci `App/Config/local.neon`, viz `local.neon.template`,
   zde nastavte údaje pro přístup k databázi, recaptchu, adresu na které web poběží, maily...
6. Smažte obsah adresáře `temp/cache`.
7. Spusťe databázové migrace pro zajištění aktuálnosti databáze: `php bin/console migrations:migrate`
9. Nakonfigurujte Apache tak, aby používal správnou verzi PHP a `DocumentRoot` směřoval do složky `www`.
10. Přistupte skrze prohlížeč na nakonfigurovanou doménu a otestujte základní funkčnost webu.
11. Ověřte, že citlivé soubory nejsou dostupné zvenčí, obzvláště soubor `App/Config/local.neon`.


## Postup aktualizace

1. Odstavte web (viz níže Režim údržby).
2. Zazálujte celou databázi před začátkem aktualzace.
2. Aktualizujte repozitář na požadovanou verzi (pozor na neverzované změny!)
3. Doinstalujte a aktualizujte závislosti dle požadavku dané verze pomocí `composer install --no-dev --optimize-autoloader --no-progress --no-interaction --no-suggest`.
4. Smažte obsah adresáře `temp/cache`, v produkčním režimu se keš sama přegenerovává jen pokud chybí! (Bez toho by aplikace nemusela vůbec naběhnout, nebo by fungovala jako ve staré verzi.)

## Režim údržby

Web má připravený režim údržby (via `.htaccess` v kořenové složce projektu), pro jeho zapnutí stačí vytvořit v kořenové složce projektu
soubor pojmenovaný `UNDER_MAINTENANCE`, který zapříčiní zobrazení `.maintenance.php` na všechny požadavky.

Pro přístup během údržby je potřeba ve svém prohlížeči a na správné adrese vytvořit ve svém prohlížeči ve vývojářských nástrojích
cookie s názvem `SKIP_MAINTENANCE` a hodnotu `true`. Po obnovení stránky by se web měl zobrazit, pokud ne, zkontrolujte přesnou shodu domény, platnost cookie a parametry ohledně její striktnosti.

Pro ukončení stačí soubor `UNDER_MAINTENANCE` odstranit.

## Composer

Pokud nemáte composer na PATH, nebo v systému máte příliš starý, můžete vyřešit problém lokální instalací
(resp. stažením souboru PHAR a jeho spouštěním), viz [návod](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos).

Poté namísto `composer <action>` spouštíte `php composer.phar <action>`. Oba způsoby jsou ekvivalentní.

## Keycloak

Je třeba vytvořit `tmou-web` klienta, správně nastavit:

- `Root URL`
- `Valid Redirect URL`
- `Base URL` (jako absolutní cestu bez hostname)
- `Admin URL`
- `Web origins`

Taktéž je potřeba změnit `Access Type` na confidential. V `Mappers` je potřeba vytvořit Groups mapper, který bude do tokenů přidávat pod klíčem `groups` informace o skupinách uživatele.

Uživatelům, kteří mají mít přístup do TMOU webu je potřeba přidat zařazení do skupiny `Organizátoři TMOU` nebo `tmou_org` (přesná shoda).
