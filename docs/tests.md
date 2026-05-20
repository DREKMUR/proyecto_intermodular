# Tests y Pruebas de Error - DeMuFe

## 1. Introducción

Este documento recoge todas las pruebas realizadas durante el desarrollo de DeMuFe para garantizar el correcto funcionamiento del sistema. Se incluyen tests funcionales, pruebas de API, verificaciones de validación y corrección de errores detectados.

## 2. Pruebas de Autenticación

### 2.1 Registro de Usuario

| Prueba | Resultado |
|--------|-----------|
| Registro con datos correctos | ✅ Crea usuario, retorna token |
| Registro con email duplicado | ❌ Error 422 "El email ya está registrado" |
| Registro con contraseña débil (< 8 caracteres) | ❌ Error validación |
| Registro con fecha futura | ❌ Error "Debes tener entre 18 y 99 años" |
| Registro con teléfono sin código internacional | ❌ Error validación formato |
| Registro con nombre con números | ❌ Error validación (regex limpia) |

### 2.2 Login

| Prueba | Resultado |
|--------|-----------|
| Login con credenciales correctas | ✅ Retorna token + datos usuario |
| Login con email incorrecto | ❌ Error "Las credenciales son incorrectas" |
| Login con contraseña incorrecta | ❌ Error "Las credenciales son incorrectas" |
| Login sin email/password | ❌ Error validación |

### 2.3 Protección de Rutas (Middleware)

| Prueba | Resultado |
|--------|-----------|
| Acceder a `/api/admin/*` sin token | ❌ 401 Unauthenticated |
| Acceder a `/api/admin/*` con token de cliente | ❌ 403 Forbidden |
| Acceder a `/admin/*` (frontend) sin login | ✅ Redirige a `/login` |
| Acceder a `/admin/*` como cliente | ✅ Redirige a `/` |

## 3. Pruebas del Catálogo y Productos

### 3.1 CRUD de Productos (Admin)

| Prueba | Resultado |
|--------|-----------|
| Crear producto con datos correctos | ✅ 201 Created |
| Crear producto sin nombre | ❌ Error validación |
| Crear producto con precio negativo | ❌ Error validación |
| Editar producto (cambiar precio, stock) | ✅ 200 OK |
| Editar producto sin cambios | ✅ 200 OK |
| Eliminar producto | ✅ 200 + desaparición del listado |
| Descuento masivo 10% | ✅ Todos los productos actualizados |
| Buscar producto por nombre | ✅ Filtra correctamente |
| Ordenar por stock (asc/desc) | ✅ Ordenación correcta |

### 3.2 Visualización (Cliente)

| Prueba | Resultado |
|--------|-----------|
| Ver listado de productos | ✅ Muestra imagen, nombre, precio |
| Ver detalle de producto | ✅ Muestra info completa + valoraciones |
| Producto sin stock se muestra | ✅ Se muestra (pendiente ocultar según rúbrica) |

## 4. Pruebas del Carrito de Compra

| Prueba | Resultado |
|--------|-----------|
| Añadir producto al carrito (sin login) | ✅ Aparece en el carrito |
| Añadir producto al carrito (logueado) | ✅ Aparece en el carrito |
| Modificar cantidad | ✅ Precio total se actualiza |
| Eliminar producto del carrito | ✅ Desaparece, total se actualiza |
| Carrito vacío → mensaje "No hay productos" | ✅ Muestra empty state |
| **Persistencia localStorage** | ✅ Al cerrar y abrir navegador, el carrito se mantiene |
| Añadir más stock del disponible | ✅ Error "Stock insuficiente" al checkout |

## 5. Pruebas del Checkout y Pedidos

| Prueba | Resultado |
|--------|-----------|
| Checkout con carrito vacío | ❌ Error "El carrito está vacío" |
| Checkout con datos correctos | ✅ Pedido creado, stock decrementado |
| Checkout sin autenticación | ❌ Error "Debes iniciar sesión" |
| **Stock se decrementa correctamente** | ✅ Producto pasa a "Vendido" si stock llega a 0 |
| **Transacción: error en medio del proceso** | ✅ Rollback, stock no se modifica |

### 5.1 Gestión de Pedidos (Admin)

| Prueba | Resultado |
|--------|-----------|
| Ver listado de pedidos | ✅ Muestra ID, cliente, productos, total, estado |
| Cambiar estado a "Enviado" | ✅ Estado actualizado |
| Eliminar pedido | ✅ Pedido eliminado |

### 5.2 Historial de Pedidos (Cliente)

| Prueba | Resultado |
|--------|-----------|
| Ver historial del usuario | ✅ Muestra solo sus pedidos |
| Pedido con estado "Completado" | ✅ Badge verde |
| Sin pedidos → mensaje | ✅ "No tienes pedidos todavía" |

## 6. Pruebas del Sistema de Opiniones

### 6.1 API de Opiniones

| Prueba | Resultado |
|--------|-----------|
| `GET /api/getOpinions/{id}` producto existente | ✅ Retorna opiniones + ratings |
| `GET /api/getOpinions/{id}` sin opiniones | ✅ ratings vacíos, totalOpinions=0 |
| `GET /api/getOpinions/{id}` id inexistente | ✅ Error 404 |
| `POST /api/sendOpinion` correcto | ✅ 201 Created |
| `POST /api/sendOpinion` sin rating | ❌ Error 400 |
| `GET /api/getRating` | ✅ Top 10 productos por valoración |
| `POST /api/sendOpinion` con `order_item_id` | ✅ `has_to_comment` = false |

### 6.2 Recordatorio de Opinión (ReviewReminderModal)

| Prueba | Resultado |
|--------|-----------|
| Compra → flag `has_to_comment` activado | ✅ true en order_items |
| Login después de compra → AJAX check | ✅ Modal aparece si hay pendientes |
| Hacer clic "Opinar" | ✅ Navega al producto |
| Hacer clic "Ahora no" | ✅ Modal se cierra |
| Hacer clic "Recuérdamelo más tarde" | ✅ Modal se cierra, flag se mantiene |
| **Token fix al refresh** | ✅ Autorización mantenida |

### 6.3 Visualización de Estrellas

| Prueba | Resultado |
|--------|-----------|
| Mostrar 5 estrellas (valoración 5/5) | ✅ Todas amarillas |
| Mostrar 3 estrellas | ✅ 3 amarillas, 2 grises |
| Hover sobre estrellas (enviar opinión) | ✅ Cambian de color |

## 7. Pruebas de los Tickets

| Prueba | Resultado |
|--------|-----------|
| Crear ticket sin autenticación | ✅ 201 Created, permitido por rúbrica |
| Crear ticket con datos incorrectos | ❌ Error validación |
| Listar tickets propios (logueado) | ✅ Solo los suyos |
| **Bug: GET /api/ticket devolvía vacío** | ✅ **Fix**: Faltaba middleware `auth:sanctum`. Añadido. |
| Admin ve todos los tickets | ✅ Paginados, con filtro de estado |
| Cerrar ticket (admin) | ✅ Estado cambia a "Cerrado" |
| Reabrir ticket (admin) | ✅ Estado cambia a "Abierto" |

## 8. Pruebas del Perfil de Usuario

| Prueba | Resultado |
|--------|-----------|
| Ver perfil propio | ✅ Datos precargados |
| Actualizar nombre | ✅ Cambiado, authStore actualizado |
| Actualizar email | ✅ Cambiado |
| Email duplicado | ❌ Error validación backend |

## 9. Pruebas del Panel Admin

### 9.1 Dashboard

| Prueba | Resultado |
|--------|-----------|
| Muestra estadísticas correctas | ✅ Total productos, pedidos, usuarios |
| Stock bajo se muestra | ✅ Count de productos con stock < 5 |
| Sin datos → valores 0 | ✅ No errors |

### 9.2 Gráfico de Ventas

| Prueba | Resultado |
|--------|-----------|
| Gráfico se dibuja correctamente | ✅ Barras proporcionales, nombres de producto |
| Sin ventas → mensaje | ✅ "No hay datos de ventas todavía" |

### 9.3 Gestión de Usuarios

| Prueba | Resultado |
|--------|-----------|
| Listado de usuarios con contador de pedidos | ✅ withCount('orders') |
| Editar usuario (cambiar nombre, hacer admin) | ✅ 200 OK |
| Eliminar usuario | ✅ Desaparece del listado |
| **Bug: Error 500 al cargar usuarios** | ✅ **Fix**: Faltaba `orders()` relationship al modelo User |
| Auto-eliminación | ❌ Error "No puedes eliminarte a ti mismo" |

## 10. Pruebas de Validación del Formulario de Registro

*(Implementado recientemente)*

| Prueba | Resultado |
|--------|-----------|
| Fecha DD/MM/YYYY correcta (15/05/1998) | ✅ Válida, edad calculada correctamente |
| Fecha incorrecta (32/13/2020) | ❌ Inválida |
| Fecha con edad < 18 | ❌ Inválida |
| Fecha con edad ≥ 100 | ❌ Inválida |
| `<meter>` muestra color correcto | ✅ Rojo (débil), amarillo (medio), verde (fuerte) |
| Dirección con patrón correcto | ✅ "Calle Mayor 10, 28001 Madrid" |
| Dirección sin número | ❌ Inválida |
| Dirección sin CP | ❌ Inválida |
| Campo tocado e inválido → borde rojo | ✅ Feedback visual por campo |
| Campo tocado y válido → borde verde | ✅ Feedback visual por campo |

## 11. Pruebas de Componentes Volt

| Prueba | Resultado |
|--------|-----------|
| InputText texto lila → gray | ✅ Cambio global en `src/volt/InputText.vue` |
| InputNumber texto lila → gray | ✅ Cambio global en `src/volt/InputNumber.vue` |
| Select texto lila → gray | ✅ Cambio global en `src/volt/Select.vue` |
| **Bug: Año 2026 → 2.026** | ✅ **Fix**: `:useGrouping="false"` al campo year |

## 12. Pruebas de Responsive Design

| Prueba | Resultado |
|--------|-----------|
| Header en móvil (< 768px) | ✅ Menú hamburguesa, enlaces verticales |
| Tablas admin en móvil | ✅ Scroll horizontal |
| Formularios en móvil | ✅ Columnas únicas (`md:col-span-2`) |

## 13. Resumen de Errores Corregidos

| Error | Causa | Solución |
|-------|-------|----------|
| Token no se enviaba al refresh | `auth.ts` solo ponía header al login | Añadir `axios.defaults.headers.common['Authorization']` a la inicialización |
| 500 al cargar usuarios admin | Modelo User no tenía `orders()` HasMany | Añadir relación al modelo |
| GET /api/ticket devolvía vacío | No tenía middleware `auth:sanctum` | Añadir middleware a la ruta |
| Año 2.026 al crear producto | InputNumber aplicaba separador de miles | `:useGrouping="false"` |
| Texto/borde lila en inputs | `text-surface-700` en preset Volt | Cambiar a `text-gray-700` en InputText, InputNumber, Select |
| Error 500 al enviar opinión sin `order_item_id` | Faltaba comprobación de null | Añadir `if (isset($data['order_item_id']))` |
| `has_to_comment` nunca se activaba | OrderController no ponía flag | Añadir `'has_to_comment' => true` al crear order items |
