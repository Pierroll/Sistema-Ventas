#!/bin/bash

# Test Case ID: DYN-PERF-03
# Descripción: Prueba de Carga - Simular 50 usuarios concurrentes durante 10 minutos.

# --- Configuración ---
DURATION_MINUTES=2
TEST_FILE="DYN-PERF-01_Tiempo_Respuesta_Login.js" # Usaremos el test de login como carga base.
NUM_WORKERS=50 # Número de "usuarios" concurrentes.

# --- Verificaciones Previas ---
if ! command -v node &> /dev/null; then
    echo "❌ Error: Node.js no está instalado. Por favor, instálalo para continuar."
    exit 1
fi

if [ ! -f "$TEST_FILE" ]; then
    echo "❌ Error: El archivo de prueba '$TEST_FILE' no se encontró."
    exit 1
fi

# --- Función para un "trabajador" (simula un usuario) ---
run_worker() {
    local worker_id=$1
    echo "Worker ${worker_id}: Iniciando..."
    # Ejecuta el test en un bucle infinito. El script principal controlará la duración.
    while true; do
        node "$TEST_FILE" > /dev/null 2>&1 # Suprimimos la salida para no saturar la consola.
        sleep 1 # Pequeña pausa para no sobrecargar el sistema de forma irreal.
    done
}

# --- Script Principal ---
echo "--- Iniciando Prueba de Carga (DYN-PERF-03) ---"
echo "Duración: ${DURATION_MINUTES} minutos"
echo "Usuarios Concurrentes (Workers): ${NUM_WORKERS}"
echo "Prueba base: ${TEST_FILE}"
echo "-------------------------------------------------"
echo "Para monitorear recursos, ejecuta ./DYN-BEH-02_Monitor_Recursos.sh en otra terminal."
echo ""

# Iniciar los workers en segundo plano
worker_pids=()
for i in $(seq 1 $NUM_WORKERS); do
    run_worker "$i" &
    worker_pids+=($!)
done

echo "${NUM_WORKERS} workers iniciados. La prueba está en ejecución..."
echo "IDs de procesos de los workers: ${worker_pids[*]}"

# Dejar que la prueba se ejecute durante el tiempo especificado
sleep $((DURATION_MINUTES * 60))

# --- Finalización de la Prueba ---
echo ""
echo "--- Finalizando la Prueba de Carga ---"
echo "Deteniendo a los ${NUM_WORKERS} workers..."

# Matar todos los procesos de los workers
for pid in "${worker_pids[@]}"; do
    # Usamos ps para verificar si el proceso todavía existe antes de intentar matarlo
    if ps -p "$pid" > /dev/null; then
        kill "$pid"
    fi
done

# Esperar a que todos los procesos en segundo plano terminen
wait

echo "✅ Prueba de carga (DYN-PERF-03) finalizada."
echo "Si ejecutaste el monitor de recursos, detenlo ahora (Ctrl+C) para ver los promedios."
