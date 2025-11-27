# Test Callback PagoFácil
$url = "https://ffac33a097a2.ngrok-free.app/pagos/pagofacil/callback"

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "   TEST CALLBACK PAGOFÁCIL" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "URL: $url" -ForegroundColor Yellow
Write-Host ""

# Simular callback de pago completado
$body = @{
    PedidoID = "SUS-21-1732694400"
    Fecha = (Get-Date -Format "yyyy-MM-dd")
    Hora = (Get-Date -Format "HH:mm:ss")
    MetodoPago = "QR"
    Estado = "completado"
} | ConvertTo-Json

Write-Host "Datos del callback:" -ForegroundColor Cyan
Write-Host $body -ForegroundColor Gray
Write-Host ""

try {
    Write-Host "Enviando callback..." -ForegroundColor Yellow
    
    $response = Invoke-WebRequest -Uri $url `
        -Method POST `
        -ContentType "application/json" `
        -Body $body `
        -UseBasicParsing

    Write-Host ""
    Write-Host "✅ CALLBACK EXITOSO!" -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Green
    Write-Host "Status Code: $($response.StatusCode)" -ForegroundColor Green
    Write-Host ""
    Write-Host "Response:" -ForegroundColor Cyan
    Write-Host $response.Content -ForegroundColor White
    Write-Host ""
}
catch {
    Write-Host ""
    Write-Host "❌ ERROR AL ENVIAR CALLBACK" -ForegroundColor Red
    Write-Host "==================================================" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
}

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Revisa los logs en: storage/logs/laravel.log" -ForegroundColor Yellow
Write-Host "Comando: Get-Content storage\logs\laravel.log -Tail 50" -ForegroundColor Gray
Write-Host "==================================================" -ForegroundColor Cyan
