<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    protected $baseUrl;
    protected $tokenService;
    protected $tokenSecret;

    public function __construct()
    {
        $this->baseUrl = config('pagofacil.base_url');
        $this->tokenService = config('pagofacil.token_service');
        $this->tokenSecret = config('pagofacil.token_secret');
    }

    /**
     * Obtener token de autenticación de PagoFácil
     * Según documentación v2: POST /login con headers tcTokenService y tcTokenSecret
     */
    public function obtenerToken()
    {
        try {
            $client = new Client();

            $response = $client->post($this->baseUrl . '/login', [
                'headers' => [
                    'Accept' => 'application/json',
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret' => $this->tokenSecret
                ],
                'timeout' => config('pagofacil.timeout', 30)
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (config('pagofacil.enable_logs')) {
                Log::info('Token obtenido de PagoFácil', ['response' => $result]);
            }

            return $result;
        } catch (\Exception $e) {
            if (config('pagofacil.enable_logs')) {
                Log::error('Error al obtener token de PagoFácil', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            throw new \Exception("Error al obtener el token: " . $e->getMessage());
        }
    }

    /**
     * Generar QR para pago
     * 
     * @param object $usuario Usuario que realiza el pago
     * @param float $monto Monto a pagar
     * @param string $pedidoId ID único del pedido
     * @param string $callbackUrl URL para recibir notificaciones
     * @param array $detalles Array de detalles del pedido
     * @return array Respuesta con qrBase64, transactionId, etc.
     */
    public function generarQr($usuario, $monto, $pedidoId, $callbackUrl, $detalles = [])
    {
        try {
            Log::info('Inicio de generarQr en PagoFacilService', [
                'usuario_id' => $usuario->id,
                'monto' => $monto,
                'pedido_id' => $pedidoId
            ]);

            // Obtener token de autenticación
            $tokenResponse = $this->obtenerToken();

            if (!isset($tokenResponse['values']['accessToken'])) {
                Log::error('No se pudo obtener un token válido', ['response' => $tokenResponse]);
                throw new \Exception('No se pudo obtener un token válido');
            }

            $accessToken = $tokenResponse['values']['accessToken'];
            Log::info('Access token extraído correctamente');

            // Si no se proporcionan detalles, crear uno por defecto
            if (empty($detalles)) {
                $detalles = [
                    [
                        'serial' => 1,
                        'product' => 'Pago de servicio',
                        'quantity' => 1,
                        'price' => $monto,
                        'discount' => 0,
                        'total' => $monto
                    ]
                ];
            }

            $body = [
                "paymentMethod" => 4, // 4 = QR según ejemplos actualizados
                "clientName" => $usuario->nombre ?? $usuario->name ?? 'Cliente',
                "documentType" => 1, // 1 = CI
                "documentId" => (string) ($usuario->ci_nit ?? "0"),
                "phoneNumber" => (string) ($usuario->telefono ?? "0"),
                "email" => $usuario->email,
                "paymentNumber" => $pedidoId,
                "amount" => (float) $monto,
                "currency" => 2, // 2 = BOB
                "clientCode" => (string) $usuario->id,
                "callbackUrl" => $callbackUrl,
                "orderDetail" => $detalles,
            ];

            Log::info('Cuerpo de la solicitud generado', ['body' => $body]);

            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ];

            $client = new Client();
            $url = $this->baseUrl . '/generate-qr';
            Log::info('Enviando solicitud a PagoFácil', ['url' => $url]);

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body
            ]);

            $responseContent = $response->getBody()->getContents();
            Log::info('Contenido crudo de la respuesta', ['response' => $responseContent]);

            $result = json_decode($responseContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error al decodificar JSON', [
                    'error_message' => json_last_error_msg(),
                    'response_content' => $responseContent
                ]);
                throw new \Exception('Error al procesar la respuesta del servicio');
            }

            if (!isset($result['values'])) {
                Log::error('El campo values no está presente en la respuesta', ['result' => $result]);
                throw new \Exception('Respuesta inesperada del servicio');
            }

            $values = $result['values'];

            Log::info('Estructura completa de values recibida', [
                'values_keys' => array_keys((array) $values),
                'values_type' => gettype($values),
                'values_content' => $values
            ]);

            $qrBase64 = $values['qrBase64'] ?? null;
            $transactionId = $values['transactionId'] ?? null;

            if (!$qrBase64 || !$transactionId) {
                Log::error('No se encontraron qrBase64 o transactionId en la respuesta', [
                    'values' => $values,
                    'qrBase64_encontrado' => !is_null($qrBase64),
                    'transactionId_encontrado' => !is_null($transactionId),
                    'todas_las_claves' => array_keys((array) $values)
                ]);
                throw new \Exception('Error al obtener los datos del QR');
            }

            Log::info('QR y transaction ID generados correctamente', [
                'qrBase64' => substr($qrBase64, 0, 50) . '...',
                'transactionId' => $transactionId
            ]);

            return $result['values'];

        } catch (\Throwable $th) {
            Log::error('Error en generarQr', [
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    /**
     * Consultar estado de una transacción
     * 
     * @param int $transaccionId ID de la transacción en PagoFácil
     * @return array|null Datos de la transacción o null si hay error
     */
    public function consultarTransaccion($transaccionId)
    {
        set_time_limit(120);

        try {
            // Obtener token con manejo de errores
            try {
                $tokenResponse = $this->obtenerToken();
            } catch (\Exception $e) {
                Log::error('Fallo al obtener token en consultarTransaccion', ['error' => $e->getMessage()]);
                return null;
            }

            if (!isset($tokenResponse['values']['accessToken'])) {
                Log::error('No se pudo autenticar con PagoFácil');
                return null;
            }

            $accessToken = $tokenResponse['values']['accessToken'];
            $client = new Client();

            // Realizar la petición con http_errors => false para evitar excepciones fatales
            $response = $client->post($this->baseUrl . '/query-transaction', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                'json' => [
                    'pagofacilTransactionId' => $transaccionId
                ],
                'http_errors' => false, // Evita que Guzzle lance excepción en 4xx/5xx
                'timeout' => 90,
                'connect_timeout' => 10
            ]);

            $responseContent = $response->getBody()->getContents();
            $result = json_decode($responseContent, true);

            Log::info('Respuesta cruda consultarTransaccion', ['content' => $result]);

            // Validar si la respuesta es válida (JSON mal formado o null)
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Respuesta inválida del proveedor', ['error' => json_last_error_msg()]);
                return null;
            }

            // Validar errores lógicos de la API
            if (isset($result['error']) && $result['error'] != 0) {
                Log::error('Error en la transacción', ['message' => $result['message'] ?? 'Error desconocido']);
                return null;
            }

            if (!isset($result['values'])) {
                Log::error('Datos no encontrados en la respuesta');
                return null;
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Excepción crítica en consultarTransaccion', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return null;
        }
    }
}
