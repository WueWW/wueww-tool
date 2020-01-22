# wueww-tool

Das WueWW-Redaktionstool ist eine Web-Anwendung, die potentiellen Sessiongeber:innen dazu dienen
soll, ihre Sessions zur WueWW 2020 bekannt zu geben. Daran angebunden ist ein kleiner
Freigabeprozess.

Es handelt sich um eine Symfony 5.0 Applikation, die vor der WueWW 2020 zur Verfügung gestellt
werden soll. Noch ist es leider nicht so weit.

## Mitentwickeln

Wenn du Vorschläge zur Applikation hast, dann nichts wie los.  Auf GitHub unter *Issues* kannst du
gerne eigene Vorschläge einbringen bzw. dich an Diskussionen beteiligen.

Falls du aktiv mit an der Anwendung hacken möchtest, dein Beitrag ist herzlich willkommen :-)

Um die Applikation lokal auszuprobieren, bietet sich der Symfony DEV Web-Server in Kombination
mit SQLite als Datenbank an.  Dazu musst du nichts weiter konfigurieren, sondern einfach nur
das Git Repository klonen, dann composer & yarn install, dann die Anwendung starten.

 Step 1: Abhängigkeiten installieren + Build der JavaScript/CSS Assets

 ```
$ composer install
$ yarn install
$ yarn build
```

Step 2: Datenbank erstellen

```
$ bin/console doctrine:database:create -n
$ bin/console doctrine:schema:create -n
```

Wenn du Dummy-Daten in der Datenbank haben möchtest, dann zusätzlich

```
$ bin/console doctrine:fixtures:load -n
```

Vielleicht möchtest du auch noch einen Benutzer mit der EDITOR-Rolle anlegen (aka Admin), dazu

```
$ bin/console app:create-editor adminuser@example.org
```

Last but not least, die Anwendung starten

```
$ bin/console cache:warmup -n
$ bin/console server:run
```