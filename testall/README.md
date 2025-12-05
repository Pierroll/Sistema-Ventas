# Suite de Pruebas E2E para Sistema de Ventas

Este proyecto contiene la suite de pruebas automatizadas End-to-End (E2E) para el Sistema de Ventas, utilizando Playwright.

## Requisitos Previos

- **Node.js**: Versión 16 o superior.
- **Servidor PHP**: Un entorno como XAMPP, WAMP o similar, con el sistema de ventas corriendo en `http://localhost/venta`.
- **Base de Datos**: MySQL con los datos de prueba necesarios, incluyendo los usuarios definidos en `testall/fixtures/testData.js`.

## Instalación

1. Clona el repositorio (si aplica).
2. Navega a la carpeta `testall`:
   ```bash
   cd testall
   ```
3. Instala las dependencias de Node.js:
   ```bash
   npm install
   ```

## Ejecución de Pruebas

Esta suite utiliza un `globalSetup` para autenticarse una única vez como administrador, guardando el estado de la sesión. Esto acelera la ejecución de los tests.

**Paso 1: Generar estado de autenticación**

```bash
npm run setup
```

**Paso 2: Ejecutar los tests**

- **Ejecutar todos los tests en modo headless**:
  ```bash
  npm test
  ```

- **Ejecutar todos los tests con navegador visible**:
  ```bash
  npm run test:headed
  ```

- **Ejecutar en modo UI para una experiencia interactiva**:
  ```bash
  npm run test:ui
  ```

- **Ejecutar un subconjunto de tests (ej. autenticación)**:
  ```bash
  npm run test:auth
  ```

- **Ver el reporte HTML de la última ejecución**:
  ```bash
  npm run report
  ```

## Estructura del Proyecto

- `e2e/`: Contiene los archivos de tests (`.spec.js`), organizados por módulos del sistema.
- `pages/`: Contiene los Page Object Models (POM), que abstraen la interacción con las páginas.
- `helpers/`: Clases de utilidad para tareas comunes como manejar pop-ups o esperas AJAX.
- `fixtures/`: Datos de prueba reutilizables, como credenciales de usuario.
- `docs/`: Documentación de soporte, como el diccionario de selectores.
- `globalSetup.js`: Script que se ejecuta antes de todos los tests para realizar el login.
- `playwright.config.js`: Archivo de configuración principal de Playwright.

## Troubleshooting

- **Error en `globalSetup`**: Asegúrate de que el servidor web esté corriendo y que las credenciales en `fixtures/testData.js` sean correctas.
- **Tests fallan por timeouts**: Verifica que la aplicación responde rápido. Puedes ajustar los timeouts en `playwright.config.js` si tu entorno es lento.
- **`storageState.json` no se crea**: Revisa los logs de la consola al ejecutar `npm run setup` para ver los errores detallados.
