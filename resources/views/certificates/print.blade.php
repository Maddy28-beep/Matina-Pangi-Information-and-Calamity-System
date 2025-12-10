<!DOCTYPE html>
<html>
<head>
    <title>{{ $certificate->type_label }} - {{ $certificate->certificate_number }}</title>
    <style>
        @page { margin: 1in; }
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
        }
        .logo {
            width: 100px;
            height: 100px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .subtitle {
            font-size: 14px;
            font-style: italic;
        }
        .cert-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
        .content {
            text-align: justify;
            font-size: 14px;
            margin: 30px 0;
            text-indent: 50px;
        }
        .resident-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
        }
        .signature-section {
            margin-top: 60px;
            text-align: right;
        }
        .signature-line {
            border-top: 2px solid #000;
            width: 250px;
            display: inline-block;
            margin-top: 50px;
        }
        .cert-number {
            margin-top: 30px;
            font-size: 12px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="position: fixed; top: 10px; right: 10px; padding: 10px 20px; background: #3AB795; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Print Certificate
    </button>

    <div class="header">
        <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
        <div class="title">REPUBLIC OF THE PHILIPPINES</div>
        <div class="title">BARANGAY MATINA PANGI</div>
        <div class="subtitle">Davao City</div>
    </div>

    <div class="cert-title">{{ strtoupper($certificate->type_label) }}</div>

    <div class="content">
        <p><strong>TO WHOM IT MAY CONCERN:</strong></p>

        <p>
            This is to certify that <span class="resident-name">{{ strtoupper($certificate->resident->full_name) }}</span>,
            {{ $certificate->resident->age }} years old, {{ $certificate->resident->civil_status }},
            Filipino citizen, and a bonafide resident of {{ $certificate->resident->household->address }},
            Barangay Matina Pangi, Davao City.
        </p>

        <p>
            This certification is issued upon the request of the above-named person for
            <strong>{{ strtoupper($certificate->purpose) }}</strong>.
        </p>

        <p>
            Issued this {{ $certificate->issued_date->format('jS') }} day of {{ $certificate->issued_date->format('F Y') }}
            at Barangay Matina Pangi, Davao City, Philippines.
        </p>
    </div>

    <div class="signature-section">
        <div class="signature-line"></div>
        <div><strong>BARANGAY CAPTAIN</strong></div>
        <div>Barangay Matina Pangi</div>
    </div>

    <div class="cert-number">
        <strong>Certificate No.:</strong> {{ $certificate->certificate_number }}<br>
        @if($certificate->or_number)
            <strong>OR No.:</strong> {{ $certificate->or_number }}<br>
        @endif
        <strong>Date Issued:</strong> {{ $certificate->issued_date->format('F d, Y') }}<br>
        @if($certificate->valid_until)
            <strong>Valid Until:</strong> {{ $certificate->valid_until->format('F d, Y') }}
        @endif
    </div>
</body>
</html>
