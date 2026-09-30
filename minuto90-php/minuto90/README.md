# Minuto 90' — Versión PHP + MySQL

Tienda de camisetas de fútbol hecha con **PHP + MySQL** (backend) y
**HTML/CSS/JavaScript** (frontend).

---

## 1. Instalación paso a paso

### Requisitos
Descarga e instala **XAMPP** desde https://www.apachefriends.org
(trae Apache + MySQL + PHP + phpMyAdmin en un solo instalador).

### Pasos

1. **Copia la carpeta del proyecto** dentro de `htdocs`:
   - Windows: `C:\xampp\htdocs\minuto90`
   - Mac: `/Applications/XAMPP/htdocs/minuto90`

2. **Enciende Apache y MySQL** desde el panel de control de XAMPP
   (los dos deben quedar en verde).

3. **Crea la base de datos**:
   - Abre http://localhost/phpmyadmin
   - Clic en la pestaña **Importar**
   - Selecciona el archivo `sql/minuto90.sql`
   - Clic en **Continuar**

   Esto crea la base `minuto90` con sus 5 tablas y los 30 productos.

4. **Abre el sitio**: http://localhost/minuto90/

5. **Inicia sesión o regístrate**:
   - Puedes iniciar sesión con el usuario **administrador** creado por defecto:
     - **Correo:** `admin@minuto90.com`
     - **Contraseña:** `admin123`
   - O regístrate en http://localhost/minuto90/register.php para crear un nuevo usuario.

### Si sale error de conexión
Abre `config/db.php` y revisa los datos. Por defecto XAMPP usa usuario
`root` sin contraseña. Si le pusiste contraseña a MySQL, cámbiala ahí.

---

## 2. Estructura del proyecto

```
minuto90/
├── admin/                      Panel de Administración
│   ├── index.php               Dashboard principal (métricas y últimos pedidos)
│   ├── pedidos.php             Gestión de pedidos (cambio de estado y reembolsos)
│   ├── usuarios.php            Gestión de usuarios (lista con direcciones y opción de eliminar)
│   └── header_admin.php        Encabezado y control de acceso por rol
├── config/
│   └── db.php                  Conexión a MySQL con PDO
├── includes/
│   ├── funciones.php           Funciones reutilizables (sesión, carrito, precios, roles)
│   ├── header_tienda.php       Encabezado que usan todas las páginas internas
│   └── sidebar.php             Menú lateral generado desde la BD
├── sql/
│   └── minuto90.sql            Script para crear la BD y cargar los datos
├── css/
│   └── admin.css               Estilos del panel de administración
├── js/
│   ├── tienda.js               Menús y selección de talla
│   └── validation.js           Validación en el navegador
├── img/                        Imágenes de productos y banners
│
├── index.php                   Home público (ofertas destacadas de la BD)
├── register.php                Registro (guarda usuario con password_hash)
├── login.php                   Login (verifica con password_verify)
├── logout.php                  Cierra sesión
├── bienvenida.php              Pantalla de bienvenida
├── tienda.php                  Home interno con banner y menú lateral
├── catalogo.php                Catálogo dinámico (reemplaza 10 archivos HTML)
├── producto.php                Ficha de producto
├── buscar.php                  Buscador de productos
├── carrito.php                 Carrito + formulario de compra
├── procesar_pedido.php         Guarda el pedido en la BD (con transacción)
├── gracias.php                 Confirmación de compra
├── perfil.php                  Datos del usuario + historial real de compras
├── seguimiento.php             Estado del pedido
└── ayuda.php                   Preguntas frecuentes
```

---

## 3. Base de datos

### Tablas

| Tabla | Para qué sirve |
|---|---|
| `usuarios` | Cuentas registradas, con contraseña cifrada y campo de rol (`usuario` o `admin`) |
| `categorias` | Novedades, Hombre, Niños, Guayos, Descuento |
| `productos` | Los 30 productos con precio, imagen, tallas y categoría |
| `pedidos` | Cabecera de cada compra (totales, envío, estado) |
| `pedido_items` | Qué productos y tallas tenía cada pedido |

### Relaciones (claves foráneas)
- `productos.categoria_id` → `categorias.id`
- `pedidos.usuario_id` → `usuarios.id`
- `pedido_items.pedido_id` → `pedidos.id`
- `pedido_items.producto_id` → `productos.id`

---

## 4. Lo que cambió respecto a la versión HTML

| Antes (HTML + JS) | Ahora (PHP + MySQL) |
|---|---|
| 10 archivos de catálogo repetidos | **1 solo** `catalogo.php` que consulta la BD |
| Productos escritos a mano en el HTML | Productos guardados en la tabla `productos` |
| Login que no verificaba nada | Login real contra la BD con contraseña cifrada |
| Carrito en `localStorage` (se perdía) | Carrito en sesión de PHP + pedido guardado en la BD |
| Historial de compras inventado | Historial real leído de `pedidos` |
| "Ordenar por" reordenaba con JS | `ORDER BY` en la consulta SQL |
| Paginación con archivos `hombre2.html` | `LIMIT` y `OFFSET` calculados en PHP |

### Para agregar un producto nuevo
Ya **no tocas código**. Entra a phpMyAdmin → tabla `productos` → Insertar,
llena los campos y aparece solo en el catálogo, con su paginación
recalculada automáticamente.

---

## 5. Puntos de seguridad aplicados

Estos detalles son buenos para mencionar al presentar el proyecto:

- **Consultas preparadas (PDO)** en todas las consultas: evita inyección SQL.
  Nunca se concatena texto del usuario dentro del SQL.
- **`password_hash()` / `password_verify()`**: las contraseñas nunca se
  guardan en texto plano. Ni siquiera desde phpMyAdmin se pueden leer.
- **`htmlspecialchars()`** (la función `e()`) al imprimir cualquier dato:
  evita inyección de HTML/JavaScript (XSS).
- **Lista blanca para el ORDER BY**: el criterio de ordenamiento no se toma
  directo de la URL, se valida contra un array permitido.
- **`session_regenerate_id()`** al iniciar sesión: evita robo de sesión.
- **Transacción** al guardar el pedido: o se guarda completo o no se guarda
  nada, nunca queda un pedido a medias.
- **Mensaje de error genérico** en el login ("correo o contraseña
  incorrectos"): no revela si un correo existe o no en la base.

---

## 6. Ideas para seguir mejorando

- **Panel de administración completo incluido en `/admin/`**:
  - **Gestión de Usuarios:** Ver todos los usuarios registrados con su nombre, correo, teléfono y dirección física guardada, además de poder eliminar usuarios de la base de datos.
  - **Gestión de Pedidos:** Ver todos los pedidos con sus detalles completos (dirección de envío, items comprados, tallas, cantidades, totales) y cambiar el estado del pedido entre:
    - 📦 *En Bodega* (en proceso)
    - 🚚 *Enviado* (en camino)
    - ✅ *Entregado / Recibido*
    - ❌ *Cancelado*
    - 💸 *Reembolsado*
- Subida de imágenes de productos desde un formulario
- Filtros por precio y talla en el catálogo
- Recuperación de contraseña por correo
