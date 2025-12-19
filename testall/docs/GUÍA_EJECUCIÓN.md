# Guía de Ejecución de Pruebas de Performance

Este documento detalla cómo ejecutar cada uno de los scripts de prueba de performance ubicados en el directorio `testall/performance`.

**Nota Importante:** Antes de ejecutar los scripts, asegúrate de que tengan permisos de ejecución. Si no, ejecútalos con `chmod +x <nombre_del_script>`.

---

### DYN-BEH-02: Monitor de Consumo de Recursos

- **Descripción:** Monitorea y registra el consumo de CPU y memoria. Está diseñado para ejecutarse en segundo plano mientras se realizan otras pruebas. Al detenerlo (`Ctrl+C`), calcula y muestra los promedios.
- **Comando:**
  ```bash
  cd testall/performance
  ./DYN-BEH-02_Monitor_Recursos.sh
  ```
- **Nota:** Generalmente se ejecuta en una terminal separada.

---

### DYN-PERF-01: Tiempo de Respuesta (Login)

- **Descripción:** Mide el tiempo de respuesta del endpoint de login bajo la carga de 50 peticiones.
- **Comando:**
  ```bash
  node testall/performance/DYN-PERF-01_Tiempo_Respuesta_Login.js
  ```

---

### DYN-PERF-02: Tiempo de Respuesta (Búsqueda de Productos)

- **Descripción:** Mide el tiempo de respuesta del endpoint de búsqueda de productos tras iniciar sesión.
- **Comando:**
  ```bash
  node testall/performance/DYN-PERF-02_Tiempo_Respuesta_Busqueda.js
  ```

---

### DYN-PERF-03: Prueba de Carga (2 Minutos)

- **Descripción:** Simula una carga sostenida de 50 usuarios durante 2 minutos.
- **Comando:**
  ```bash
  cd testall/performance
  ./DYN-PERF-03_Prueba_de_Carga.sh
  ```
- **Nota:** Se recomienda ejecutar el `DYN-BEH-02_Monitor_Recursos.sh` en paralelo.

---

### DYN-PERF-04: Prueba de Resistencia (1 Hora)

- **Descripción:** Simula una carga sostenida de 20 usuarios durante 1 hora.
- **Comando:**
  ```bash
  cd testall/performance
  ./DYN-PERF-04_Prueba_de_Resistencia.sh
  ```
- **Nota:** Se recomienda ejecutar el `DYN-BEH-02_Monitor_Recursos.sh` en paralelo.

---

### DYN-PERF-05: Prueba de Picos

- **Descripción:** Simula un incremento súbito de 10 a 100 usuarios en un minuto.
- **Comando:**
  ```bash
  cd testall/performance
  ./DYN-PERF-05_Prueba_de_Picos.sh
  ```
- **Nota:** Este script intenta iniciar el monitor de recursos automáticamente.
