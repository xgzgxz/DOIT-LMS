# GitHub & Git: Dein Spickzettel

Dieses Dokument fasst die wichtigsten Git-Befehle für den täglichen Gebrauch zusammen.

---

## Szenario 1: Ein komplett neues Projekt starten und auf GitHub hochladen

Dies tust du nur **einmal** pro Projekt.

### Schritt 1: Lokales Projekt vorbereiten (auf deinem PC)

1.  **Öffne das Terminal** in deinem Projektordner (in VS Code: `Terminal` > `New Terminal`).

2.  **Initialisiere Git** (sagt Git, dass es diesen Ordner überwachen soll):
    ```bash
    git init
    ```

3.  **Füge alle deine Projektdateien hinzu**, um sie für den ersten "Schnappschuss" vorzubereiten:
    ```bash
    git add .
    ```

4.  **Erstelle den ersten "Schnappschuss" (Commit)** mit einer Nachricht:
    ```bash
    git commit -m "Initial commit: Projektstart"
    ```

### Schritt 2: GitHub-Repository vorbereiten (im Browser)

1.  Gehe auf [github.com/new](https://github.com/new).
2.  Gib deinem Projekt einen Namen.
3.  Stelle sicher, dass es **Public** ist.
4.  **WICHTIG:** Setze **KEINE** Haken bei `README`, `.gitignore` oder `license`. Das Repository muss komplett leer sein.
5.  Klicke auf **"Create repository"**.

### Schritt 3: Lokales Projekt mit GitHub verbinden und hochladen

1.  **Kopiere die URL** deines neuen, leeren GitHub-Repositorys. Sie sieht so aus: `https://github.com/DEIN-NAME/PROJEKT-NAME.git`

2.  **Verbinde dein lokales Projekt** mit der GitHub-URL (ersetze die URL durch deine):
    ```bash
    git remote add origin https://github.com/DEIN-NAME/PROJEKT-NAME.git
    ```
    *(`origin` ist nur ein Spitzname für die lange URL)*

3.  **Lade deine Commits zum ersten Mal hoch:**
    ```bash
    git push -u origin master
    ```
    *(oder `main` statt `master`, je nachdem, wie dein Branch heißt)*

---

## Szenario 2: An einem bestehenden Projekt weiterarbeiten

Das ist dein täglicher Arbeitsablauf.

1.  **Du änderst deinen Code** (schreibst, löschst, bearbeitest Dateien).

2.  **Änderungen für den nächsten "Schnappschuss" auswählen:**
    ```bash
    git add .
    ```

3.  **Den "Schnappschuss" (Commit) erstellen** und beschreiben, was du getan hast:
    ```bash
    git commit -m "Eine kurze, aussagekräftige Nachricht, z.B. 'Suchfunktion verbessert'"
    ```

4.  **Alle neuen Commits zu GitHub hochladen:**
    ```bash
    git push
    ```

---

## Szenario 3: Ein bestehendes Projekt von GitHub herunterladen

Wenn du an einem anderen Computer weiterarbeiten oder ein fremdes Projekt herunterladen möchtest.

1.  Gehe auf die GitHub-Seite des Projekts.
2.  Klicke auf den grünen **"< > Code"**-Button.
3.  Kopiere die **HTTPS-URL**.

4.  Öffne das Terminal in dem Ordner, **wo dein Projektordner erstellt werden soll** (z.B. auf dem Desktop).

5.  **Klone das Projekt** auf deinen Computer (ersetze die URL):
    ```bash
    git clone https://github.com/DEIN-NAME/PROJEKT-NAME.git
    ```
    *Git lädt das Projekt herunter und erstellt automatisch den richtigen Ordner.*

Danach kannst du direkt mit dem Arbeitsablauf aus **Szenario 2** weitermachen.
