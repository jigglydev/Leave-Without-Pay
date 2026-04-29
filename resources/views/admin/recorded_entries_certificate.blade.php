<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Certificate — {{ $record->snapshot_name ?? $record->user?->full_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 13pt; color: #000; background: #fff; padding: 24px 32px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { height: 75px; margin: 0 8px; }
        .header-logos { display: flex; justify-content: center; align-items: center; margin-bottom: 8px; }
        .header p { line-height: 1.3; font-size: 11pt; }
        .header .agency { font-weight: bold; font-size: 12pt; }
        .header .office-title { font-weight: bold; font-size: 12pt; margin-top: 15px; text-transform: uppercase; }
        .title { text-align: center; font-size: 18pt; font-weight: bold; margin: 30px 0; letter-spacing: 5px; }
        
        .body-para { margin-bottom: 24px; font-size: 12pt; line-height: 1.5; text-align: justify; }
        .indent { text-indent: 50px; }
        
        
        table { margin: 20px auto 30px 60px; font-size: 12pt; border-collapse: collapse; }
        td { padding: 3px 15px; }
        .col-val { text-align: right; }
        
        .sig-grid { margin-top: 70px; width: 100%; position: relative; }
        .sig-block { position: absolute; right: 10%; text-align: center; }
        .sig-name { font-weight: bold; font-size: 12pt; display: block; }
        .sig-title { font-size: 12pt; }
        
        @media print { body { padding: 10mm 15mm; } .no-print { display: none !important; } @page { size: 8.5in 11in; margin: 15mm; } }
        /* Toolbar styles */
        .toolbar { position: fixed; top: 0; left: 0; right: 0; background: #1e293b; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; z-index: 999; gap: 12px; }
        .toolbar button { padding: 7px 18px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity .15s; }
        .toolbar button:hover { opacity: .85; }
        .btn-print { background: #ef4444; color: #fff; }
        .btn-download { background: #3b82f6; color: #fff; }
        .btn-back { background: transparent; color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; }
        .print-area { margin-top: 60px; }
        @media print { .toolbar { display: none; } .print-area { margin-top: 0; } }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <a href="{{ route('admin.recorded-entries') }}" class="btn-back">← Back</a>
        <span style="color:#94a3b8; font-size:13px;">Certificate — {{ $record->snapshot_name ?? $record->user?->full_name }}</span>
        <div style="display:flex;gap:10px;">
            <button class="btn-print" onclick="window.print()">🖨 Print</button>
            <button class="btn-download" onclick="window.print()">⬇ Save as PDF</button>
        </div>
    </div>

    @php
        $u = $record->user ?? $record->employee;
        if ($u) {
            $mi = $u->middle_name ? strtoupper(substr($u->middle_name, 0, 1)) . '.' : '';
            $s = $u->suffix ? ', ' . $u->suffix : '';
            $displayName = trim($u->given_name . ' ' . $mi . ' ' . $u->last_name) . $s;
        } else {
            $displayName = $record->snapshot_name ?? '';
        }
        $displayPosition = $record->snapshot_position ?? ($u ? $u->position : '');
        $displayOffice = $record->snapshot_office ?? ($u ? $u->office : '');
        
        $asOfFormatted = $record->as_of_date ? $record->as_of_date->format('F j, Y') : now()->format('F j, Y');
        
        $vl = $record->el_vl !== null ? (float)$record->el_vl : 0;
        $sl = $record->el_sl !== null ? (float)$record->el_sl : 0;
        $total = $vl + $sl;
        
        $pronoun = ($prefix === 'Mr.') ? 'his' : 'her';
    @endphp

    <div class="print-area">
        <div class="header">
            <div class="header-logos">
                <img src="{{ asset('images/hrlogo.png') }}" alt="HR Logo" />
                <img src="{{ asset('images/phrmo-logo.png') }}" alt="PHRMO Logo" />
                <img src="{{ asset('images/bp-logo.png') }}" alt="BP Logo" />
            </div>
            <p>Republic of the Philippines</p>
            <p class="agency">PROVINCE OF BUKIDNON</p>
            <p>Provincial Capitol</p>
            <div class="office-title">PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</div>
        </div>

        <div class="title">C E R T I F I C A T I O N</div>

        <div class="body-para">
            TO WHOM IT MAY CONCERN:
        </div>

        <div class="body-para indent">
            <strong>THIS IS TO CERTIFY</strong> that per records of this office, 
            <strong><span class="u">{{ $prefix }} {{ $displayName }}</span></strong>, <span class="u">{{ $displayPosition }}</span> of the <span class="u">{{ $displayOffice }}</span> 
            has the following leave credits as of <span class="u">{{ $asOfFormatted }}</span>:
        </div>

        <table>
            <tr>
                <td>Vacation Leave</td>
                <td>-</td>
                <td class="col-val">{{ number_format($vl, 3) }}</td>
            </tr>
            <tr>
                <td>Sick Leave</td>
                <td>-</td>
                <td class="col-val">{{ number_format($sl, 3) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>-</strong></td>
                <td class="col-val"><strong>{{ number_format($total, 3) }}</strong></td>
            </tr>
        </table>

        <div class="body-para indent">
            Moreover, the above-mentioned date indicates {{ $pronoun }} last day of service with the Provincial Government of Bukidnon.
        </div>

        <div class="body-para indent">
            This certification is issued to support {{ $pronoun }} {{ $purpose }}.
        </div>

        <div class="body-para indent" style="margin-top: 30px;">
            Given this <span class="u">{{ now()->format('jS') }}</span> day of <span class="u">{{ now()->format('F Y') }}</span> at the Provincial Capitol, Malaybalay City, Bukidnon.
        </div>

        <div class="sig-grid">
            <div class="sig-block">
                <span class="sig-name">{{ strtoupper($certifierName) }}</span>
                <span class="sig-title">{!! nl2br(e($certifierPosition)) !!}</span>
            </div>
        </div>
    </div>

   {{-- Reference Number --}}
@if($record->reference_no_lb || $record->ref_number)
<div style="font-size: 10pt; color: #555; margin-top: 350px; margin-bottom: 10px;">
    Reference No.: <strong>{{ $record->reference_no_lb ?? $record->ref_number }}</strong>
</div>
@endif
