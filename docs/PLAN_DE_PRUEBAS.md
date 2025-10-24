# Plan de Pruebas Exhaustivo — VENTAS-SISTEMA (ISO/IEC 25010)

Versión: 1.0  
Fecha: 2025-10-24  
Alcance: Aplicación PHP MVC (Controllers, Models, Views) en `/workspace`

---

## 1. Introducción

### 1.1 Objetivo
Asegurar la calidad del sistema VENTAS-SISTEMA mediante un plan de pruebas enfocado en:
- **Idoneidad funcional (ISO/IEC 25010):** verificar que las funciones implementadas satisfacen los requisitos especificados de forma completa y correcta.
- **Mantenibilidad (ISO/IEC 25010):** controlar que el código sea fácil de analizar, modificar, probar y evolucionar, con límites de complejidad y deuda técnica.

### 1.2 Documentos y artefactos relacionados
- Requisitos funcionales (epics y módulos: Autenticación, Usuarios, Clientes, Productos/Categorías/Medidas, Compras, Ventas, Apartados, Créditos, Caja). 
- Estructura del proyecto (PHP MVC): `Controllers/`, `Models/`, `Views/`, `Config/`.
- Esquema de BD y semillas de prueba (ver sección 7).

---

## 2. Alcance de las pruebas

### 2.1 Cuadro resumen de funcionalidades incluidas
- Autenticación y control de acceso por rol (Admin, Vendedor).
- Gestión de Usuarios, Clientes, Productos, Categorías, Medidas.
- Compras y actualización de inventario.
- Ventas (contado y crédito), Apartados, Abonos, Anulación.
- Gestión de Caja (apertura, cierre, validaciones).

### 2.2 Requerimientos excluidos (por ahora)
- Integraciones externas no presentes en el repo (pasarelas de pago, webhooks).
- Pruebas de rendimiento a gran escala y pruebas de seguridad avanzadas (se planifican en otro documento).

### 2.3 Casos de prueba incluidos
- Ver sección 5 (Pruebas funcionales) para el detalle de suites y casos.

### 2.4 Criterios de no cobertura actual (con justificación)
- Funciones o pantallas no accesibles desde la navegación de `Views/` actual: quedarán documentadas y se planifican para ciclo siguiente.

---

## 3. Entorno y configuración de las pruebas

### 3.1 Criterios de inicio
- PHP ≥ 8.1, Composer instalado.
- BD MySQL/MariaDB dedicada para pruebas (vacía o reseteable).  
- Variables de entorno de conexión de pruebas configuradas en `Config/Config.php`.
- Dependencias de desarrollo instaladas (ver sección 6.2).

### 3.2 Base de datos de pruebas
- Nombre sugerido: `ventas_sistema_test`.
- Estrategia: “reset por suite” (re-crear y sembrar datos base antes de cada suite de integración/E2E).
- Semillas mínimas: usuarios admin y vendedor, 1 proveedor, 2 clientes, 2 categorías, 2 medidas, 3 productos con distinto stock, 1 caja abierta.  
  Ver semillas sugeridas en la sección 7.

### 3.3 Criterios de aprobación / rechazo
- No debe haber errores críticos en pruebas estáticas (análisis y estilo): 0 errores; se permiten advertencias menores documentadas.
- Cobertura mínima por línea (Models críticos): 80% (`VentasModel`, `ProductosModel`, `UsuariosModel`).
- Complejidad ciclomática: ningún método > 15.
- Incidentes funcionales severidad Alta = 0 abiertos al cierre del ciclo.

---

## 4. Estrategia de pruebas

### 4.1 Enfoque y niveles
- **Estático (Mantenibilidad)**: estándares de código, análisis estático, duplicación, complejidad, arquitectura.
- **Unitarias (Models)**: pruebas de lógica de negocio, dobles de test para la BD cuando aplique.
- **Integración (Controllers↔Models↔BD)**: rutas críticas, validaciones y flujos.
- **End-to-End (E2E)**: flujos completos en navegador para perfiles Admin/Vendedor.
- **Regresión**: suites automáticas corren en cada commit a la rama del equipo.

### 4.2 Orden de ejecución
1) Estático → 2) Unitarias → 3) Integración → 4) E2E → 5) Mutación → 6) Reportes.

### 4.3 Equipo y responsabilidades (5 integrantes)
- QA Lead: planificación, gobernanza de métricas, revisión final y criterios de salida.
- QA Funcional: diseño y ejecución de casos funcionales manuales y exploratorios.
- QA Automatización (PHPUnit/Integración): construcción de unitarias e integración.
- QA E2E: automatización de flujos de UI y datos de prueba.
- QA Estático/Mantenibilidad: análisis estático, estilo, complejidad, cobertura de arquitectura.

---

## 5. Pruebas de Idoneidad Funcional (selección concreta)

A continuación, una síntesis operativa de los casos de prueba clave. La redacción completa de pasos y datos se incluirá en las suites automatizadas y/o listas de verificación manuales.

### 5.1 Autenticación y Acceso
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-AUTH-01 | Login exitoso (Admin) | E2E Auto | Alta | Redirección a dashboard admin |
| FUNC-AUTH-02 | Login exitoso (Vendedor) | E2E Auto | Alta | Redirección a módulo de ventas |
| FUNC-AUTH-03 | Login fallido por contraseña | E2E Auto | Alta | Mensaje “Usuario o contraseña incorrecta” |
| FUNC-AUTH-06 | Logout invalida sesión | E2E Auto | Alta | Redirección a login y bloqueo de rutas |

### 5.2 Usuarios y Roles
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-USER-01 | Crear usuario rol Vendedor | Manual | Alta | Usuario aparece en lista y puede iniciar sesión |
| FUNC-USER-02 | Correo duplicado | Integración Auto | Alta | Error “El usuario ya existe” |
| FUNC-USER-07 | Vendedor accede a gestión de usuarios | E2E Auto | Alta | Acceso denegado (403 o redirección) |

### 5.3 Clientes
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-CLI-01 | Crear cliente válido | Manual | Alta | Cliente activo en listado |
| FUNC-CLI-02 | DNI/RUC duplicado | Integración Auto | Alta | Error de duplicado |

### 5.4 Productos, Categorías, Medidas
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-PROD-01 | Crear producto válido | Integración Auto | Alta | Producto con stock inicial, categoría y medida |
| FUNC-PROD-02 | Código duplicado | Integración Auto | Alta | Error “El producto ya existe” |
| FUNC-PROD-03 | Editar precio de venta | E2E Auto | Alta | Nuevas ventas usan el precio nuevo |

### 5.5 Compras e Inventario
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-COMP-01 | Registrar compra | Manual | Alta | Stock incrementa y registro creado |
| FUNC-COMP-03 | PDF de compra | Manual | Media | Se genera PDF con detalles |

### 5.6 Ventas y Apartados
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-VENTA-01 | Agregar producto con stock | E2E Auto | Alta | Item se añade al detalle |
| FUNC-VENTA-02 | Agregar producto sin stock | E2E Auto | Alta | Mensaje “No hay Stock” |
| FUNC-VENTA-03 | Finalizar venta contado | E2E Auto | Alta | Registra venta, descuenta stock, PDF, afecta caja |
| FUNC-VENTA-06 | Anular venta contado | E2E Auto | Alta | Venta anulada y stock repuesto |

### 5.7 Caja
| ID | Descripción | Tipo | Prioridad | Resultado esperado |
|----|-------------|------|----------:|--------------------|
| FUNC-CAJA-01 | Abrir caja | Manual | Alta | Caja abierta con saldo inicial |
| FUNC-CAJA-03 | Vender con caja cerrada | E2E Auto | Alta | Bloqueo con mensaje “La caja esta cerrada” |

---

## 6. Pruebas de Mantenibilidad (estáticas y automatizadas)

### 6.1 Métricas y criterios de aceptación
- **Deuda técnica**: objetivo < 5 días (medida indirecta via issues/violaciones y phpmetrics opcional).
- **Complejidad ciclomática**: ningún método > 15.
- **Estándares de código**: PSR-12 sin errores (0 errores; advertencias justificadas).
- **Cobertura de línea**: mínimo 80% en `VentasModel`, `ProductosModel`, `UsuariosModel`.
- **Duplicación**: < 3% en `Controllers/` y `Models/`.
- **Arquitectura**: `Models` no deben instanciar otros `Models` directamente (acoplamiento bajo); `Helpers` y `Config` sin dependencias hacia controladores específicos.
- **Calidad de tests**: Mutation Score (MSI) ≥ 60% en módulos críticos.

### 6.2 Herramientas recomendadas y comandos
Instalación de dev-tools (sugerencia):

```bash
composer require --dev \
  phpunit/phpunit:^10.5 \
  phpstan/phpstan:^1.11 \
  squizlabs/php_codesniffer:^3.10 \
  phpmd/phpmd:^2.14 \
  sebastian/phpcpd:^6.0 \
  friendsofphp/php-cs-fixer:^3.58 \
  infection/infection:^0.27
```

Scripts sugeridos para `composer.json` (añadir sección `scripts`):

```json
{
  "scripts": {
    "qa:lint": "phpcs -p --standard=PSR12 Controllers Models Config",
    "qa:fix": "phpcbf --standard=PSR12 Controllers Models Config",
    "qa:md": "phpmd Controllers,Models text cleancode,codesize,design,naming,unusedcode",
    "qa:stan": "phpstan analyse Controllers Models Config --level=6",
    "qa:cpd": "phpcpd Controllers Models Config",
    "test": "phpunit --colors=always --testdox",
    "test:coverage": "phpunit --coverage-html build/coverage",
    "mutate": "infection --min-msi=60 --threads=4"
  }
}
```

Ejecución:

```bash
composer qa:lint
composer qa:stan
composer qa:md
composer qa:cpd
composer test
composer test:coverage
composer mutate
```

> Alternativa E2E basada en navegador: Playwright o Codeception. Ver sección 8.

---

## 7. Datos de prueba (semillas mínimas)

Utilizar estos datos como referencia para poblar `ventas_sistema_test`.

```sql
-- Usuarios
INSERT INTO usuarios (nombre, correo, password, rol, estado) VALUES
  ('Admin', 'admin@demo.local', SHA2('Admin#123',256), 'admin', 1),
  ('Vendedor', 'vend@demo.local', SHA2('Vend#123',256), 'vendedor', 1);

-- Clientes
INSERT INTO clientes (nombre, doc, direccion, estado) VALUES
  ('Cliente Contado', 'DNI0001', 'Calle 1', 1),
  ('Cliente Crédito', 'DNI0002', 'Calle 2', 1);

-- Proveedor
INSERT INTO proveedor (nombre, ruc, direccion, estado) VALUES
  ('Proveedor SA', 'RUC-0001', 'Av. 123', 1);

-- Categorías y Medidas
INSERT INTO categorias (nombre, estado) VALUES ('Bebidas',1),('Snacks',1);
INSERT INTO medidas (nombre, estado) VALUES ('Unidad',1),('Caja',1);

-- Productos (stock variado)
INSERT INTO productos (codigo, descripcion, precio_compra, precio_venta, id_categoria, id_medida, stock, estado) VALUES
  ('P001','Agua 600ml',3.00,5.00,1,1,50,1),
  ('P002','Galleta',1.50,2.50,2,1,0,1),
  ('P003','Agua 2L',5.00,8.00,1,2,10,1);

-- Caja abierta
INSERT INTO cajas (monto_inicial, estado) VALUES (100.00, 'abierta');
```

---

## 8. Automatización E2E (opciones)

### Opción A — Playwright (Node.js)
- Requisitos: Node 18+.
- Instalación:
```bash
npm init -y
npm i -D @playwright/test
npx playwright install
```
- Ejemplo de prueba (flujo de venta contado):
```ts
import { test, expect } from '@playwright/test';

test('venta contado con stock', async ({ page }) => {
  await page.goto('http://localhost:8080');
  await page.fill('#email', 'vend@demo.local');
  await page.fill('#password', 'Vend#123');
  await page.click('button[type=submit]');
  await page.click('text=Ventas');
  await page.fill('#buscar-producto', 'P001');
  await page.click('text=Agregar');
  await page.click('text=Finalizar Venta');
  await expect(page.locator('.toast-success')).toContainText('Venta registrada');
});
```

### Opción B — Codeception (PHP)
```bash
composer require --dev codeception/codeception
vendor/bin/codecept bootstrap
vendor/bin/codecept generate:suite acceptance
```
- Usar `PhpBrowser` o `WebDriver` según disponibilidad.

---

## 9. Matriz de trazabilidad (requisitos ↔ casos)

| Requisito (FR) | Descripción | Casos asociados |
|----------------|-------------|-----------------|
| FR-AUTH | Autenticación y roles | FUNC-AUTH-01, 02, 03, 06 |
| FR-USU | Gestión de usuarios | FUNC-USER-01, 02, 07 |
| FR-CLI | Gestión de clientes | FUNC-CLI-01, 02 |
| FR-PROD | Productos/Categorías/Medidas | FUNC-PROD-01, 02, 03 |
| FR-COMP | Compras/Inventario | FUNC-COMP-01, 03 |
| FR-VENT | Ventas/Apartados | FUNC-VENTA-01, 02, 03, 06 |
| FR-CAJA | Caja | FUNC-CAJA-01, 03 |

---

## 10. Cronograma y responsables (5 integrantes)

| Semana | Actividad | Responsable |
|--------|-----------|-------------|
| 1 | Instalación herramientas estáticas, linters y datos de prueba | QA Estático/Mantenibilidad |
| 1 | Diseño detallado de casos funcionales y listas de verificación | QA Funcional |
| 1-2 | Pruebas unitarias de `Models` críticos (80%+) | QA Automatización |
| 2 | Integración (Controllers↔Models↔BD) de módulos clave | QA Automatización |
| 2-3 | E2E de flujos: login, ventas contado, anulación, caja | QA E2E |
| 3 | Mutación, análisis de cobertura y remediación | QA Lead + Equipo |
| 3 | Reporte final, criterios de salida y sign-off | QA Lead |

RACI (resumen): QA Lead (A/R), cada especialista (R), equipo dev (C/I según hallazgos).

---

## 11. Criterios de inicio, salida y suspensión
- **Inicio**: Entorno preparado (sección 3), datos de prueba cargados.
- **Salida**: 100% de casos críticos ejecutados; 0 fallas severidad Alta; métricas de mantenibilidad dentro de umbrales; cobertura y mutación cumplidas.
- **Suspensión**: indisponibilidad del entorno, defectos bloqueantes en autenticación o navegación básica.

---

## 12. Gestión de defectos y reportes
- Registro en issues del repo con etiquetas: `bug`, `sev:alta|media|baja`, `area:modulo`.
- Evidencia: capturas, logs, queries, IDs de commit, versión del build.
- Reportes: diario (resumen de ejecución), semanal (tendencias y métricas), final (cumplimiento de criterios de salida).

---

## 13. Riesgos y mitigaciones
- Datos inconsistentes entre entornos → estandarizar semillas y "reset" antes de suites.
- Alto acoplamiento en código legacy → priorizar refactors pequeños guiados por PHPMD y pruebas.
- Falta de aislamiento en pruebas con BD → usar transacciones/rollback o re-crear el esquema por suite.

---

## 14. Apéndice — Escenarios clave (E2E “happy path”)

1) Login (Vendedor) → búsqueda producto con stock → agregar al carrito → finalizar venta contado → verificar afectación de caja y PDF.  
2) Intento con producto sin stock → mensaje de bloqueo → no altera caja.  
3) Anulación de venta contado → stock repuesto.  
4) Admin crea nuevo usuario vendedor → nuevo login exitoso.

---

Este plan es la guía operativa para asegurar que VENTAS-SISTEMA cumpla la idoneidad funcional y mantenga una alta mantenibilidad conforme a ISO/IEC 25010. Cualquier desviación de métricas o alcance debe registrarse y aprobarse por el QA Lead.
