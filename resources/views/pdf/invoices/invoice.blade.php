<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung</title>
    <style>
        .clearfix:after {

            content: "";
            display: table;
            clear: both;
        }


        /* Allgemeine Stile */
        body {
            position: relative;
            width: 19cm;
            height: 29.7cm;
            margin: 1cm auto;
            padding: 0;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }


        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        /* Tabellenstile */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #DDDDDD;
        }

        table th {
            background: #57B223;
            color: #FFFFFF;
            font-weight: bold;
        }

        table tbody tr:nth-child(odd) {
            background: #F9F9F9;
        }

        table tbody tr:nth-child(even) {
            background: #EEEEEE;
        }

        table tfoot td {
            font-weight: bold;
            background: #FFFFFF;
            border-top: 2px solid #AAAAAA;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }


        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices-creator {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            padding-bottom: 4px;
        }


        #paypal {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            padding-bottom: 4px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #AAAAAA;
            padding: 10px;
            font-size: 10px;
            background: #FFFFFF;
        }
    </style>
</head>

<body>
    <header class="clearfix">

        <div id="logo">
            @if ($creator->logo_path)
                <img src="{{ storage_path('app/public/' . $creator->logo_path) }}" alt="Logo">
            @else
                <div class="no-logo">Kein Logo vorhanden.</div>
            @endif
        </div>
        <div id="company">
            <h2 class="name">{{ $creator->company_name }}</h2>
            <div>{{ $creator->address }}</div>
            <div>{{ $creator->postal_code }} {{ $creator->city }}</div>
            <div>{{ $creator->email }}</div>
        </div>
    </header>

    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">RECHNUNG AN:</div>
                <h2 class="name">{{ $recipient->name }}</h2>
                <div class="address">{{ $recipient->address }}, {{ $recipient->zip_code }} {{ $recipient->city }}</div>
                <div class="email">{{ $recipient->email }}</div>
            </div>
            <div id="invoice">
                <h1>Rechnung {{ $invoice->invoice_number }}</h1>
                <div class="date">Rechnungsdatum: {{ $invoice->invoice_date }}</div>
                <div class="date">Fälligkeitsdatum: {{ $invoice->due_date }}</div>
            </div>
        </div>

        <!-- Rechnungspositionen -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>BESCHREIBUNG</th>
                    <th>EINZELPREIS</th>
                    <th>MENGE</th>
                    <th>NETTO</th>
                    <th>MWST (%)</th>
                    <th>GESAMTPREIS (BRUTTO)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="no">{{ $item->item_number }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->unit_price, 2, ',', '.') }} €</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }} €</td>
                        <td>{{ number_format($item->tax_rate, 2, ',', '.') }}%</td>
                        <td>{{ number_format($item->total_price, 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Steuerzusammenfassung -->
        <table>
            <thead>
                <tr>
                    <th>Steuersatz</th>
                    <th>Netto</th>
                    <th>Steuerbetrag</th>
                    <th>Brutto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxDetails as $tax)
                    <tr>
                        <td>{{ number_format($tax['rate'], 2, ',', '.') }}%</td>
                        <td>{{ number_format($tax['net'], 2, ',', '.') }} €</td>
                        <td>{{ number_format($tax['tax'], 2, ',', '.') }} €</td>
                        <td>{{ number_format($tax['gross'], 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Gesamtsummen -->
        <table>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">GESAMTNETTO</td>
                    <td>{{ number_format($totalNet, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">GESAMT MWST</td>
                    <td>{{ number_format($totalTax, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">GESAMTBETRAG</td>
                    <td>{{ number_format($totalGross, 2, ',', '.') }} €</td>
                </tr>
            </tfoot>
        </table>

        <div id="thanks">Vielen Dank für Ihren Auftrag!</div>

        @if ($creator->notes)
            <div id="notices-creator">
                <div>HINWEIS:</div>
                <div class="notice">{{ $creator->notes }}</div>
            </div>
        @endif

        @if ($creator->accept_paypal)
            <div id="paypal">
                <div>Bezahlen via PayPal:</div>
                <div class="notice">{{ $creator->paypal_account }}</div>
            </div>
        @endif

        <div id="notices">
            <div>HINWEIS:</div>
            <div class="notice">Diese Rechnung wurde elektronisch erstellt und ist ohne Unterschrift gültig.</div>
        </div>
    </main>

    <footer>
        {{ $creator->company_name }} - erstellt am {{ date('d.m.Y') }}.
    </footer>
</body>

</html>
