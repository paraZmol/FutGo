# Plan de Base de Datos — FutGo (Simulación)

**Estado general:** Pendiente de inicio  
**Motor actual:** SQLite (desarrollo local)  
**Motor futuro:** MySQL 8 (producción — ese es otro plan, ver Documento Arquitectónico Maestro v2.0)

---

## Por qué esta BD es DIFERENTE a la del documento

El documento arquitectónico define una BD de producción que requiere:
- MySQL 8 con columnas POINT + índice SPATIAL (geoespacial)
- Redis para locks distribuidos (anti-colisión de slots)
- Webhooks firmados con HMAC-SHA256 (pasarela de pago real)
- Workers con Laravel Horizon para colas

**Esta BD de simulación simplifica todo eso** para que las vistas Blade funcionen con datos reales sin infraestructura compleja. Cuando se migre a producción se reemplaza por el esquema del documento.

---

## Simplificaciones aplicadas

| Concepto del documento | Cómo se simplifica aquí |
|---|---|
| Columna POINT geoespacial | `latitude` y `longitude` DECIMAL simples |
| Redis lock distribuido | Solo campo `status` en `slots` con transacción MySQL |
| Webhook HMAC-SHA256 | Campo `payment_status` directo en `bookings` |
| Idempotency keys | No se implementa en simulación |
| Workers y colas (Horizon) | No aplica — todo es síncrono |

## Reglas que SÍ se respetan

- Montos siempre `DECIMAL(10,2)` — nunca float
- Timestamps en UTC
- Máquina de estados del slot: `available | pending_payment | reserved | completed | expired`
- Un slot solo puede pertenecer a un booking
- Precios en bookings son snapshot al momento de reservar
- `audit_logs` solo tiene `created_at` (sin `updated_at`)

---

## Estado de avance

| # | Fase | Tabla | Estado |
|---|------|-------|--------|
| 1 | Usuarios | users | ✅ Listo |
| 2 | Usuarios | password_reset_tokens | ✅ Listo |
| 3 | Ciudades | cities | ✅ Listo |
| 4 | Complejos | venues | ✅ Listo |
| 5 | Complejos | venue_staff | ✅ Listo |
| 6 | Complejos | fields | ✅ Listo |
| 7 | Inventario | operating_hours | ✅ Listo |
| 8 | Inventario | slots | ✅ Listo |
| 9 | Inventario | events | ✅ Listo |
| 10 | Reservas | bookings | ✅ Listo |
| 11 | Reservas | booking_slots | ✅ Listo |
| 12 | Reservas | transactions | ✅ Listo |
| 13 | Staff | shift_logs | ⬜ Pendiente |
| 14 | Staff | shift_movements | ⬜ Pendiente |
| 15 | Auditoría | audit_logs | ⬜ Pendiente |
| 16 | Auditoría | notifications_log | ⬜ Pendiente |

**Total: 16 tablas en 6 fases**

**Total: 15 tablas en 6 fases**

---

## FASE 1 — Usuarios

### 1. `users` ⬜
```
id                  bigint PK autoincrement
name                string
email               string unique
password            string (hashed)
role                enum: user | partner | staff | admin   default: user
phone               string nullable
avatar_url          string nullable
email_verified_at   timestamp nullable
remember_token      string nullable
created_at / updated_at
```
**Nota:** Agregar `city_id FK → cities nullable` a esta tabla cuando se cree la migración de cities (Fase 2).  
**Seeders:** 1 admin, 2 moderadores, 5 partners, 3 staff, 20 jugadores con nombres peruanos y ciudades asignadas.

---

### 2. `password_reset_tokens` ⬜
```
email       string PK
token       string
created_at  timestamp
```

---

## FASE 2 — Ciudades

### 3. `cities` ⬜
Ciudades donde opera FutGo. Centraliza la cobertura y permite filtrar canchas y eventos por ciudad. El jugador elige su ciudad al registrarse o en el buscador; si cambia de ciudad lo decide él (GPS o búsqueda manual).

**Lógica:**
- FutGo decide qué ciudades activa (`is_active`)
- `venues` tiene FK a `cities` — un complejo pertenece a una ciudad
- `users` tiene FK nullable a `cities` — la ciudad preferida del jugador
- El home pre-filtra canchas y eventos según `city_id` del usuario logueado o de la cookie de ciudad seleccionada

```
id           bigint PK autoincrement
name         string           (Huaraz, Cusco, Lima...)
department   string           (Áncash, Cusco, Lima...)
slug         string unique    (huaraz, cusco, lima)
latitude     DECIMAL(10,8)    (centro de la ciudad)
longitude    DECIMAL(11,8)
is_active    boolean default false   (solo Admin activa ciudades)
created_at / updated_at
```

**Seeders:** Huaraz (activa), Cusco (activa), Lima (activa), Arequipa (activa), Trujillo (activa). Más ciudades con `is_active = false` para cuando expanda.

**También agregar a `users`:**
- `city_id` FK → cities nullable (ciudad preferida del jugador)

---

## FASE 3 — Complejos y canchas

### 4. `venues` ⬜
```
id              bigint PK
user_id         FK → users (partner dueño)
city_id         FK → cities
name            string
slug            string unique
description     text nullable
address         string
district        string
latitude        DECIMAL(10,8)
longitude       DECIMAL(11,8)
status          enum: pending | active | suspended   default: pending
cover_image     string nullable
created_at / updated_at
```
**Seeders:** 5 complejos distribuidos en Huaraz, Cusco y Lima con coordenadas reales.

---

### 4. `venue_staff` ⬜
```
id          bigint PK
venue_id    FK → venues
user_id     FK → users (role=staff)
active      boolean default true
created_at / updated_at
UNIQUE(venue_id, user_id)
```

---

### 5. `fields` ⬜
```
id          bigint PK
venue_id    FK → venues
name        string (Cancha 1, Cancha 2...)
sport_type  enum: futbol5 | futbol7 | futbol11
surface     enum: sintetico | natural
is_covered  boolean default false
amenities   JSON nullable
status      enum: active | maintenance   default: active
created_at / updated_at
```
**Seeders:** 2-4 canchas por venue con variedad de tipos.

---

## FASE 3 — Inventario de tiempo

### 6. `operating_hours` ⬜
```
id              bigint PK
field_id        FK → fields
day_of_week     tinyint 0-6 (0=domingo)
opens_at        time
closes_at       time
price_day       DECIMAL(10,2)
price_night     DECIMAL(10,2)
deposit_amount  DECIMAL(10,2)
is_active       boolean default true
created_at / updated_at
UNIQUE(field_id, day_of_week)
```

---

### 7. `slots` ⬜
```
id              bigint PK
field_id        FK → fields
booking_id      FK → bookings nullable
starts_at       datetime UTC
ends_at         datetime UTC
status          enum: available | pending_payment | reserved | event_occupied | completed | expired
unit_price      DECIMAL(10,2)
lock_expires_at datetime nullable
created_at / updated_at
INDEX(field_id, starts_at)
INDEX(status)
```
**Seeders:** Command `slots:seed` — genera slots 30 días adelante, algunos ya reservados.

---

### 8. `events` ⬜
```
id          bigint PK
venue_id    FK → venues
field_id    FK → fields nullable
title       string
type        enum: torneo | mantenimiento | evento_privado | otro
starts_at   datetime UTC
ends_at     datetime UTC
created_by  FK → users
created_at / updated_at
```

---

## FASE 4 — Reservas y pagos

### 9. `bookings` ⬜
```
id              bigint PK
user_id         FK → users
field_id        FK → fields
qr_token        string unique (UUID)
status          enum: pending | confirmed | checked_in | no_show | cancelled | completed
total_price     DECIMAL(10,2) snapshot
deposit_amount  DECIMAL(10,2) snapshot
balance_due     DECIMAL(10,2) snapshot
payment_status  enum: unpaid | paid | refunded   default: unpaid
payment_method  enum: yape | plin | tarjeta | efectivo nullable
is_walkin       boolean default false
notes           text nullable
created_at / updated_at
```

---

### 10. `booking_slots` ⬜
```
id          bigint PK
booking_id  FK → bookings
slot_id     FK → slots UNIQUE
unit_price  DECIMAL(10,2) snapshot
```

---

### 11. `transactions` ⬜
```
id              bigint PK
booking_id      FK → bookings
amount          DECIMAL(10,2)
type            enum: deposit | balance | refund | walkin
payment_method  enum: yape | plin | tarjeta | efectivo
status          enum: pending | approved | rejected | refunded
notes           string nullable
created_at / updated_at
```

---

## FASE 5 — Operación del Staff

### 12. `shift_logs` ⬜
```
id              bigint PK
venue_id        FK → venues
user_id         FK → users (staff)
opened_at       datetime UTC
closed_at       datetime nullable UTC
expected_cash   DECIMAL(10,2) nullable
delivered_cash  DECIMAL(10,2) nullable
notes           text nullable
created_at / updated_at
```

---

### 13. `shift_movements` ⬜
```
id              bigint PK
shift_log_id    FK → shift_logs
booking_id      FK → bookings nullable
type            enum: checkin | walkin | noshow_retention | manual
amount          DECIMAL(10,2)
description     string nullable
created_at
```

---

## FASE 6 — Auditoría y notificaciones

### 14. `audit_logs` ⬜
```
id              bigint PK
user_id         FK → users nullable
action          string
target_type     string nullable
target_id       bigint nullable
payload         JSON nullable
ip_address      string nullable
created_at      (sin updated_at — inmutable)
```

---

### 15. `notifications_log` ⬜
```
id          bigint PK
user_id     FK → users
channel     enum: email | push | whatsapp
type        string
payload     JSON
status      enum: pending | sent | failed
sent_at     datetime nullable
created_at
```

---

## Notas para migración a MySQL (producción)

- Agregar columna `POINT location` en `venues` con índice SPATIAL
- Agregar `idempotency_key` en `bookings` y `transactions`
- Agregar tabla `webhook_events` con firma HMAC
- Configurar Redis para locks de slots
- Implementar Laravel Horizon para colas
- Ver Documento Arquitectónico Maestro v2.0 Sección 9
