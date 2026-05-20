# Documentación Técnica - DeMuFe

## 1. Arquitectura General

La aplicación DeMuFe sigue una arquitectura **SPA (Single Page Application)** con backend Laravel y frontend Vue 3:

```
Cliente (Vue 3 + Vite)  ←→  API REST (Laravel)  ←→  MySQL
         ↓
   localStorage (carrito)
```

- **Backend**: Laravel 11 con Sanctum para autenticación API
- **Frontend**: Vue 3 + TypeScript + Vite + PrimeVue (Volt) + Tailwind CSS
- **Base de datos**: MySQL

## 2. Almacenamiento de Datos

### 2.1 Base de Datos (MySQL)

**Tablas principales:**

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios (admin + clientes), autenticación Sanctum |
| `brands` | Marcas de vehículos |
| `cars` | Productos/vehículos con precio, stock, especificaciones |
| `orders` | Pedidos realizados |
| `order_items` | Productos dentro de cada pedido (con `has_to_comment`) |
| `opinions` | Valoraciones y comentarios de los usuarios |
| `tickets` | Tickets de soporte |
| `personal_access_tokens` | Tokens Sanctum |

**Relaciones Eloquent:**

```
User ──hasMany──→ Order
User ──hasMany──→ Opinion
User ──hasMany──→ Ticket (opcional, por email)

Order ──belongsTo──→ User
Order ──hasMany──→ OrderItem

OrderItem ──belongsTo──→ Order
OrderItem ──belongsTo──→ Car (product_id)

Car ──belongsTo──→ Brand
Car ──hasMany──→ OrderItem (product_id)
Car ──hasMany──→ Opinion (product_id)

Opinion ──belongsTo──→ Car (product_id)
Opinion ──belongsTo──→ User (opcional, idUser puede ser null)
```

### 2.2 localStorage (Carrito de Compra)

El carrito de compra se almacena en `localStorage` para persistir entre sesiones sin requerir autenticación:

```typescript
// stores/cart.ts
interface CartItem {
    id: number;
    name: string;
    quantity: number;
    imageRoute: string;
    price: number;
    stock: number;
}

// Almacenamiento
localStorage.setItem('cart', JSON.stringify(cartItems.value));

// Recuperación
const saved = localStorage.getItem('cart');
if (saved) cartItems.value = JSON.parse(saved);
```

**Decisión técnica**: Utilizamos `localStorage` en lugar de `IndexedDB` porque:
- Los datos del carrito son simples (array de objetos)
- No requiere consultas complejas
- `localStorage` es síncrono y más sencillo de integrar con Vue reactivity
- Capacidad suficiente para decenas de productos

Cuando el usuario se loguea, el carrito se mantiene en `localStorage` y se envía al backend al hacer checkout.

### 2.3 Autenticación con Tokens (Sanctum)

La autenticación se realiza mediante **Laravel Sanctum** con tokens de API:

```php
// AuthController@login
$token = $user->createToken('auth_token')->plainTextToken;
return response()->json([
    'access_token' => $token,
    'token_type'   => 'Bearer',
    'user'         => $user
]);
```

En el frontend, el token se guarda en `localStorage` y se añade a los headers de Axios:

```typescript
// stores/auth.ts
localStorage.setItem('auth_token', data.access_token);
axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
```

**Protección de rutas API** con middleware `auth:sanctum`. **Protección de rutas Vue Router** con guards `requiresAuth` y `requiresAdmin`.

## 3. Decisiones Técnicas Importantes

### 3.1 ¿Por qué SPA en lugar de Blade?

- Experiencia de usuario más fluida (sin recargas de página)
- Separación completa frontend/backend
- Permite reutilizar la misma API para futuros clientes (app móvil, etc.)
- Desarrollo independiente (equipos separados)

### 3.2 ¿Por qué PrimeVue (Volt) + Tailwind?

- Componentes ricos y accesibles (Select, InputNumber, DatePicker)
- Modo `unstyled` + `pt` personalización = control total sobre estilos
- Integración natural con Tailwind CSS
- Tema oscuro consistente

### 3.3 ¿Por qué Enum CarTypes en lugar de tabla de categorías?

Los tipos de vehículo (Deportivo, Lowrider, Clásico...) son valores fijos que no cambian. Usar un **Enum de PHP** (`CarTypes`) evita:
- Tablas adicionales en la BD
- CRUD innecesario
- Consultas JOIN

```php
enum CarTypes: string
{
    case Sport = 'Deportivo';
    case Lowrider = 'Lowrider';
    case Classic = 'Clasico';
    // ...
}
```

### 3.4 Gestión de Stock y Ventas

El campo `total_sold` se calcula con una **subquery SQL** en lugar de almacenarlo:

```php
$query->selectRaw('COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = cars.id), 0) as total_sold');
```

**Decisión**: Evita la duplicación de datos e inconsistencias. El cálculo en tiempo real es rápido gracias al índice de `product_id` en `order_items`.

Cuando se realiza una compra, el stock se decrementa dentro de una **transacción** para garantizar la integridad:

```php
DB::transaction(function () use ($request) {
    $car = Car::query()->lockForUpdate()->find($cartItem['id']);
    if ($car->stock < $cartItem['quantity']) {
        throw new \Exception("Stock insuficiente");
    }
    $car->decrement('stock', $cartItem['quantity']);
    // ...
});
```

### 3.5 Sistema de Opiniones con Recordatorio AJAX

El sistema cumple los requisitos del enunciado:
1. Cuando se compra, `has_to_comment = true` en cada `order_item`
2. Al login, llamada AJAX a `GET /api/check-pending-opinion`
3. Muestra `ReviewReminderModal` si hay pendientes
4. El usuario puede: opinar, decir "Ahora no", o "Recuérdamelo más tarde" (dismiss)

```typescript
// App.vue
watch(() => authStore.isLoggedIn, (loggedIn) => {
    if (loggedIn) {
        setTimeout(() => reviewReminder.value?.checkPending(), 500);
    }
});
```

### 3.6 Descuento Masivo (Bulk Discount)

Se actualizan todos los productos con una sola consulta SQL:

```php
Car::query()->update(['discount' => $request->discount]);
```

Esto permite aplicar descuentos de Black Friday o rebajas de forma masiva.

### 3.7 Gráfico de Ventas con Canvas

El gráfico de barras se dibuja con la API **Canvas** nativa del navegador (sin librerías externas):

```typescript
// AdminSalesChart.vue
const canvas = ref<HTMLCanvasElement | null>(null);
const ctx = canvas.value?.getContext('2d');
// Dibuja barras con gradiente, etiquetas y valores
```

**Decisión**: Canvas nativo evita dependencias pesadas (Chart.js, D3.js) para un gráfico sencillo.

### 3.8 Componentes Volt Personalizados

Todos los componentes Volt (`InputText`, `InputNumber`, `Select`) usan modo `unstyled` con temas `pt`:

```vue
<template>
    <InputText
        unstyled
        :pt="theme"
        :ptOptions="{ mergeProps: ptViewMerge }"
    />
</template>

<script setup>
const theme = ref({
    root: `bg-white text-gray-700 border border-gray-300 ...`
});
</script>
```

Esto permite cambiar colores globales desde un solo lugar. Recientemente se cambiaron todos los `text-surface-*` a `text-gray-*` para eliminar el tono lila no deseado.

## 4. Flujos de Datos Importantes

### 4.1 Flujo de Compra

```
1. Usuario navega catálogo → añade productos al carrito (localStorage)
2. Va a /checkout → introduce datos de envío/facturación
3. Submit → POST /api/orders (autenticado)
4. Backend:
   a. Valida stock
   b. Crea Order + OrderItems
   c. Decrementa stock
   d. Marca has_to_comment = true
   e. Retorna order_id
5. Frontend: vacía carrito, muestra toast éxito, dispara evento check-pending-opinion
```

### 4.2 Flujo de Valoración

```
1. POST /api/orders → has_to_comment = true
2. Usuario hace login → GET /api/check-pending-opinion (AJAX)
3. Si pendiente → ReviewReminderModal
4. Usuario hace clic "Opinar" → navega a CarView
5. Submit opinión → POST /api/sendOpinion
   - Si recibe `order_item_id`, marca has_to_comment = false
6. GET /api/getOpinions/{id} → retorna valoraciones con estrellas
```

## 5. Estructura de Archivos

```
app/
├── Http/Controllers/
│   ├── AdminController.php      # CRUD productos, marcas, pedidos, usuarios
│   ├── AuthController.php       # Login, registro, logout
│   ├── BrandController.php      # Listado de marcas (público)
│   ├── CarController.php        # CRUD de vehículos (API pública)
│   ├── OpinionController.php    # Sistema de valoraciones
│   ├── OrderController.php      # Creación de pedidos + historial
│   ├── ProfileController.php    # Perfil de usuario
│   └── TicketsController.php    # Tickets de soporte
├── Models/
│   ├── Car.php, Brand.php, Order.php, OrderItem.php,
│   ├── User.php, Opinion.php, Ticket.php
├── Enums/
│   ├── CarTypes.php, CarStates.php

resources/ts/
├── components/
│   ├── auth/Login.vue, Register.vue
│   ├── admin/AdminNav.vue
│   ├── HeaderBase.vue, FooterBase.vue
│   ├── BaseInput.vue, ReviewReminderModal.vue
├── views/
│   ├── admin/ (Dashboard, Products, Orders, Users, Tickets, SalesChart)
│   ├── CarView.vue, Catalog.vue, Cart.vue, CheckoutView.vue, ...
├── stores/
│   ├── auth.ts, cart.ts
├── router/index.ts
├── types.ts

src/volt/    ← Componentes PrimeVue personalizados
├── InputText.vue, InputNumber.vue, Select.vue, ...
```
