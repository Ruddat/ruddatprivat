<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mietquittung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            background-color: #fff;
            line-height: 1.3;
            font-size: 12px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            padding: 20px;
            border: 1px solid #5066a8;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #5066a8;
        }
        h1 {
            font-size: 20px;
            color: #5066a8;
            margin: 0;
            text-transform: uppercase;
        }
        .receipt-number {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        .address-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
        }
        .address-box {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .address-box h3 {
            margin: 0 0 8px 0;
            color: #5066a8;
            font-size: 13px;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }
        .address-line {
            margin: 3px 0;
            font-size: 11px;
        }
        .address-line strong {
            display: block;
            margin-bottom: 2px;
        }
        .compact-line {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .date-section {
            text-align: right;
            margin: 15px 0;
            font-style: italic;
            font-size: 11px;
        }
        .amount-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 3px solid #5066a8;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        .amount-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            color: #5066a8;
        }
        .amount-label {
            font-weight: bold;
            font-size: 11px;
        }
        .amount-value {
            text-align: right;
            font-size: 11px;
        }
        .amount-in-words {
            background: #e8f4fd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-style: italic;
            font-size: 11px;
            border-left: 3px solid #2196F3;
        }
        .description-section {
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
            font-size: 11px;
        }
        .description-section h3 {
            margin: 0 0 6px 0;
            color: #5066a8;
            font-size: 12px;
        }
        .tax-info {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #5066a8;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .verification {
            background: #fff8e1;
            padding: 10px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 10px;
            border: 1px solid #ffd54f;
        }
        .verification h4 {
            margin: 0 0 5px 0;
            color: #e65100;
            font-size: 11px;
        }
        .hash {
            font-family: monospace;
            word-break: break-all;
            background: #f5f5f5;
            padding: 3px;
            border-radius: 2px;
            font-size: 9px;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 180px;
            text-align: center;
            padding-top: 3px;
            font-size: 10px;
        }
        .company-info {
            text-align: center;
            margin-top: 8px;
            font-size: 10px;
            color: #666;
            line-height: 1.2;
        }
        .compact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 10px;
        }
        .print-button {
            display: block;
            margin: 15px auto;
            padding: 6px 12px;
            font-size: 11px;
            color: #fff;
            background-color: #5066a8;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
            }
            .container {
                border: none;
                padding: 15px;
            }
        }
    </style>
    <script>
        function printReceipt() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Mietquittung</h1>
            <div class="receipt-number">Quittungsnummer: {{ $receipt->number }}</div>
        </div>

        <!-- Adressen kompakt nebeneinander -->
        <div class="address-section">
            <!-- Absender (Vermieter) -->
            <div class="address-box">
                <h3>Absender</h3>
                <div class="address-line">
                    <strong>{{ $receipt->sender }}</strong>
                </div>
                @if($receipt->sender_street || $receipt->sender_house_number)
                <div class="address-line">{{ $receipt->sender_street }} {{ $receipt->sender_house_number }}</div>
                @endif
                @if($receipt->sender_zip || $receipt->sender_city)
                <div class="address-line">{{ $receipt->sender_zip }} {{ $receipt->sender_city }}</div>
                @endif
                <div class="compact-grid">
                    @if($receipt->sender_phone)
                    <div>Tel: {{ $receipt->sender_phone }}</div>
                    @endif
                    @if($receipt->sender_email)
                    <div>Mail: {{ $receipt->sender_email }}</div>
                    @endif
                </div>
                @if($receipt->sender_tax_number)
                <div class="tax-info">St.-Nr.: {{ $receipt->sender_tax_number }}</div>
                @endif
            </div>

            <!-- Empfänger (Mieter) -->
            <div class="address-box">
                <h3>Empfänger</h3>
                <div class="address-line">
                    <strong>{{ $receipt->receiver }}</strong>
                </div>
                @if($receipt->receiver_street || $receipt->receiver_house_number)
                <div class="address-line">{{ $receipt->receiver_street }} {{ $receipt->receiver_house_number }}</div>
                @endif
                @if($receipt->receiver_zip || $receipt->receiver_city)
                <div class="address-line">{{ $receipt->receiver_zip }} {{ $receipt->receiver_city }}</div>
                @endif
                <div class="compact-grid">
                    @if($receipt->receiver_phone)
                    <div>Tel: {{ $receipt->receiver_phone }}</div>
                    @endif
                    @if($receipt->receiver_email)
                    <div>Mail: {{ $receipt->receiver_email }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Datum -->
        <div class="date-section">
            {{ $receipt->sender_city }}, den {{ \Carbon\Carbon::parse($receipt->date)->format('d.m.Y') }}
        </div>

        <!-- Beträge kompakt -->
        <div class="amount-section">
            <div class="amount-row">
                <div class="amount-label">Betrag (Netto)</div>
                <div class="amount-value">{{ number_format($receipt->amount, 2, ',', '.') }} €</div>
            </div>

            @if($receipt->tax_percent && $receipt->tax_amount > 0)
            <div class="amount-row">
                <div class="amount-label">zzgl. {{ $receipt->tax_percent }}% USt.</div>
                <div class="amount-value">{{ number_format($receipt->tax_amount, 2, ',', '.') }} €</div>
            </div>
            @endif

            <div class="amount-row">
                <div class="amount-label">Gesamtbetrag (Brutto)</div>
                <div class="amount-value">{{ number_format($receipt->amount + $receipt->tax_amount, 2, ',', '.') }} €</div>
            </div>
        </div>

        <!-- Betrag in Worten -->
        @if($receipt->amount_in_words)
        <div class="amount-in-words">
            <strong>In Worten:</strong> {{ $receipt->amount_in_words }}
        </div>
        @endif

        <!-- Beschreibung -->
        @if($receipt->description)
        <div class="description-section">
            <h3>Verwendungszweck</h3>
            <div>{{ $receipt->description }}</div>
        </div>
        @endif

        <!-- Verifikation kompakt -->
        <div class="verification">
            <h4>Elektronische Verifikation</h4>
            <p>Diese Quittung wurde digital erstellt und ist rechtlich gültig ohne Unterschrift.</p>
            @if($receipt->hash)
            <div class="tax-info">
                <strong>Prüfsumme:</strong> <span class="hash">{{ $receipt->hash }}</span>
            </div>
            @endif
        </div>

        <!-- Signaturbereich -->
        <div class="signature-section">
            <div class="signature-line">
                _________________________<br>
                Ort, Datum
            </div>
            <div class="signature-line">
                _________________________<br>
                Unterschrift Vermieter
            </div>
        </div>

        <!-- Footer mit Firmendaten -->
        <div class="footer">
            @php
                $companyName = \App\Models\Setting::where('key', 'name')->where('group', 'company')->value('value') ?? 'Homelengo';
                $companyAddress = \App\Models\Setting::where('key', 'address')->where('group', 'company')->value('value') ?? '';
                $companyCity = \App\Models\Setting::where('key', 'city')->where('group', 'company')->value('value') ?? '';
                $companyPhone = \App\Models\Setting::where('key', 'phone')->where('group', 'company')->value('value') ?? '';
                $companyEmail = \App\Models\Setting::where('key', 'email')->where('group', 'company')->value('value') ?? 'support@homelengo.de';
                $companyWebsite = \App\Models\Setting::where('key', 'website')->where('group', 'company')->value('value') ?? 'www.homelengo.de';
                $companyTaxId = \App\Models\Setting::where('key', 'tax_id')->where('group', 'company')->value('value') ?? '';
            @endphp

            <p><strong>Erstellt mit {{ $companyName }}</strong></p>

            <div class="company-info">
                @if($companyAddress || $companyCity)
                <div>{{ $companyAddress }}, {{ $companyCity }}</div>
                @endif
                @if($companyPhone || $companyEmail)
                <div>
                    @if($companyPhone)Tel: {{ $companyPhone }}@endif
                    @if($companyPhone && $companyEmail) | @endif
                    @if($companyEmail)Mail: {{ $companyEmail }}@endif
                </div>
                @endif
                @if($companyWebsite)
                <div>Web: {{ $companyWebsite }}</div>
                @endif
                @if($companyTaxId)
                <div class="tax-info">USt-IdNr.: {{ $companyTaxId }}</div>
                @endif
            </div>

            <p class="tax-info">Elektronisch erstellt gemäß §126b BGB</p>
        </div>

        <!-- Druckbutton -->
        <button class="print-button" onclick="printReceipt()">Quittung drucken</button>
    </div>
</body>
</html>
