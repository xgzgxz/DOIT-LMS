document.addEventListener('DOMContentLoaded', function () {

    // --- Logik für die Hauptnavigation ---
    const navToggle = document.querySelector('.nav-toggle');
    const navRight = document.querySelector('.nav-right');

    if (navToggle && navRight) {
        navToggle.addEventListener('click', function() {
            // Fügt eine Klasse zum Body hinzu, um den Zustand zu speichern
            document.body.classList.toggle('nav-open');
        });
    }

    // --- Logik für die Kurs-Navigation ---
    const coursesToggle = document.querySelector('.courses-toggle');
    const coursesLinks = document.querySelector('.courses-links');

    if (coursesToggle && coursesLinks) {
        coursesToggle.addEventListener('click', function() {
            // Fügt eine Klasse zum Body hinzu, um den Zustand zu speichern
            document.body.classList.toggle('courses-open');
        });
    }

});
