# Tecno Gym - Sistema de Gestión de Gimnasio

## 📋 Estado Actual del Proyecto

**Versión:** 1.0 - Fase de Implementación Avanzada  
**Última Actualización:** 26 de Noviembre 2025  
**Stack:** Laravel 11 + Vue 3 + Inertia.js + PostgreSQL + Stripe

---

## ✅ MÓDULOS COMPLETADOS (8/8)

### 1. **Usuarios** ✅
- CRUD completo (crear, leer, actualizar, eliminar)
- Autenticación con Breeze
- Roles y permisos básicos

### 2. **Membresías** ✅
- CRUD completo
- Tipos: Mensual, Trimestral, Semestral, Anual
- Precios configurables
- Duración en días

### 3. **Suscripciones** ✅
- CRUD completo
- Estados: Inactiva, Activa, Cancelada
- Tipos de pago: Al contado (contado) y A crédito (credito)
- Fechas de inicio y fin automáticas
- Cálculo de estado de pago basado en cuotas

### 4. **Pagos** ✅
- CRUD completo
- Dos tipos:
  - **Al Contado:** Pago único inmediato
  - **A Crédito:** Plan de pagos en cuotas
- Métodos: Efectivo, Tarjeta, Stripe
- Integración con Stripe (checkout, webhooks)
- Pagos parciales soportados

### 5. **Pagos a Crédito - Cuotas** ✅ (NUEVO)
- Tabla `plan_pagos`: Almacena planes de pago
- Tabla `cuotas_pago`: Almacena cuotas individuales
- Pago individual de cada cuota
- Estados: Pendiente, Pagado, Pagado Parcial, Vencido
- Activación automática de suscripción cuando todas las cuotas están pagadas
- Formulario de pago con monto fijo y fecha formateada

### 6. **Rutinas** ✅
- CRUD completo
- Nombre, descripción, duración
- Asociadas a usuarios

### 7. **Seguimientos** ✅
- CRUD completo
- Registro de progreso
- Notas y observaciones

### 8. **Paquetes** ✅
- CRUD completo
- Paquetes de servicios adicionales
- Precios y descripciones

### 9. **Ventas de Paquetes** ✅
- CRUD completo
- Vinculación con usuarios
- Registro de compras

---

## 🔄 FLUJO COMPLETO POR ROL

### **ROL: ADMINISTRADOR**

#### 1. Gestión de Usuarios
```
Dashboard → Usuarios → Crear/Editar/Eliminar
├─ Nombre, Email, Teléfono
├─ Fecha de nacimiento
├─ Dirección
└─ Estado (Activo/Inactivo)
```

#### 2. Gestión de Membresías
```
Dashboard → Membresías → Crear/Editar/Eliminar
├─ Nombre (Mensual, Trimestral, etc.)
├─ Precio
├─ Duración en días
└─ Descripción
```

#### 3. Gestión de Suscripciones
```
Dashboard → Suscripciones → Crear/Editar/Ver
├─ Seleccionar Usuario
├─ Seleccionar Membresía
├─ Fecha de Inicio
├─ Tipo de Pago:
│  ├─ AL CONTADO
│  │  └─ Pago único inmediato
│  └─ A CRÉDITO
│     ├─ Cantidad de cuotas (1-12)
│     ├─ Fechas de vencimiento (auto-generadas, editables)
│     └─ Montos por cuota (auto-calculados, editables)
├─ Estado: Inactiva (por defecto) → Activa (al pagar)
└─ Mostrar: Progreso de pago, montos pagados/pendientes
```

#### 4. Gestión de Pagos
```
Dashboard → Pagos → Crear/Ver
├─ PAGOS AL CONTADO
│  ├─ Seleccionar Suscripción
│  ├─ Monto Total (pre-llenado)
│  ├─ Monto Abonado (editable)
│  ├─ Método: Efectivo/Tarjeta/Stripe
│  └─ Resultado: Suscripción Activa
│
└─ PAGOS A CRÉDITO (Cuotas)
   ├─ Ver Plan de Pagos
   ├─ Tabla de Cuotas:
   │  ├─ Cuota #1, #2, #3...
   │  ├─ Monto de cada cuota
   │  ├─ Fecha de vencimiento
   │  ├─ Estado (Pendiente/Pagada)
   │  └─ Botón "Pagar" (solo si Pendiente)
   │
   └─ Al hacer clic "Pagar":
      ├─ Formulario de Pago:
      │  ├─ Monto: Fijo (no editable)
      │  ├─ Fecha Vencimiento: Formateada (DD-MM-YYYY)
      │  ├─ Método de Pago: Efectivo/Tarjeta
      │  └─ Observaciones: Opcional
      └─ Resultado: Cuota marcada como Pagada
         └─ Si todas pagadas → Suscripción Activa
```

#### 5. Gestión de Rutinas
```
Dashboard → Rutinas → Crear/Editar/Ver
├─ Nombre
├─ Descripción
├─ Duración (minutos)
└─ Asociar a Usuario
```

#### 6. Gestión de Paquetes
```
Dashboard → Paquetes → Crear/Editar/Ver
├─ Nombre
├─ Descripción
├─ Precio
└─ Cantidad disponible
```

#### 7. Gestión de Ventas de Paquetes
```
Dashboard → Ventas Paquetes → Crear/Ver
├─ Seleccionar Usuario
├─ Seleccionar Paquete
├─ Cantidad
├─ Precio Total (auto-calculado)
└─ Fecha de Venta
```

---

### **ROL: CLIENTE**

#### 1. Ver Mi Suscripción
```
Dashboard → Mi Suscripción
├─ Membresía actual
├─ Fecha de inicio y fin
├─ Estado (Activa/Inactiva)
├─ Progreso de Pago:
│  ├─ Monto Total
│  ├─ Monto Pagado
│  ├─ Monto Pendiente
│  └─ Porcentaje pagado (barra)
│
└─ Si es A CRÉDITO:
   ├─ Tabla de Cuotas
   ├─ Próxima cuota a vencer
   └─ Historial de pagos
```

#### 2. Ver Mis Rutinas
```
Dashboard → Mis Rutinas
├─ Rutinas asignadas
├─ Descripción
├─ Duración
└─ Historial de sesiones
```

#### 3. Ver Mi Historial de Pagos
```
Dashboard → Historial Pagos
├─ Fecha de pago
├─ Monto pagado
├─ Método de pago
├─ Estado
└─ Recibo/Comprobante
```

---

## 📊 ESTRUCTURA DE DATOS

### Tablas Principales

#### `usuarios`
```
id, nombre, email, password, telefono, fecha_nacimiento, 
direccion, estado, created_at, updated_at
```

#### `membresia`
```
id, nombre, precio, duracion_dias, descripcion, 
estado, created_at, updated_at
```

#### `suscripcion`
```
id, usuario_id, membresia_id, fecha_inicio, fecha_fin,
estado (inactiva/activa/cancelada), estado_pago (boolean),
tipo_pago (contado/credito), created_at, updated_at
```

#### `pagos`
```
id, suscripcion_id, monto_abonado, monto_total_membresia,
metodo_pago (efectivo/tarjeta/stripe), estado_pago (boolean),
stripe_payment_id, stripe_session_id, stripe_status,
fecha_abono, observaciones, created_at, updated_at
```

#### `plan_pagos` (NUEVO)
```
id, suscripcion_id, monto_total, cantidad_cuotas,
estado (activo/completado/cancelado), fecha_inicio,
created_at, updated_at
```

#### `cuotas_pago` (NUEVO)
```
id, plan_pago_id, numero_cuota, monto, fecha_vencimiento,
fecha_pago, estado (pendiente/pagado/pagado_parcial/vencido),
metodo_pago, created_at, updated_at
```

#### `rutinas`
```
id, usuario_id, nombre, descripcion, duracion_minutos,
estado, created_at, updated_at
```

#### `seguimientos`
```
id, usuario_id, rutina_id, fecha, notas, progreso,
created_at, updated_at
```

#### `paquetes`
```
id, nombre, descripcion, precio, cantidad_disponible,
estado, created_at, updated_at
```

#### `venta_paquete`
```
id, usuario_id, paquete_id, cantidad, precio_total,
fecha_venta, estado, created_at, updated_at
```

---

## 🚀 PRÓXIMOS PASOS - IMPLEMENTACIÓN PENDIENTE

### **FASE 3: Ejercicios, Rutina Sesiones y Asistencia** (EN PROGRESO)

#### 1. **Ejercicios** (PENDIENTE)
```
Tabla: ejercicios
├─ id, nombre, descripcion, grupo_muscular, 
│  dificultad, imagen_url, video_url, estado
│
Funcionalidad:
├─ CRUD completo
├─ Categorías por grupo muscular
├─ Niveles de dificultad (Principiante, Intermedio, Avanzado)
├─ Búsqueda y filtrado
└─ Asociar a Rutinas
```

#### 2. **Rutina Sesiones** (PENDIENTE)
```
Tabla: rutina_sesion
├─ id, rutina_id, numero_sesion, ejercicios (JSON),
│  series, repeticiones, peso, descanso, notas
│
Funcionalidad:
├─ Crear sesiones dentro de una rutina
├─ Sesión 1, 2, 3 con ejercicios diferentes
├─ Progresión: aumentar peso/repeticiones
├─ Editar ejercicios por sesión
├─ Ver historial de sesiones completadas
└─ Seguimiento de progreso
```

#### 3. **Asistencia Sesiones** (PENDIENTE)
```
Tabla: asistencia_sesion
├─ id, usuario_id, rutina_sesion_id, fecha,
│  hora_entrada, hora_salida, estado (asistio/no_asistio),
│  observaciones
│
Funcionalidad:
├─ Registrar asistencia a sesiones
├─ Hora de entrada y salida
├─ Duración de sesión
├─ Notas del entrenador
├─ Historial de asistencia
├─ Reportes de asistencia por usuario
└─ Estadísticas de asistencia (% de asistencia)
```

---

## 📝 CAMBIOS NECESARIOS

### **Base de Datos**
```
1. Crear tabla: ejercicios
2. Crear tabla: rutina_sesion
3. Crear tabla: asistencia_sesion
4. Modificar: venta_paquete (agregar columnas de asistencia si es necesario)
```

### **Backend (Laravel)**
```
1. Crear modelo: Ejercicio
2. Crear modelo: RutinaSesion
3. Crear modelo: AsistenciaSesion
4. Crear controladores: EjercicioController, RutinaSesionController, AsistenciaSesionController
5. Crear validaciones específicas
6. Crear relaciones entre modelos
```

### **Frontend (Vue 3)**
```
1. Crear vista: Ejercicios/Index.vue, Create.vue, Edit.vue, Show.vue
2. Crear vista: RutinaSesiones/Index.vue, Create.vue, Edit.vue, Show.vue
3. Crear vista: AsistenciaSesiones/Index.vue, Create.vue, Show.vue
4. Agregar componentes reutilizables
5. Agregar validaciones en formularios
```

### **Rutas**
```
1. Route::resource('ejercicios', EjercicioController::class);
2. Route::resource('rutina-sesion', RutinaSesionController::class);
3. Route::resource('asistencia-sesion', AsistenciaSesionController::class);
```

---

## 🔧 CONFIGURACIÓN ACTUAL

### **Dependencias Instaladas**
```json
{
  "php": "^8.2",
  "laravel/framework": "^11.31",
  "inertiajs/inertia-laravel": "^2.0",
  "stripe/stripe-php": "^19.0",
  "tightenco/ziggy": "^2.0"
}
```

### **Base de Datos**
- **Motor:** PostgreSQL
- **Migraciones:** Todas ejecutadas
- **Seeders:** Disponibles para datos de prueba

### **Autenticación**
- **Sistema:** Laravel Breeze
- **Middleware:** auth, verified

---

## 🎯 RESUMEN DE LO REALIZADO

### **Sesión Actual (26 Nov 2025)**

#### ✅ Problemas Solucionados
1. **Cuotas no se guardaban en BD**
   - Problema: Arrays de fechas/montos no se enviaban correctamente desde Vue
   - Solución: Implementar `form.transform()` en frontend y validación mejorada en backend

2. **Estado de pago incorrecto para crédito**
   - Problema: Mostraba "Pagado" aunque no se hubiera pagado nada
   - Solución: Mejorar lógica en modelo `Suscripcion::obtenerEstadoPago()`

3. **Pago de cuotas individuales**
   - Problema: No había forma de pagar cada cuota
   - Solución: Crear `CuotaPagoController` con métodos `create()` y `store()`

4. **Rutas de pago de cuotas**
   - Problema: Model binding no funcionaba correctamente
   - Solución: Agregar rutas personalizadas con parámetro explícito

5. **Formato de fechas**
   - Problema: Fechas mostraban formato ISO (2025-12-03T00:00:00.000000Z)
   - Solución: Crear función `formatFecha()` que convierte a DD-MM-YYYY

6. **Monto editable en pago de cuotas**
   - Problema: Usuario podía cambiar el monto
   - Solución: Hacer campo `readonly` y `disabled`

#### 📊 Estadísticas
- **Módulos completados:** 8/8
- **Tablas creadas:** 10
- **Controladores:** 9
- **Vistas Vue:** 25+
- **Rutas:** 50+

---

## 📋 CHECKLIST FINAL

### Antes de Migración a Producción
- [ ] Implementar Ejercicios (CRUD)
- [ ] Implementar Rutina Sesiones (CRUD)
- [ ] Implementar Asistencia Sesiones (CRUD)
- [ ] Ejecutar: `php artisan migrate:fresh --seed`
- [ ] Pruebas de flujo completo
- [ ] Validación de datos
- [ ] Pruebas de Stripe
- [ ] Documentación de API
- [ ] Pruebas de rendimiento
- [ ] Backup de base de datos

---

## 🚀 CÓMO EJECUTAR

### Instalación
```bash
composer install
npm install
npm run build
php artisan migrate
php artisan seed
```

### Desarrollo
```bash
php artisan serve
npm run dev
```

### Producción
```bash
npm run build
php artisan migrate:fresh --seed
```

---

## 📞 NOTAS IMPORTANTES

1. **Stripe:** Configurar variables de entorno (STRIPE_PUBLIC_KEY, STRIPE_SECRET_KEY)
2. **Base de Datos:** Usar PostgreSQL para mejor rendimiento
3. **Seguridad:** Todas las rutas están protegidas con middleware `auth`
4. **Validación:** Implementada en frontend y backend
5. **Errores:** Sistema de logging completo en `storage/logs/laravel.log`

---

## 📅 Próxima Sesión

**Objetivo:** Implementar Ejercicios, Rutina Sesiones y Asistencia Sesiones

**Tiempo estimado:** 4-5 horas

**Pasos:**
1. Crear migraciones para las 3 nuevas tablas
2. Crear modelos y relaciones
3. Crear controladores con CRUD
4. Crear vistas Vue
5. Agregar rutas
6. Pruebas completas

---

**Última actualización:** 26 de Noviembre 2025  
**Versión:** 1.0 - Fase Avanzada
