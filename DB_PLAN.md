# Plan de Base de Datos — FutGo (Simulación v2)

**Estado general:** ✅ 16/16 tablas implementadas — BD COMPLETA  
**Motor actual:** SQLite (desarrollo local)  
**Motor futuro:** MySQL 8 (producción)  
**Referencia:** Documento Arquitectónico Maestro v2.0

---

## Por qué esta BD es DIFERENTE a la del documento

| Concepto del documento | Cómo se simplifica en simulación |
|---|---|
| Columna `POINT` geoespacial | `latitude` y `longitude` DECIMAL simples |
| Redis lock distribuido | Campo `status` + `lock_expires_at` en `slots` |
| Webhook HMAC-SHA256 | `payment_status` directo en `bookings` |
| `idempotency_key` | No implementado en simulación |
| Workers y Horizon | Todo síncrono |
| `platform_fee` en bookings | No implementado (Fase 1 = 0%) |

## Reglas que SÍ se respetan

- Montos siempre `DECIMAL(10,2)` — NUNCA float
- Timestamps en UTC
- Máquina de estados del slot: `available | pending_payment | reserved | event_occupied | completed | expired`
- Un slot solo puede pertenecer a un booking (`slot_id UNIQUE` en booking_slots)
- Precios en bookings son **snapshot inmutable** al momento de reservar
- `audit_logs` solo tiene `created_at` (sin `updated_at` — inmutable por diseño)
- Ningún partner puede ver venues ajenos (validado en rutas con `venues()->where('id',...)`)

---

## Estado de avance

| # | Fase | Tabla | Estado | Notas |
|---|------|-------|--------|-------|
| 1 | Usuarios | users | ✅ Listo | role, phone, avatar_url, city_id |
| 2 | Usuarios | password_reset_tokens | ✅ Listo | Laravel nativo |
| 3 | Ciudades | cities | ✅ Listo | is_active, lat/lng |
| 4 | Complejos | venues | ✅ Listo | city_id FK, status pending/active/suspended |
| 5 | Complejos | venue_staff | ✅ Listo | UNIQUE(venue_id, user_id) |
| 6 | Complejos | fields | ✅ Listo | sport_type, surface, amenities JSON |
| 7 | Inventario | operating_hours | ✅ Listo | price_day/night diferenciados |
| 8 | Inventario | slots | ✅ Listo | máquina de estados, lock_expires_at |
| 9 | Inventario | events | ✅ Listo | torneo/mantenimiento/evento_privado |
| 10 | Reservas | bookings | ✅ Listo | qr_token, snapshot precios |
| 11 | Reservas | booking_slots | ✅ Listo | unit_price snapshot, slot_id UNIQUE |
| 12 | Reservas | transactions | ✅ Listo | deposit/balance/refund/walkin |
| 13 | Staff | shift_logs | ✅ Listo | turno con apertura/cierre de caja |
| 14 | Staff | shift_movements | ✅ Listo | checkin/walkin/noshow por turno |
| 15 | Auditoría | audit_logs | ✅ Listo | append-only, inmutable |
| 16 | Auditoría | notifications_log | ✅ Listo | email/push/whatsapp |

---

## Falencias detectadas vs. el documento

### ❌ Faltantes críticos

1. **`shift_logs` y `shift_movements`** — El staff no tiene turno registrado en BD. La PWA muestra datos hardcodeados.
2. **`audit_logs`** — Las acciones del admin (aprobar partner, resolver disputa) no se registran. El documento exige bitácora inmutable.
3. **`notifications_log`** — No hay registro de notificaciones enviadas.
4. **`platform_fee` en bookings** — El documento exige que el campo exista desde el día 1 aunque valga 0. Permite activar monetización sin reescritura.
5. **`venue.phone` falta en algunos modelos** — ya existe en la migración pero falta en el fillable de Venue.

### ⚠️ Inconsistencias menores

6. **`users.city_id`** — FK creada en migración pero el seeder no asigna ciudades a los jugadores.
7. **`venues.cover_image`** — Campo existe pero siempre null; las vistas usan imágenes de Unsplash hardcodeadas.
8. **`bookings.is_walkin`** — Existe pero las reservas presenciales del staff no crean bookings reales.
9. **`slots` solo 14 días** — El documento exige ventana de 30 días. Actualmente generamos ±7 días.
10. **`operating_hours` no se leen en el detalle de cancha** — La vista usa los slots reales pero el formulario de horarios del partner sigue hardcodeado.

### ✅ Bien implementado

- Roles y autenticación (`role` en users, middleware auth)
- Relación partner ↔ venues (multi-venue con selector de activo)
- Relación staff ↔ venue (venue_staff)
- Slots con máquina de estados
- Bookings con snapshots de precio
- Transacciones separadas por tipo
- Ciudades con `is_active`
- Todas las rutas protegidas con `auth` middleware

---

## FASE 5 — Staff (PENDIENTE)

### 13. `shift_logs` ⬜
Registro de cada turno del staff. El documento exige cuadre de caja auditable.

```
id              bigint PK
venue_id        FK → venues
user_id         FK → users (staff)
opened_at       datetime UTC
closed_at       datetime nullable UTC
expected_cash   DECIMAL(10,2) nullable  ← calculado al cerrar
delivered_cash  DECIMAL(10,2) nullable  ← lo que entrega el staff
notes           text nullable
created_at / updated_at
```

**Seeders:** 1 turno abierto hoy (Pedro Mamani, Canchas Yungay) + 5 turnos cerrados de días anteriores con sus montos.

---

### 14. `shift_movements` ⬜
Movimientos de caja dentro de un turno. Cada check-in, presencial y no-show queda registrado.

```
id              bigint PK
shift_log_id    FK → shift_logs
booking_id      FK → bookings nullable
type            enum: checkin | walkin | noshow_retention | manual
amount          DECIMAL(10,2)
description     string nullable
created_at      (sin updated_at — registro histórico)
```

---

## FASE 6 — Auditoría y notificaciones (PENDIENTE)

### 15. `audit_logs` ⬜
Bitácora inmutable de acciones críticas. El documento dice: **nadie, incluido el admin, puede borrar registros financieros.**

```
id              bigint PK
user_id         FK → users nullable  ← quién hizo la acción
action          string               ← PARTNER_APROBADO, BOOKING_REVERTIDO, etc.
target_type     string nullable       ← App\Models\Venue
target_id       bigint nullable
payload         JSON nullable         ← estado antes y después
ip_address      string nullable
created_at      (sin updated_at — INMUTABLE)
```

**Seeders:** Logs de las acciones del seeder (partners aprobados, bookings creados).

---

### 16. `notifications_log` ⬜
Registro de todas las notificaciones enviadas. Permite auditar y reenviar.

```
id          bigint PK
user_id     FK → users
channel     enum: email | push | whatsapp
type        string   ← booking_confirmed, checkin_reminder, noshow_alert
payload     JSON
status      enum: pending | sent | failed
sent_at     datetime nullable
created_at
```

---

## Campos faltantes a agregar en tablas existentes

### `bookings` — agregar `platform_fee`
```sql
ALTER TABLE bookings ADD COLUMN platform_fee DECIMAL(10,2) DEFAULT 0.00;
```
El documento exige que exista desde el día 1 aunque sea 0. Cuando se active la Fase 3 de monetización, se llena con el % correspondiente sin cambiar el esquema.

### `venues` — agregar `cover_image` (ya existe) y `rating_avg`
```sql
ALTER TABLE venues ADD COLUMN rating_avg DECIMAL(3,2) DEFAULT 0.00;
ALTER TABLE venues ADD COLUMN rating_count int DEFAULT 0;
```
Para cuando se implemente el sistema de calificaciones del documento (RF de ratings).

---

## Notas para migración a MySQL (producción)

1. Cambiar `latitude`/`longitude` DECIMAL por columna `POINT location` con índice `SPATIAL`
2. Agregar `idempotency_key` en `bookings` y `transactions`
3. Agregar tabla `webhook_events` (firma HMAC-SHA256 de la pasarela)
4. Cambiar `SESSION_DRIVER` a Redis
5. Configurar Redis para locks de slots (TTL 600s)
6. Implementar Laravel Horizon para colas por prioridad: critical > default > notifications > low
7. Activar `platform_fee` con el motor de comisiones
8. Ver Documento Arquitectónico Maestro v2.0 Sección 9 para el esquema completo

---

## Credenciales de prueba

| Rol | Email | Password |
|-----|-------|----------|
| Admin | admin@futgo.app | password |
| Partner (2 venues Huaraz) | juan.quispe@gmail.com | password |
| Partner (El 10, Cusco) | maria.lopez@gmail.com | password |
| Staff | pedro.staff@futgo.app | password |
| Jugador (con reservas) | mario.quispe@gmail.com | password |
| Jugador (con reservas) | luis.torres@gmail.com | password |

**How to apply:** Al iniciar sesión de trabajo en BD, consultar este archivo, identificar qué tabla sigue en la tabla de avance y ejecutar `php artisan migrate:fresh --seed` después de cada fase completada.
