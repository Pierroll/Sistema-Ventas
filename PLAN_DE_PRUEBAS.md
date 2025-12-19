ID Caso	Módulo	Descripción del Caso de Prueba	Tipo	Tipo de Prueba	Técnica Aplicada	Prioridad	Responsable	Fecha Ejecución	Estado	Resultado Obtenido	Defecto ID	Observaciones
FUNC-AUTH-01	Autenticación	Login exitoso con credenciales válidas (admin)	E2E Auto	Caja Negra	Partición de Equivalencia (Clase Válida)	CRÍTICA	Pinedo Calle	28/10	PASS	El sistema muestra el mensaje de bienvenida		URL: / ; Selectores: input[name='correo'], input[name='clave']; Esperar AJAX /usuarios/validar
FUNC-AUTH-02	Autenticación	Login exitoso con credenciales válidas (vendedor)	E2E Auto	Caja Negra	Partición de Equivalencia (Clase Válida - Diferente Rol)	CRÍTICA	Pinedo Calle	28/10	PASS	El sistema muestra el mensaje de bienvenida		Similar a AUTH-01 pero con rol vendedor
FUNC-AUTH-03	Autenticación	Login fallido con contraseña incorrecta	E2E Auto	Caja Negra	Partición de Equivalencia (Clase Inválida)	ALTA	Pinedo Calle	28/10	PASS	El Sistema muestra: Usuario o contraseña incorrecta		Validar SweetAlert (#swal2-title)
FUNC-AUTH-04	Autenticación	Login fallido con correo no existente	E2E Auto	Caja Negra	Partición de Equivalencia (Clase Inválida)	MEDIA	Pinedo Calle	28/10	PASS	El Sistema muestra: Usuario o contraseña incorrecta		Similar a AUTH-03, validar mensaje de error
FUNC-AUTH-05	Autenticación	Login con campos vacíos	Manual	Caja Negra	Análisis de Valores Límite	MEDIA	Pinedo Calle	28/10	PASS	El sistem pide rellenar los campos		Validar mensajes de validación del formulario
FUNC-AUTH-06	Autenticación	Logout de sesión cierra la sesión activa	E2E Auto	Caja Negra	Prueba de Estado (Transición Sesión)	ALTA	Pinedo Calle	28/10	PASS	La sesion se cierra exitosamente		Selector: [onclick='salir(event)']; Validar redirect a /
FUNC-AUTH-07	Autenticación	Recordar sesión (si aplica)	Manual	Caja Negra	Prueba de Estado (Persistencia)	BAJA	Pinedo Calle	28/10	FAIL	No tiene la opción para recordar sesiones	02	Verificar si existe funcionalidad de 'Recordarme'
FUNC-AUTH-08	Autenticación	Bloqueo de cuenta tras múltiples intentos fallidos	Manual	Caja Negra	Análisis de Valores Límite	BAJA	Pinedo Calle	28/10	FAIL	No tiene limites de intentos	03	Intentar 5+ logins fallidos y verificar bloqueo
FUNC-USER-01	Usuarios	(Admin) Crear un nuevo usuario 'Vendedor'	Manual	Caja Negra	Partición de Equivalencia (Clase Válida)	ALTA	Tarazona Narciso	28/10	PASS	El vendedor se crear exitosamente		Verificar creación en BD y permisos asignados
FUNC-USER-02	Usuarios	(Admin) Intentar crear usuario con correo duplicado	Integración	Caja Blanca	Prueba de Integridad BD (Constraint UNIQUE)	ALTA	Tarazona Narciso	28/10	PASS	El sistema muetra el mensaje de usuario ya existente		Validar constraint UNIQUE en BD
FUNC-USER-03	Usuarios	(Admin) Editar información de un usuario	Manual	Caja Negra	Partición de Equivalencia (Modificación)	MEDIA	Tarazona Narciso	28/10	PASS	La informacion de ususario se actualiza dorectamente		Modificar nombre, rol, estado
FUNC-USER-04	Usuarios	(Admin) Desactivar (eliminar lógicamente) un usuario	E2E Auto	Caja Negra	Prueba de Estado (Cambio Estado)	MEDIA	Tarazona Narciso	28/10	PASS	El usuario se elimina exitosamente		Campo estado debe cambiar a 'Inactivo'
FUNC-USER-05	Usuarios	(Admin) Reactivar un usuario inactivo	Manual	Caja Negra	Prueba de Estado (Cambio Estado)	MEDIA	Tarazona Narciso	28/10	FAIL	Los usuarios eliminados no aparecen en la lista		Cambiar estado de 'Inactivo' a 'Activo'
FUNC-USER-06	Usuarios	(Admin) Asignar permisos específicos a un rol	Manual	Caja Negra	Tabla de Decisión (Permisos x Roles)	ALTA	Tarazona Narciso	28/10	PASs	No existe la opción de rol	04	Verificar matriz de permisos por rol
FUNC-USER-07	Usuarios	(Vendedor) Intentar acceder a la gestión de usuarios	E2E Auto	Caja Negra	Prueba de Seguridad (Control Acceso)	ALTA	Rivera de la Matta	28/10	FAIL	EL vendedor si puede ingresar a modulo de gesntio ursers	01	Login vendedor, navegar /usuarios, validar redirect /administracion/permisos
FUNC-CLI-01	Clientes	Crear un nuevo cliente con datos completos y válidos	Manual	Caja Negra	Partición de Equivalencia (Clase Válida)	ALTA	Rivera de la Matta	28/10	PASS	El clinete es registrado exitosamente		DNI/RUC, nombre, dirección, teléfono
FUNC-CLI-02	Clientes	Intentar crear un cliente con DNI/RUC duplicado	Integración	Caja Blanca	Prueba de Integridad BD (Constraint UNIQUE)	ALTA	Rivera de la Matta	28/10	PASS	El sistem valida los DNI´s		Validar constraint UNIQUE
FUNC-CLI-03	Clientes	Editar la dirección de un cliente	Manual	Caja Negra	Partición de Equivalencia (Modificación)	MEDIA	Rivera de la Matta	28/10	PASS	Los botones de editar no funcionan		Modal de edición, actualizar dirección
FUNC-CLI-04	Clientes	Buscar un cliente por nombre	E2E Auto	Caja Negra	Partición de Equivalencia (Búsqueda)	ALTA	Rivera de la Matta	28/10	PASS	El cliente es encontrado exitosamente.		Selector: input[type='search']. Validar DataTables filtra
FUNC-CLI-05	Clientes	Desactivar un cliente	Manual	Caja Negra	Prueba de Estado (Cambio Estado)	MEDIA	Rivera de la Matta	28/10	PASS	No tiene funcion el boton eliminar en cliente		Cambiar estado a 'Inactivo'
FUNC-PROD-01	Productos	Crear un producto con todos los campos válidos	Integración	Caja Negra	Partición de Equivalencia (Clase Válida)	ALTA	Bardales Rojas	28/10	PASS	El producto se creo exitosamente		Código, nombre, precios, stock, categoría, medida
FUNC-PROD-02	Productos	Intentar crear un producto con código duplicado	Integración	Caja Blanca	Prueba de Integridad BD (Constraint UNIQUE)	ALTA	Bardales Rojas	28/10	PASS	El sistema no permite crear producto con codigo duplicado		Validar constraint UNIQUE en código
FUNC-PROD-03	Productos	Editar el precio de venta de un producto	E2E Auto	Caja Negra	Partición de Equivalencia (Modificación)	ALTA	Bardales Rojas	28/10	PASS	El precio es actulizado con normalidad		Modal de edición, actualizar precio_venta
FUNC-PROD-04	Productos	Asignar una imagen a un producto	Manual	Caja Negra	Partición de Equivalencia (Upload Archivo)	MEDIA	Bardales Rojas	28/10	PASS	La imagen se actualiza exitosamente		Upload de imagen, validar almacenamiento
FUNC-PROD-05	Productos	Crear, Editar y Eliminar una Categoría	Manual	Caja Negra	Prueba Funcional (CRUD Completo)	MEDIA	Bardales Rojas	28/10	PASS	Crud completo completado exitosamente		CRUD completo de categorías
FUNC-PROD-06	Productos	Crear, Editar y Eliminar una Unidad de Medida	Manual	Caja Negra	Prueba Funcional (CRUD Completo)	MEDIA	Bardales Rojas	28/10	PASS	Crud completo completado exitosamente		CRUD completo de medidas
FUNC-COMP-01	Compras	Registrar una compra a un proveedor	Manual	Caja Negra	Partición de Equivalencia + Verificación BD	ALTA	Pinedo Calle	28/10	PASS	La comprar ha sido registrada		Verificar incremento de stock
FUNC-COMP-02	Compras	Ver el historial de compras por rango de fechas	Manual	Caja Negra	Análisis de Valores Límite (Rangos Fecha)	MEDIA	Pinedo Calle	28/10	PASS	El hisorial de compras es filtrado con éxito		Filtro de fechas, validar resultados
FUNC-COMP-03	Compras	Generar reporte de una compra en PDF	Manual	Caja Negra	Partición de Equivalencia (Generación PDF)	MEDIA	Pinedo Calle	28/10	PASS	Reporte de compra generaod exitosamente		Descargar PDF, validar contenido
FUNC-COMP-04	Compras	Ajuste manual de inventario (aumento)	Integración	Caja Blanca	Prueba de Integridad BD (Update Stock)	MEDIA	Pinedo Calle	28/10	PASS	Ajuste de inventario exitoso		Incrementar stock manualmente, validar en BD
FUNC-COMP-05	Compras	Ajuste manual de inventario (disminución)	Integración	Caja Blanca	Prueba de Integridad BD (Update Stock)	MEDIA	Pinedo Calle	28/10	PASS	Ajuste de inventario exitoso		Decrementar stock manualmente, validar en BD
FUNC-VENTA-01	Ventas	Agregar producto al carrito (stock suficiente)	E2E Auto	Caja Negra	Partición de Equivalencia (Stock Suficiente)	CRÍTICA	Tarazona + Rivera	28/10	PASS	Producto agregad correctamente		Validar producto en carrito, stock reservado
FUNC-VENTA-02	Ventas	Intentar agregar producto sin stock	E2E Auto	Caja Negra	Partición de Equivalencia (Stock Insuficiente)	CRÍTICA	Tarazona + Rivera	28/10	PASS	Producto sin stock no se puede agregar al carrito		Validar mensaje de error, no agregar al carrito
FUNC-VENTA-03	Ventas	Finalizar venta al contado	E2E Auto	Caja Negra	Prueba de Flujo Completo (E2E)	CRÍTICA	Tarazona + Rivera	28/10	PASS	Venta realizada		Descontar stock, registrar venta, generar comprobante
FUNC-VENTA-04	Ventas	Finalizar venta a crédito	Manual	Caja Negra	Tabla de Decisión (Tipo Pago)	ALTA	Tarazona Narciso	28/10	PASS	Venta de crédito 		Registrar saldo pendiente, fecha vencimiento
FUNC-VENTA-05	Ventas	Realizar un abono a una venta a crédito	Manual	Caja Negra	Partición de Equivalencia (Abono Válido)	ALTA	Tarazona Narciso	28/10	PASS	realizadaFuncion no abordada por el sistema		Reducir saldo pendiente, registrar pago
FUNC-VENTA-06	Ventas	Anular una venta al contado	E2E Auto	Caja Negra	Prueba de Estado (Anulación)	ALTA	Tarazona + Rivera	28/10	PASS	Venta anulada correctamente		Restaurar stock, cambiar estado venta
FUNC-VENTA-07	Ventas	Anular una venta a crédito	Manual	Caja Negra	Prueba de Estado (Anulación)	MEDIA	Tarazona Narciso	28/10	PASS	Venta de crédito anulada correctamente		Restaurar stock, cancelar saldo pendiente
FUNC-VENTA-08	Ventas	Crear un apartado de producto	Manual	Caja Negra	Prueba de Estado (Apartado)	MEDIA	Tarazona Narciso	28/10	PASS	Apartado creado correctamente 		Reservar producto, no descontar stock aún
FUNC-VENTA-09	Ventas	Liquidar un apartado (convertir en venta)	Manual	Caja Negra	Prueba de Transición (Apartado→Venta)	MEDIA	Tarazona Narciso	28/10	PASS	Apartado liquidado exitosamente		Descontar stock, generar venta
FUNC-CAJA-01	Caja	Abrir caja con un monto inicial	Manual	Caja Negra	Prueba de Estado (Apertura Caja)	ALTA	Huaman Cardenas	28/10	PASS	Caja configurada exitosamente		Registrar apertura, monto inicial
FUNC-CAJA-02	Caja	Realizar un arqueo de caja (cierre)	Manual	Caja Negra	Prueba de Estado (Cierre Caja)	ALTA	Huaman Cardenas	28/10	PASS	Cierre de caja realizado con éxito		Calcular total, registrar cierre
FUNC-CAJA-03	Caja	Intentar realizar una venta con la caja cerrada	E2E Auto	Caja Negra	Prueba de Seguridad (Validación Estado)	ALTA	Huaman Cardenas	28/10	PASS	La vente no puede ser realizada		Validar mensaje de error, bloquear venta
DYN-PERF-01	Performance	Tiempo de Respuesta (Login): < 500ms promedio	Performance	Caja Gris	Prueba de Performance (Tiempo Respuesta)	ALTA	Bardales Rojas	28/10	PASS	Tiempo de respuesta por dabajo de del promedio, bastante bueno		Docker + ShellScript: 50 usuarios, medir tiempo respuesta
DYN-PERF-02	Performance	Tiempo de Respuesta (Búsqueda Productos): < 300ms	Performance	Caja Gris	Prueba de Performance (Tiempo Respuesta)	ALTA	Bardales Rojas	28/10	PASS	

Tiempo de respuesta por dabajo de del promedio, bastante bueno
        Docker + ShellScript: 
DYN-PERF-03	Performance	Prueba de Carga: 50 usuarios concurrentes (10 min)	Performance	Caja Gris	Prueba de Carga (Usuarios Concurrentes)	ALTA	Bardales Rojas	28/10	PASS	Servidor corriendo perfectamente		Docker + ShellScript: mantener 50 usuarios activos 10 min
DYN-PERF-04	Performance	Prueba de Resistencia: 20 usuarios (1 hora)	Performance	Caja Gris	Prueba de Estrés (Carga Sostenida)	MEDIA	Bardales Rojas	28/10	NOT RUN		Servidor activo y coerirendo , prueba exitosa		JMeter: carga sostenida 1 hora
DYN-PERF-05	Performance	Prueba de Picos: 10 a 100 usuarios en 1 minuto	Performance	Caja Gris	Prueba de Picos (Carga Súbita)	MEDIA	Bardales Rojas	28/10	PASS	

Servidor funcionando con nromalidad
        JMeter: incremento súbito de carga
DYN-BEH-01	Comportamiento	Análisis de Consultas BD (Problema N+1)	Performance	Caja Blanca	Análisis de Código (Optimización Queries)	ALTA	Huaman Cardenas	28/10	PASS	
Consultas redundantes 
        Monitorear queries con Laravel Telescope/logs
DYN-BEH-02	Comportamiento	Consumo de Memoria y CPU durante carga	Performance	Caja Gris	Monitoreo de Recursos (CPU/RAM)	ALTA	Huaman Cardenas	28/10	PASS	Consumo 
medio de los recuros 		Usar htop/top durante pruebas de carga
DYN-BEH-03	Comportamiento	Validación de Flujos Funcionales (casos E2E)	E2E Auto	Caja Negra	Prueba de Flujo Completo (E2E)	CRÍTICA	Huaman Cardenas	28/10	PASS	NOT RUN		Verificar integridad de flujos completos
