<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Talonarios · {{ $selectedPrize?->name ?? $draw->title }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: 80mm auto; margin: 0; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #000; background: #eee; }
        .toolbar { position: sticky; top: 0; display: flex; justify-content: center; gap: 10px; padding: 14px; background: #fff; border-bottom: 1px solid #999; }
        .toolbar button, .toolbar a { border: 1px solid #000; background: #fff; color: #000; padding: 9px 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .ticket { width: 80mm; min-height: 78mm; margin: 14px auto; padding: 7mm 6mm; background: #fff; border: 1px solid #000; page-break-after: always; break-after: page; }
        .brand { font-size: 10pt; font-weight: 700; letter-spacing: 1px; text-align: center; }
        .title { margin: 5px 0 2px; font-size: 16pt; font-weight: 800; text-align: center; text-transform: uppercase; }
        .approved { margin: 8px 0; padding: 5px; border: 2px solid #000; font-size: 10pt; font-weight: 800; text-align: center; letter-spacing: 1px; }
        .number { margin: 10px 0; padding: 9px 4px; border-top: 2px dashed #000; border-bottom: 2px dashed #000; font-size: 29pt; font-weight: 800; text-align: center; }
        .label { margin-top: 10px; font-size: 7.5pt; font-weight: 700; letter-spacing: .8px; }
        .value { margin-top: 2px; font-size: 11pt; font-weight: 700; }
        .footer { margin-top: 15px; padding-top: 8px; border-top: 1px dashed #000; font-size: 8pt; text-align: center; line-height: 1.4; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .ticket { margin: 0; border: 0; width: 80mm; min-height: 78mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Imprimir {{ $tickets->count() }} talonario(s){{ $selectedPrize ? ' · '.$selectedPrize->name : '' }}</button>
        <a href="{{ route('draws.show', $draw) }}">Volver al sorteo</a>
    </div>
    @forelse($tickets as $ticket)
        <article class="ticket">
            <div class="brand">SISTEMA DE SORTEOS</div>
            <div class="title">{{ $draw->title }}</div>
            <div class="approved">TICKET APROBADO</div>
            <div class="number">N.º {{ $ticket->ticket_number }}</div>
            <div class="label">PREMIO</div>
            <div class="value">{{ $ticket->prize?->name ?? 'Premio' }}</div>
            <div class="footer">Talonario para ánfora · Válido únicamente para el premio indicado.</div>
        </article>
    @empty
        <p style="text-align:center;padding:32px;">No hay tickets aprobados para imprimir en este sorteo.</p>
    @endforelse
</body>
</html>
