#!/bin/bash

# Test Case ID: DYN-PERF-04
# Descripción: Prueba de Resistencia - Mantener una carga de 20 usuarios durante 1 hora.

# --- Configuración ---
DURATION_MINUTES=60
TEST_FILE="DYN-PERF-02_Tiempo_Respuesta_Busqueda.js" # Usaremos la búsqueda de productos, que es más representativa.
NUM_WORKERS=20 # Número de "usuarios" para la carga sostenida.

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
    while true; do
        node "$TEST_FILE" > /dev/null 2>&1 # Suprimimos la salida.
        # Pausa más larga para simular un ritmo más realista en una prueba de resistencia.
        sleep 5 
    done
}

# --- Script Principal ---
echo "--- Iniciando Prueba de Resistencia (DYN-PERF-04) ---"
echo "Duración: ${DURATION_MINUTES} minutos"
echo "Usuarios Concurrentes (Workers): ${NUM_WORKERS}"
echo "Prueba base: ${TEST_FILE}"
echo "----------------------------------------------------"
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

# Dejar que la prueba se ejecute
sleep $((DURATION_MINUTES * 60))

# --- Finalización de la Prueba ---
echo ""
echo "--- Finalizando la Prueba de Resistencia ---"
echo "Deteniendo a los ${NUM_WORKERS} workers..."

for pid in "${worker_pids[@]}"; do
    if ps -p "$pid" > /dev/null; then
        kill "$pid"
    fi
done

wait

echo "✅ Prueba de resistencia (DYN-PERF-04) finalizada."
echo "Si ejecutaste el monitor de recursos, detenlo ahora (Ctrl+C) para ver los promedios."
