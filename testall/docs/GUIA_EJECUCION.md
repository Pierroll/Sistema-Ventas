## Guía de Ejecución de Pruebas E2E

Sigue estos pasos para ejecutar la suite de pruebas automatizadas con Playwright.

### 1. Verificar Entorno Local

- **Servidor Web**: Asegúrate de que tu servidor local (XAMPP, WAMP, etc.) esté en ejecución.
- **Aplicación**: La aplicación de ventas debe ser accesible en `http://localhost/venta`.

### 2. Verificar Base de Datos

- **Usuarios de Prueba**: Confirma que los usuarios de prueba existen en tu base de datos. Si no, créalos:
  - **Admin**: `admin@test.com` / `Admin123!`
  - **Vendedor**: `vendedor@test.com` / `Vendedor123!`

### 3. Instalar Dependencias

Navega a la carpeta `testall` y ejecuta:

```bash
npm install
```

### 4. Ejecutar Global Setup (Login)

Este paso autentica al usuario administrador y guarda la sesión para que los tests no tengan que hacer login repetidamente.

```bash
npm run setup
```

Deberías ver un mensaje de éxito en la consola. Si falla, revisa las credenciales en `fixtures/testData.js` y el estado de tu servidor.

### 5. Ejecutar los Tests

Puedes ejecutar los tests de varias maneras:

- **Todos los tests (headless)**:
  ```bash
  npm test
  ```

- **Todos los tests (con navegador visible)**:
  ```bash
  npm run test:headed
  ```

- **Modo UI (interactivo)**:
  ```bash
  npm run test:ui
  ```

- **Solo tests de autenticación**:
  ```bash
  npm run test:auth
  ```

### 6. Ver Reportes

Después de la ejecución, se genera un reporte HTML. Para abrirlo, usa:

```bash
npm run report
```

### 7. Debugging

Si un test falla, puedes usar el modo debug de Playwright para inspeccionar paso a paso:

```bash
npm run test:debug
```
