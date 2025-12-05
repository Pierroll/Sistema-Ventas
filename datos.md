| Entidad | Campo 1 | Valor 1 | Campo 2 | Valor 2 | Notas |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Usuario Admin** | `correo` | `admin@agmail.com` | `clave` | `admin` | Credenciales válidas para login. Usado en `testall/fixtures/testData.js`. |
| **Usuario Vendedor**| `correo` | `vendedor@agmail.com`| `clave` | `vendedor`| Rol con permisos limitados. Usado en `testall/fixtures/testData.js`. |
| **Cliente Válido** | `nombre` | `Cliente Test` | `dni` | `12345678` | Cliente predefinido en `test_setup.php` para pruebas de búsqueda. |
| **Cliente Duplicado**| `dni` | `12345678` | | | Usar este DNI al crear un nuevo cliente debe disparar la validación de duplicado. |
| **Producto Válido** | `codigo` | `P-0001` | `stock` | `50` | (Ejemplo) Producto con stock suficiente para pruebas de ventas. |
| **Producto sin Stock**| `codigo` | `P-9999` | `stock` | `0` | (Ejemplo) Usado para validar que no se puede agregar al carrito/venta. |
