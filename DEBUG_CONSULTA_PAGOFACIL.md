# 🔍 Debugging: Consulta de PagoFácil No Funciona

## ❌ Problema Identificado

La consulta de estado de PagoFácil está fallando. Los datos se envían correctamente:
```
{
  transactionId: 6752999,
  pagoId: 31,
  suscripcionId: 21
}
```

Pero la consulta no se completa.

---

## 🔎 Causas Posibles

### **1. Tokens de PagoFácil Vacíos** ⚠️ (MÁS PROBABLE)

En tu `.env` tienes:
```env
PAGOFACIL_TOKEN_SERVICE=
PAGOFACIL_TOKEN_SECRET=
```

**Están vacíos**. Sin estos tokens, el sistema no puede autenticarse con PagoFácil.

### **2. Error de Red**
- Timeout de conexión
- Firewall bloqueando la solicitud
- ngrok no está corriendo

### **3. Error en la API de PagoFácil**
- El ambiente sandbox no está disponible
- El transactionId no existe en PagoFácil

---

## ✅ Soluciones

### **Solución 1: Obtener Tokens de PagoFácil** (RECOMENDADO)

1. **Contacta a PagoFácil** para obtener:
   - `PAGOFACIL_TOKEN_SERVICE`
   - `PAGOFACIL_TOKEN_SECRET`

2. **Agrégalos a tu `.env`**:
   ```env
   PAGOFACIL_TOKEN_SERVICE=tu_token_service_aqui
   PAGOFACIL_TOKEN_SECRET=tu_token_secret_aqui
   ```

3. **Limpia la caché**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Prueba nuevamente**

---

### **Solución 2: Verificar Logs** (DEBUGGING)

Para ver exactamente qué está fallando:

1. **Abre una terminal** y ejecuta:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Intenta hacer la consulta** nuevamente

3. **Busca en los logs**:
   - `"Fallo al obtener token"` → Problema con tokens
   - `"Error en la transacción"` → Problema con PagoFácil
   - `"Excepción crítica"` → Error de código

---

### **Solución 3: Verificar en el Navegador** (DEBUGGING)

1. **Abre DevTools** (F12)
2. **Ve a la pestaña Network**
3. **Haz clic en "Verificar Estado del Pago Ahora"**
4. **Busca la solicitud** `pagofacil/consultar`
5. **Revisa**:
   - **Status Code**: ¿200, 500, 422?
   - **Response**: ¿Qué dice la respuesta?
   - **Request Payload**: ¿Se enviaron los datos correctos?

---

## 🧪 Prueba Rápida

### **Verificar si los tokens están configurados**

Ejecuta en tu terminal:

```bash
php artisan tinker
```

Luego:

```php
config('pagofacil.token_service')
config('pagofacil.token_secret')
```

Si devuelven `null` o vacío → **Los tokens no están configurados**

---

## 📊 Estados de Respuesta Esperados

### **Si todo funciona bien:**

```json
{
  "error": 0,
  "status": 2008,
  "message": "Consulta realizada.",
  "values": {
    "pagofacilTransactionId": 6752999,
    "paymentStatus": 0,  // 0=Pendiente, 2=Completado
    "paymentDate": null,
    "paymentTime": null
  }
}
```

### **Si falta autenticación:**

```json
{
  "error": 1,
  "status": 401,
  "message": "No autorizado"
}
```

### **Si el transactionId no existe:**

```json
{
  "error": 1,
  "status": 404,
  "message": "Transacción no encontrada"
}
```

---

## 🔧 Solución Temporal (Sin Tokens)

Si no tienes los tokens aún, puedes **simular** la verificación:

### **Opción A: Usar el Callback Manual**

En lugar de esperar la verificación automática, puedes simular un callback de PagoFácil usando Postman:

```
POST http://tu-dominio.com/pagos/pagofacil/callback

Body (JSON):
{
  "PedidoID": "SUS-31-1700000000",
  "Fecha": "2025-11-27",
  "Hora": "14:30:00",
  "MetodoPago": "QR",
  "Estado": "completado"
}
```

### **Opción B: Actualizar Manualmente en la Base de Datos**

```sql
UPDATE pagos 
SET estado_pago = 1, 
    fecha_abono = NOW() 
WHERE id = 31;

UPDATE suscripcion 
SET estado_pago = 1 
WHERE id = 21;
```

---

## 📝 Checklist de Verificación

- [ ] Tokens de PagoFácil configurados en `.env`
- [ ] Caché limpiada (`php artisan config:clear`)
- [ ] Logs revisados (`tail -f storage/logs/laravel.log`)
- [ ] Network tab revisada en DevTools
- [ ] ngrok corriendo (si estás en desarrollo local)
- [ ] URL de callback correcta en `.env`

---

## 🎯 Próximos Pasos

1. **Obtén los tokens de PagoFácil** (contacta a su soporte)
2. **Agrégalos al `.env`**
3. **Limpia la caché**
4. **Prueba nuevamente**

Si no puedes obtener los tokens ahora, usa la **Solución Temporal** para probar el resto del flujo.

---

## 📞 Contacto PagoFácil

Para obtener los tokens, contacta a:
- **Email**: soporte@pagofacil.com.bo
- **Teléfono**: (Verifica en su sitio web)
- **Documentación**: https://pagofacil.com.bo/developers

Solicita:
- Token Service (tcTokenService)
- Token Secret (tcTokenSecret)
- Para ambiente: **Sandbox** (desarrollo) o **Producción**
