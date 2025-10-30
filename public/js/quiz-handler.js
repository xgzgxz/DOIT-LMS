                   // 1. Warten, bis das ganze HTML geladen ist
                    document.addEventListener('DOMContentLoaded', function() {
                        
                        // 2. Das Formular und das Feedback-Feld holen
                        const quizForm = document.getElementById('quiz-form');
                        const quizFeedback = document.getElementById('quiz-feedback');

                        // Hole das Container-Div, an dem das Etikett klebt
                        const quizContainer = document.getElementById('quiz-container');

                        // 3. Prüfen, ob das Formular überhaupt auf der Seite ist
                        if (quizForm && quizContainer) {
                            const checkAnswerUrl = quizContainer.dataset.checkUrl;
                            // 4. Einen "submit"-Listener auf das Formular legen
                            quizForm.addEventListener('submit', function(event) {
                                
                                // 5. DAS WICHTIGSTE: Das Standard-Verhalten (Seite neu laden) VERHINDERN
                                event.preventDefault();

                                // 6. Die ausgewählte Antwort finden
                                const selectedAnswer = quizForm.querySelector('input[name="quiz_answer"]:checked');

                                // 7. Prüfen, ob überhaupt was ausgewählt wurde
                                if (!selectedAnswer) {
                                    quizFeedback.style.color = 'red';
                                    quizFeedback.textContent = 'Bitte wähle eine Antwort aus.';
                                    return; // Funktion hier abbrechen
                                }

                                // 8. Die ID der Antwort holen (z.B. "12")
                                const answerId = selectedAnswer.value;

                                // 9. Die AJAX-Anfrage (fetch) an unser neues Backend
                                fetch(checkAnswerUrl, {
                                    method: 'POST', // Wir senden Daten
                                    headers: {
                                        'Content-Type': 'application/json', // Wir sagen, wir senden JSON
                                        'Accept': 'application/json'
                                    },
                                    // Wir packen die ID in einen JSON-String
                                    body: JSON.stringify({ answer_id: answerId })
                                })
                                .then(response => response.json()) // Die JSON-Antwort vom Server ("{correct: true}") umwandeln
                                .then(data => {
                                    // 10. Die Antwort vom Server (data) verarbeiten
                                    if (data.correct) {
                                        // ---- RICHTIG ----
                                        quizFeedback.style.color = 'green';
                                        quizFeedback.textContent = 'Richtig! Sehr gut.';
                                    } else {
                                        // ---- FALSCH ----
                                        quizFeedback.style.color = 'red';
                                        quizFeedback.textContent = 'Das war leider nicht richtig. Versuch es noch einmal!';
                                    }
                                })
                                .catch(error => {
                                    // Falls was schiefgeht (z.B. Server-Fehler)
                                    console.error('Fehler beim Prüfen:', error);
                                    quizFeedback.style.color = 'red';
                                    quizFeedback.textContent = 'Ein Fehler ist aufgetreten. Bitte lade die Seite neu.';
                                });
                            });
                        }
                    });