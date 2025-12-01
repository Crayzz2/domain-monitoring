<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>
        @switch($type)
            @case("domain")
                Resumo dos domínios vencidos
                @break
            @case("hosting")
                Resumo das hospedagens vencidas
                @break
            @default
                Resumo de vencidos
        @endswitch
    </title>
    <style>
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
    @switch($type)
        @case("domain")
            <h1>Resumo dos domínios vencidos</h1>
            @break
        @case("hosting")
            <h1>Resumo das hospedagens vencidas</h1>
            @break
        @default
        <h1>Resumo de todos os vencidos</h1>
    @endswitch
</header>

<div class="report">
    @php
        $domains = \App\Models\Domain::where('expiration_date', '<', now('America/Sao_Paulo'))->orderBy('expiration_date')->get();
        $hostings = \App\Models\Hosting::where('expiration_date', '<', now('America/Sao_Paulo'))->orderBy('expiration_date')->get();
    @endphp
    @if($type=="all" || $type=="domain")
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
            @foreach($domains as $domain)
                <tr>
                    <td style="{{$domain->expiration_date < now('America/Sao_Paulo')->format('Y-m-d') ? "color: red;" : "color: black;"}}">{{ $domain->name }}</td>
                    <td class="nowrap">{{ \Carbon\Carbon::parse($domain->expiration_date)->format('d/m/Y') }}</td>
                    <td>{{ $domain->client_id ? $domain->client->name : '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if($type=="all" || $type=="hosting")
        <h2>Hospedagens</h2>
        <table role="table" aria-label="Hospedagens">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th class="nowrap">Data de Expiração</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hostings as $hosting)
                    <tr>
                        <td style="{{$hosting->expiration_date < now('America/Sao_Paulo')->format('Y-m-d') ? "color: red;" : "color: black;"}}">{{ $hosting->client_id ? $hosting->client->name : '' }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($hosting->expiration_date)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="meta">
        Gerado em: {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }} —

        @switch($type)
            @case("domain")
                Relatório de domínios vencidos
                @break
            @case("hosting")
                Relatório de hospedagens vencidas
                @break
            @default
                Relatório de todos os vencidos
            @endswitch
        .
    </div>
</div>
</body>
</html>
