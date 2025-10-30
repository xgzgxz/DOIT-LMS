// Wir warten, bis die ganze Seite geladen ist, bevor wir unser Skript ausführen
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Finde den Burger-Button anhand seiner ID
    const burgerButton = document.getElementById('burger-menu-button');
    
    // 2. Finde die Navigationsleiste anhand ihrer ID
    const navCourses = document.getElementById('nav-courses-bar');

    // 3. Nur wenn beide Elemente gefunden wurden ...
    if (burgerButton && navCourses) {
        
        // ... fügen wir einen "Klick-Zuhörer" (Event Listener) zum Button hinzu
        burgerButton.addEventListener('click', function() {
            
            // 4. Bei jedem Klick: Schalte die Klasse 'nav-courses-open' auf der Navigationsleiste um.
            // Das heißt: Wenn sie da ist, nimm sie weg. Wenn sie nicht da ist, füge sie hinzu.
            // Den Rest erledigt unser CSS!
            navCourses.classList.toggle('nav-courses-open');
        });
    }

});