<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual - MAROMBA Training Center</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            color: #1a1a1a;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f4f4f4;
            padding: 8px;
            border-left: 4px solid #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            width: 25%;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            <div class="summary-item"><div class="summary-value">{{ $resumen['totalSuscripciones'] }}</div><div class="summary-label">Nuevas Suscripciones</div></div><div class="summary-item"><div class="summary-value">{{ $resumen['totalPaquetesVendidos'] }}</div><div class="summary-label">Paquetes Vendidos</div></div><div class="summary-item"><div class="summary-value">${{ number_format($resumen['ingresosSuscripciones'], 2) }}</div><div class="summary-label">Ingresos Suscripciones</div></div><div class="summary-item"><div class="summary-value">${{ number_format($resumen['ingresosPaquetes'], 2) }}</div><div class="summary-label">Ingresos Paquetes</div></div></div></div></div>< !-- Membresías Más Vendidas --><div class="section"><div class="section-title">Membresías Más Vendidas</div><table><thead><tr><th>Membresía</th><th class="text-center">Cantidad Vendida</th><th class="text-right">Ingresos Generados</th></tr></thead><tbody>
            @forelse($membresiasMasVendidas as $item)
            <tr><td>{{ $item->nombre }}</td><td class="text-center">{{ $item->total }}</td><td class="text-right">${{ number_format($item->ingresos, 2) }}</td></tr>@empty <tr><td colspan="3" class="text-center">No hay datos disponibles</td></tr>@endforelse </tbody></table></div>< !-- Detalle de Suscripciones --><div class="section"><div class="section-title">Detalle de Nuevas Suscripciones</div><table><thead><tr><th>Fecha</th><th>Usuario</th><th>Membresía</th><th class="text-right">Monto</th></tr></thead><tbody>
            @forelse($usuariosSuscritos as $suscripcion)
                <tr><td>{{ \Carbon\Carbon::parse($suscripcion->fecha_inicio)->format('d/m/Y') }}</td><td>
                {{ $suscripcion->nombre }}
            <br><small style="color: #666;">{{ $suscripcion->email }}</small></td><td>{{ $suscripcion->membresia }}</td><td class="text-right">${{ number_format($suscripcion->precio, 2) }}</td></tr>@empty <tr><td colspan="4" class="text-center">No hay suscripciones registradas este mes</td></tr>@endforelse </tbody></table></div>< !-- Detalle de Paquetes --><div class="section"><div class="section-title">Detalle de Paquetes Vendidos</div><table><thead><tr><th>Fecha</th><th>Usuario</th><th>Paquete</th><th class="text-right">Monto</th></tr></thead><tbody>
            @forelse($paquetesAdquiridos as $venta)
            <tr><td>{{ \Carbon\Carbon::parse($venta->fecha_compra)->format('d/m/Y') }}</td><td>{{ $venta->usuario }}</td><td>{{ $venta->paquete }}</td><td class="text-right">${{ number_format($venta->precio, 2) }}</td></tr>@empty <tr><td colspan="4" class="text-center">No hay paquetes vendidos este mes</td></tr>@endforelse </tbody></table></div><div class="footer"><p>MAROMBA Training Center - Reporte generado automáticamente por el sistema.</p></div></body></html>