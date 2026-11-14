@if ($alert->isTest)
Dies ist eine Testbenachrichtigung von RuddatTech.
Der E-Mail-Kanal für neue Anfragen ist korrekt eingerichtet.
@else
Neue Anfrage über das Kontaktformular

Vorgang:      {{ $alert->reference }}
Unternehmen:  {{ $alert->company }}
Ansprechpartner: {{ $alert->contactName }}
Thema:        {{ $alert->topic }}

Alle Angaben und die Beschreibung finden Sie im Adminbereich:
{{ $alert->adminUrl }}
@endif

--
Automatische interne Benachrichtigung der RuddatTech-Webseite.
