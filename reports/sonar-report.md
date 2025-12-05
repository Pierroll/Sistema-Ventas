---
title: Informe de Calidad de Código
subtitle: php-mvc-local
date: 13/10/2025 09:57
---

# Resumen Ejecutivo

**Proyecto:** php-mvc-local  
**Quality Gate:** **OK**

## Métricas Principales

### Confiabilidad y Seguridad
- **Bugs:** 9
- **Vulnerabilidades:** 0
- **Hotspots de Seguridad:** 0
- **Reliability Rating:** 2.0/5
- **Security Rating:** 1.0/5

### Mantenibilidad
- **Code Smells:** 245
- **Technical Debt Rating:** 1.0/5

### Tamaño del Proyecto
- **Líneas de código:** 5135
- **Archivos:** 43
- **Funciones:** 393

### Calidad de Código
- **Cobertura de tests:** 89.0%
- **Duplicación:** 14.2%
- **Tests totales:** N/A
- **Tasa de éxito:** N/A%

\newpage

# Issues Críticos

## CRITICAL - CODE_SMELL
**Regla:** php:S6600  
**Archivo:** `Controllers/Usuarios.php` (línea ?)  
**Mensaje:** Remove the parentheses from this "return" call.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Usuarios.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "Todos los campos son obligatorios" 4 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Usuarios.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "Las contraseñas no coinciden" 3 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Usuarios.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "location: " 7 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Usuarios.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 16 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `index.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "Location: " 3 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Administracion.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 40 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Administracion.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 18 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Administracion.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 21 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Ventas.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 16 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S3776  
**Archivo:** `Controllers/Ventas.php` (línea ?)  
**Mensaje:** Refactor this function to reduce its Cognitive Complexity from 23 to the 15 allowed.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Ventas.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "No hay Stock, te quedan " 3 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Ventas.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "Error al anular la venta" 3 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S1192  
**Archivo:** `Controllers/Ventas.php` (línea ?)  
**Mensaje:** Define a constant instead of duplicating this literal "Venta no encontrada" 3 times.

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Config/Config.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S1186  
**Archivo:** `Models/HomeModel.php` (línea 3)  
**Mensaje:** Add a nested comment explaining why this method is empty, throw an Exception or complete the implementation.

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Libraries/phpqrcode/qrtools.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Libraries/phpqrcode/qrtools.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Libraries/phpqrcode/qrtools.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).

## CRITICAL - CODE_SMELL
**Regla:** php:S121  
**Archivo:** `Libraries/phpqrcode/qrtools.php` (línea ?)  
**Mensaje:** Add curly braces around the nested statement(s).


\newpage

# Todos los Issues (Top 30)

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Config/App/Autoload.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Models/AdministracionModel.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | BUG
- **Regla:** php:S2003
- **Archivo:** `Config/App/Autoload.php`
- **Línea:** ?
- **Mensaje:** Replace "require" with "require once".

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Config/App/Autoload.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Config/App/Autoload.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Config/App/Autoload.php`
- **Línea:** 5
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S113
- **Archivo:** `Controllers/traits/UsuariosAuthTrait.php`
- **Línea:** ?
- **Mensaje:** Add a new line at the end of this file.

### MINOR | CODE_SMELL
- **Regla:** php:S1131
- **Archivo:** `Controllers/Usuarios.php`
- **Línea:** ?
- **Mensaje:** Remove the useless trailing whitespaces at the end of this line.

### MINOR | CODE_SMELL
- **Regla:** php:S100
- **Archivo:** `Controllers/Usuarios.php`
- **Línea:** ?
- **Mensaje:** Rename function "is valid email" to match the regular expression ^[a-z][a-zA-Z0-9]*$.

### MINOR | BUG
- **Regla:** php:S2003
- **Archivo:** `Controllers/Usuarios.php`
- **Línea:** ?
- **Mensaje:** Replace "require" with "require once".


\newpage

# Hotspots de Seguridad


\newpage

# Archivos con Baja Cobertura


\newpage

# Historial de Análisis


---

_Generado desde: http://localhost:9000_
