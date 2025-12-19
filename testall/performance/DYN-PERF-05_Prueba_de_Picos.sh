#!/bin/bash

# Test Case ID: DYN-PERF-05
# Descripción: Prueba de Picos - Incrementar de 10 a 100 usuarios en 1 minuto.

# --- Configuración ---
INITIAL_USERS=10
FINAL_USERS=100
RAMP_UP_TIME_SECONDS=60
TEST_FILE="DYN-PERF-01_Tiempo_Respuesta_Login.js" # Usaremos el test de login, ya que es una acción rápida.

# --- Verificaciones Previas ---
if ! command -v node &> /dev/null;
    then
    echo "❌ Error: Node.js no está instalado. Por favor, instálalo para continuar."
    exit 1
fi

if [ ! -f "$TEST_FILE" ];
    then
    echo "❌ Error: El archivo de prueba '$TEST_FILE' no se encontró."
    exit 1
fi

# --- Función para ejecutar una sola petición (simula un usuario por un momento) ---
run_request() {
    # Ejecuta el script de node una vez y termina.
    node "$TEST_FILE"
}

# --- Script Principal ---
echo "--- Iniciando Prueba de Picos (DYN-PERF-05) ---"
echo "Usuarios iniciales: ${INITIAL_USERS}"
echo "Usuarios finales: ${FINAL_USERS}"
echo "Tiempo de incremento: ${RAMP_UP_TIME_SECONDS} segundos"
echo "------------------------------------------------"
echo "Abre otra terminal y prepárate para ejecutar ./DYN-BEH-02_Monitor_Recursos.sh"
read -p "Presiona Enter para comenzar la prueba..."

echo "Iniciando monitoreo y prueba de picos..."
# Inicia el monitor de carga en segundo plano
./DYN-BEH-02_Monitor_Recursos.sh &
MONITOR_PID=$!

# Atrapar Ctrl+C para detener el monitor si el script se interrumpe
trap "echo -e '\nInterrupción detectada, deteniendo monitor...'; kill ${MONITOR_PID}; exit 1" INT

# --- Lógica de la Prueba de Picos ---
total_users_to_add=$((FINAL_USERS - INITIAL_USERS))
# Calculamos en cuántos "pasos" o "tandas" vamos a añadir usuarios.
# Añadiremos un grupo de usuarios cada 6 segundos durante un minuto (10 tandas).
num_steps=10
interval_seconds=$((RAMP_UP_TIME_SECONDS / num_steps))
users_per_step=$((total_users_to_add / num_steps))

echo "Se añadirán ${users_per_step} usuarios cada ${interval_seconds} segundos."

# Empezamos con la carga base
echo "Estableciendo carga base de ${INITIAL_USERS} usuarios..."
for i in $(seq 1 $INITIAL_USERS);
    do
    run_request &
done

# Incremento sóbito de carga
for step in $(seq 1 $num_steps);
    do
    echo "Paso ${step}/${num_steps}: Añadiendo ${users_per_step} usuarios más..."
    for i in $(seq 1 $users_per_step);
        do
        run_request &
done
sleep $interval_seconds
done

echo "Pico de carga alcanzado. Esperando a que las óltimas peticiones terminen..."
wait # Espera a que todos los procesos en segundo plano finalicen.

echo ""
echo "--- Finalizando la Prueba de Picos ---"
# Detener el monitor de carga
kill $MONITOR_PID
wait $MONITOR_PID 2>/dev/null # Esperar a que el monitor termine y calcule promedios

echo "✅ Prueba de picos (DYN-PERF-05) finalizada."
echo "Revisa los resultados del monitor en la consola y en el archivo CSV generado."
