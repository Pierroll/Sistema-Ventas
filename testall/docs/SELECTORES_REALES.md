## SELECTORES POR MÓDULO

### Login (Views/index.php)
- Email: `input[name="correo"]` ✅ Línea 46
- Password: `input[name="clave"]` ✅ Línea 50
- Submit: `button[type="submit"]:has-text("Login")` ✅ Línea 56

### Dashboard (Views/templates/header.php)
- User Name: `.dropdown-toggle div` ⚠️ Frágil
- Logout: `[onclick="salir(event)"]` ✅ Línea 163

### Clientes (Views/clientes/index.php)
- Search Input: `input[type="search"]` (DataTables) ✅
- Table: `table tbody tr`

### Usuarios
- URL sin permisos redirect: `/administracion/permisos`
