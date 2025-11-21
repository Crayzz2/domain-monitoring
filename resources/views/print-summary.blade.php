<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resumo {{\App\Models\Configuration::first()?->summary_default_interval_days ?? 90}} dias</title>
    <style>
        /* CSS simples compatível com DomPDF */
        html, body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 18px;
            margin: 6px 0 4px 0;
            color: #0b63b8;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px 0;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
        }

        .report {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 12px;
        }

        thead th {
            background: #f3f6fb;
            color: #0f172a;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
        }

        tbody td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            font-size: 11.5px;
        }

        .nowrap { white-space: nowrap; }

        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 8px;
        }
        .logo-container {
             width: 100%;
             display: table;
         }

        .logo-cell {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 50%;
        }

        .logo {
            max-height: 70px;
        }

        .company-name {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            width: 50%;
            color: black;
            font-size: 24px;
            font-weight: bold;
            padding-right: 10px;
        }
    </style>
</head>
<body>
<header>
    <div class="logo-container">
        <div class="company-name">
            {{ \App\Models\Configuration::first()?->company_name }}
        </div>
        @if($logo)
            <div class="logo-cell">
                <img class="logo" src="{{$logo}}">
            </div>
       @endif
    </div>
    <h1>Resumo dos próximos {{App\Models\Configuration::first()?->summary_default_interval_days ?? 90}} dias</h1>
</header>

<div class="report">
    <h2>Domínios</h2>
    <table role="table" aria-label="Domínios">
        <thead>
        <tr>
            <th>Domínio</th>
            <th class="nowrap">Data de Expiração</th>
            <th>Cliente</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Models\Domain::where('expiration_date', '<', now('America/Sao_Paulo')->addDays((integer)App\Models\Configuration::first()?->summary_default_interval_days ?? 90))->orderBy('expiration_date')->get() as $domain)
            <tr>
                <td style="{{$domain->expiration_date < now('America/Sao_Paulo')->format('Y-m-d') ? "color: red;" : "color: black;"}}">{{ $domain->name }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($domain->expiration_date)->format('d/m/Y') }}</td>
                <td>{{ $domain->client_id ? $domain->client->name : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Hospedagens</h2>
    <table role="table" aria-label="Hospedagens">
        <thead>
        <tr>
            <th>Cliente</th>
            <th class="nowrap">Data de Expiração</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Models\Hosting::where('expiration_date', '<', now('America/Sao_Paulo')->addDays((integer)App\Models\Configuration::first()?->summary_default_interval_days ?? 90))->orderBy('expiration_date')->get() as $hosting)
            <tr>
                <td style="{{$hosting->expiration_date < now('America/Sao_Paulo')->format('Y-m-d') ? "color: red;" : "color: black;"}}">{{ $hosting->client_id ? $hosting->client->name : '' }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($hosting->expiration_date)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="meta">
        Gerado em: {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }} — Relatório de vencimentos nos próximos {{App\Models\Configuration::first()?->summary_default_interval_days ?? 90}} dias.
    </div>
</div>
</body>
</html>
