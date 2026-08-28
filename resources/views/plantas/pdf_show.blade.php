<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $planta->name }} - PDF</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 6px;
        }

        .header h2 {
            margin: 0 0 4px 0;
            font-size: 18px;
            color: #0d6efd;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        /* Container da Imagem Compilada */
        .map-container {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #cbd5e1;
        }

        .map-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Caixa de aviso com Link Público */
        .public-link-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 11px;
            color: #0369a1;
        }

        .public-link-box a {
            color: #0284c7;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Tabela Detalhada */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .room-header {
            background-color: #e2e8f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 9px;
            color: #fff;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $planta->name }}</h2>
        <p>Prédio: {{ optional($planta->predio)->nome ?? 'N/A' }}</p>
    </div>

    <!-- Planta Convertida com Marcadores Injetados -->
    @if($pngBase64)
    <div class="map-container">
        <img src="{{ $pngBase64 }}" alt="{{ $planta->name }}">
    </div>
    @endif

    <!-- Aviso com Link Público -->
    <div class="public-link-box">
        <strong>Atenção:</strong> Acesse a versão interativa e detalhada desta planta em:
        <br>
        <a href="{{ $publicUrl }}" target="_blank">{{ $publicUrl }}</a>
    </div>

    <!-- Tabela com Mapeamento ID -> Ponto -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">ID</th>
                <th>Ponto (Rack - Patch - Porta)</th>
                <th>Sala</th>
                <th>Tipo</th>
                <th class="text-end">Comprimento</th>
            </tr>
        </thead>
        <tbody>
            @forelse($markers->groupBy(fn($item) => optional($item->sala)->nome ?? 'Sem Sala Definida') as $nomeSala => $pontosDaSala)
                <tr class="room-header">
                    <td colspan="5" style="text-align: center;">
                        {{ $nomeSala }}
                        @php $salaObj = $pontosDaSala->first()?->sala; @endphp
                        @if($salaObj && !empty($salaObj->descricao))
                            <span style="font-weight: normal; font-size: 10px; text-transform: none;"> — {{ $salaObj->descricao }}</span>
                        @endif
                    </td>
                </tr>

                @foreach($pontosDaSala as $ponto)
                    @php
                        $nomePonto = optional(optional($ponto->patchPanel)->rack)->nome . '-' . optional($ponto->patchPanel)->nome . '-' . $ponto->porta;
                        $corTipo = optional($ponto->tipoPorta)->cor ?? '#ef4444';
                    @endphp
                    <tr>
                        <td class="text-center fw-bold">{{ $ponto->id }}</td>
                        <td class="fw-bold">{{ $nomePonto }}</td>
                        <td>{{ optional($ponto->sala)->nome ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background-color: {{ $corTipo }};">
                                {{ optional($ponto->tipoPorta)->nome ?? 'Não definido' }}
                            </span>
                        </td>
                        <td class="text-end">
                            {{ $ponto->tamanho ? number_format($ponto->tamanho, 2, ',', '.') . ' m' : '-' }}
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum ponto visível nesta planta.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #333; color: #fff;">
                <td colspan="2" class="fw-bold">
                    Total de Pontos: {{ $markers->count() }}
                </td>
                <td colspan="2" class="text-end fw-bold">Comprimento Total da Planta:</td>
                <td class="text-end fw-bold">
                    {{ number_format($markers->sum('tamanho'), 2, ',', '.') }} m
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>