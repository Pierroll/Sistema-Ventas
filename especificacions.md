# VENTAS-SISTEMA
## Product Specification Document

---

## 1. Información General del Producto

**Nombre del Producto:** VENTAS-SISTEMA

**Descripción Ejecutiva:**
VENTAS-SISTEMA es una aplicación web de gestión integral de ventas y operaciones comerciales diseñada para empresas de mediano tamaño. Permite gestionar de manera centralizada todas las operaciones de venta, compra, inventario, clientes y generación de reportes, optimizando procesos administrativos y comerciales.

**Propósito Principal:**
Automatizar y centralizar la gestión de operaciones comerciales (ventas, compras, inventario, clientes) mediante una plataforma web moderna, mejorando la eficiencia operativa, reduciendo errores manuales y proporcionando visibilidad en tiempo real de las transacciones comerciales.

---

## 2. Características Principales

### 2.1 Gestión de Ventas
- Registro y seguimiento de ventas por transacción
- Generación de cotizaciones con opciones de aprobación
- Historial detallado de ventas por período
- Sistema de créditos y abonos para clientes
- Generación de documentos (facturas, recibos) en PDF
- Códigos QR para trazabilidad de productos/transacciones
- Seguimiento de apartados y reservas

### 2.2 Gestión de Inventario y Compras
- CRUD completo de productos con categorías y medidas
- Registro de compras y gestión de proveedores
- Control de inventario en tiempo real
- Historial de movimientos de inventario
- Integración con sistema de ventas para actualización automática

### 2.3 Gestión de Clientes
- Base de datos centralizada de clientes (activos e inactivos)
- Información de contacto y datos comerciales
- Historial de compras por cliente
- Seguimiento de créditos y deudas
- Estado de actividad/inactividad

### 2.4 Gestión de Cajas y Control Financiero
- Control de caja con arqueos diarios/periódicos
- Registro de apartados y reservas
- Monedas múltiples
- Reportes de flujo de efectivo
- Auditoría de transacciones en caja

### 2.5 Administración del Sistema
- Gestión de usuarios con permisos granulares
- Control de perfiles de acceso (roles)
- Módulo de administración general
- Listados de registros inactivos para auditoría
- Configuración de parámetros del sistema

### 2.6 Reportes y Exportación
- Generación de reportes en múltiples formatos (PDF, Excel)
- Exportación de datos a Excel para análisis adicional
- Reportes por período, cliente, producto y vendedor
- Análisis de calidad de código automático (SonarQube)

---

## 3. Cómo Funciona el Sistema

### 3.1 Flujo de Ventas
1. Usuario accede al módulo de ventas
2. Sistema carga catálogo de productos disponibles
3. Usuario selecciona productos y cantidades
4. Sistema calcula totales automáticamente
5. Usuario genera cotización (opcional)
6. Cotización es revisada y aprobada
7. Se registra la venta en BD
8. Se genera documento (factura/recibo) en PDF con QR
9. Se actualiza automáticamente el inventario

### 3.2 Flujo de Compras
1. Usuario accede al módulo de compras
2. Selecciona proveedor de lista registrada
3. Especifica productos y cantidades a comprar
4. Sistema registra la compra
5. Se actualiza automáticamente el inventario
6. Se genera documentación de compra (PDF)

### 3.3 Flujo de Control de Caja
1. Vendedor/Cajero accede al módulo de cajas
2. Sistema muestra saldo anterior
3. Registra transacciones (ingresos/egresos)
4. Al cierre del día: genera arqueo
5. Compara montos teóricos vs reales
6. Genera reporte de discrepancias si las hay

### 3.4 Flujo de Administración
1. Administrador accede al panel de admin
2. Gestiona usuarios, permisos y perfiles
3. Configura parámetros del sistema
4. Revisa reportes de auditoría
5. Accede a análisis de calidad de código

---

## 4. Arquitectura Técnica

### 4.1 Stack Tecnológico
- **Backend:** PHP con arquitectura MVC personalizada
- **Base de Datos:** MySQL
- **Frontend:** HTML5, CSS3 (Bootstrap), JavaScript (jQuery)
- **Componentes UI:** Bootstrap, DataTables, SweetAlert2
- **Generación de Documentos:** FPDF (PDF), PHPSpreadsheet (Excel), PHPQRCode (QR)
- **Control de Versiones:** Git
- **Testing:** PHPUnit
- **Calidad de Código:** SonarQube
- **Gestor de Dependencias:** Composer

### 4.2 Estructura de Carpetas
```
VENTAS-SISTEMA/
├── venta/                  # Aplicación principal
│   ├── Config/            # Configuración, conexión BD, helpers
│   ├── Controllers/        # Controladores (lógica de negocio)
│   ├── Models/            # Modelos (acceso a datos)
│   ├── Views/             # Plantillas PHP por módulo
│   ├── Libraries/         # FPDF, QRCode, etc.
│   ├── assets/            # CSS, JS, imágenes, fuentes
│   ├── tests/             # Pruebas unitarias (PHPUnit)
│   └── vendor/            # Dependencias Composer
├── composer.json          # Dependencias del proyecto
├── phpunit.xml.dist       # Configuración de pruebas
├── sonar-project.properties # Análisis de calidad
└── .gitignore, .htaccess  # Configuración
```

### 4.3 Principales Módulos de Código
- **Controllers:** Ventas, Compras, Clientes, Productos, Cajas, Usuarios, Proveedores, Admin
- **Models:** VentasModel, ComprasModel, ClientesModel, ProductosModel, etc.
- **Views:** Plantillas organizadas por módulo con modales para CRUD

---

## 5. Usuarios Finales y Casos de Uso

### 5.1 Perfiles de Usuarios
- **Administrador:** Acceso total, gestión de usuarios, configuración, reportes
- **Vendedor:** Registro de ventas, cotizaciones, generación de documentos
- **Cajero:** Control de caja, arqueos, apartados
- **Encargado de Compras:** Registro de compras, gestión de proveedores
- **Gerente/Supervisor:** Acceso a reportes, análisis, historial

### 5.2 Casos de Uso Principales
1. Registrar una venta completa con generación de factura
2. Crear y aprobar cotizaciones
3. Actualizar inventario automáticamente
4. Generar reportes de ventas por período
5. Exportar datos a Excel para análisis
6. Realizar arqueo de caja
7. Crear nuevos usuarios con permisos específicos

---

## 6. Requisitos No Funcionales

- **Rendimiento:** Carga rápida de módulos, respuesta < 2 segundos
- **Seguridad:** Sanitización de inputs, validación de permisos, auditoría de acciones
- **Escalabilidad:** Diseño preparado para múltiples sucursales/cajas
- **Disponibilidad:** Aplicación web accesible desde navegadores modernos
- **Mantenibilidad:** Código documentado, pruebas unitarias, análisis de calidad continuo
- **Compatibilidad:** Funciona en Chrome, Firefox, Safari, Edge

---

## 7. Integraciones Existentes

- **PHPSpreadsheet:** Exportación y lectura de archivos Excel
- **FPDF:** Generación de documentos PDF
- **PHPQRCode:** Generación de códigos QR
- **HTMLPurifier:** Sanitización de contenido

---

## 8. Métricas de Éxito

- Reducción en tiempo de registro de transacciones (>50%)
- Disminución de errores manuales en inventario
- Disponibilidad del sistema >99%
- Cero deudas técnicas críticas según SonarQube
- Cobertura de pruebas >80%
- Satisfacción del usuario >90%

---

## 9. Notas Adicionales

- El proyecto utiliza Git con historial extenso (~1000+ commits)
- Implementa análisis automático de calidad de código (SonarQube)
- Incluye suite completa de pruebas automatizadas
- Soporta múltiples monedas y configuraciones
- Estructura MVC personalizada sin framework externo
