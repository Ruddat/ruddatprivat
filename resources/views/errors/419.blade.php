@extends("layouts.app")

@section("title", "Seite abgelaufen")

@section("content")
    <div class="container text-center mt-5">
        <h1 class="display-1">419</h1>
        <h2 class="mb-4">Seite abgelaufen</h2>
        <p>Entschuldigung, Ihre Sitzung ist abgelaufen. Bitte laden Sie die Seite neu oder melden Sie
            sich erneut an.</p>
        <a href="{{ url("/") }}" class="btn btn-primary mt-3">Zurück zur Startseite</a>
    </div>
@endsection
<style>
    /* Fehlerseite - 419 */
    .error-page {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        /* Vollbildhöhe */
        text-align: center;
        background: #f9f9f9;
        /* Helles Hintergrunddesign */
        color: #333;
        /* Dunkle Schriftfarbe */
        font-family: Arial, sans-serif;
    }

    .error-page h1 {
        font-size: 8rem;
        /* Sehr große Schrift für die Fehlernummer */
        font-weight: bold;
        margin: 0;
        color: #ff6b6b;
        /* Rot für die Fehlernummer */
    }

    .error-page h2 {
        font-size: 2rem;
        font-weight: 500;
        margin: 1rem 0;
    }

    .error-page p {
        font-size: 1rem;
        margin-bottom: 2rem;
        color: #555;
    }

    .error-page a {
        display: inline-block;
        padding: 10px 20px;
        font-size: 1rem;
        color: #fff;
        background-color: #007bff;
        /* Blau für den Button */
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .error-page a:hover {
        background-color: #0056b3;
        /* Dunkleres Blau beim Hover */
    }
</style>
