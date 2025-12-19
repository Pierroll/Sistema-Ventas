#!/bin/bash

# Script para monitorear el consumo de CPU y Memoria en macOS durante una prueba de carga.
# Test Case ID: DYN-BEH-02
# Descripción: Consumo de Memoria y CPU durante carga - Consumo medio de los recursos.
# Nota: Este script está adaptado para macOS, ya que utiliza comandos como 'top -l'.

# --- Configuración ---
LOG_FILE="performance_monitor_mac_$(date +%Y%m%d_%H%M%S).csv"
INTERVAL=5

# --- Mensajes iniciales ---
echo "Iniciando monitoreo de CPU y Memoria (macOS) cada ${INTERVAL} segundos."
echo "Los datos se guardarán en: ${LOG_FILE}"
echo "Para detener el monitoreo, presiona Ctrl+C."
echo ""

# --- Encabezado del log CSV ---
echo "Timestamp,CPU_User_Pct,CPU_System_Pct,CPU_Idle_Pct,Memory_Used_MB,Memory_Free_MB" > "${LOG_FILE}"

# --- Función para convertir unidades de memoria a MB ---
convert_to_mb() {
    local value_raw=$1
    # Asegurarse de que el valor no esté vacío
    if [ -z "$value_raw" ]; then
        echo "0"
        return
    fi
    # Eliminar cualquier carácter no numérico/no punto al final
    local value_num=$(echo "$value_raw" | sed -e 's/[a-zA-Z]*$//')
    
    if [[ $value_raw == *"G"* ]]; then
        # Gigabytes a Megabytes
        echo "$value_num * 1024" | bc
    elif [[ $value_raw == *"M"* ]]; then
        # Megabytes
        echo "$value_num"
    elif [[ $value_raw == *"K"* ]]; then
        # Kilobytes a Megabytes
        echo "$value_num / 1024" | bc
    else
        # Asumiendo bytes, convertir a MB
        echo "$value_num / 1024 / 1024" | bc
    fi
}

# --- Función de monitoreo ---
monitor_resources() {
    while true; do
        TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")

        # Obtener estadísticas de una sola iteración de top
        TOP_STATS=$(top -l 1)

        # Extraer uso de CPU
        CPU_LINE=$(echo "${TOP_STATS}" | grep "CPU usage")
        CPU_USER=$(echo "${CPU_LINE}" | awk '{print $3}' | sed 's/%//')
        CPU_SYSTEM=$(echo "${CPU_LINE}" | awk '{print $5}' | sed 's/%//')
        CPU_IDLE=$(echo "${CPU_LINE}" | awk '{print $7}' | sed 's/%//')

        # Extraer uso de Memoria Física
        MEM_LINE=$(echo "${TOP_STATS}" | grep "PhysMem")
        MEM_USED_RAW=$(echo "${MEM_LINE}" | awk '{print $2}')
        MEM_FREE_RAW=$(echo "${MEM_LINE}" | awk '{print $6}')

        MEM_USED_MB=$(convert_to_mb "$MEM_USED_RAW")
        MEM_FREE_MB=$(convert_to_mb "$MEM_FREE_RAW")
        
        # Registrar datos
        echo "${TIMESTAMP},${CPU_USER},${CPU_SYSTEM},${CPU_IDLE},${MEM_USED_MB},${MEM_FREE_MB}" >> "${LOG_FILE}"

        sleep "${INTERVAL}"
    done
}

# --- Iniciar monitoreo y manejar interrupción ---
monitor_resources &
MONITOR_PID=$!

echo "Monitoreo iniciado (PID: ${MONITOR_PID}). Ejecuta tus pruebas de carga."
echo "Para ver datos en tiempo real (en otra terminal): tail -f ${LOG_FILE}"
echo "Cuando termines, presiona Ctrl+C para detener."

trap "echo -e '\nDeteniendo monitoreo...'; kill ${MONITOR_PID}; wait ${MONITOR_PID} 2>/dev/null; echo 'Monitoreo detenido. Datos en ${LOG_FILE}'; exit 0" INT

wait ${MONITOR_PID}

# --- Calcular promedios ---
echo -e "\n--- Calculando promedios de recursos ---"
# Saltar encabezado (NR>1), sumar columnas, luego dividir por el contador.
awk -F',' 'NR > 1 {
    cpu_user_sum += $2;
    cpu_system_sum += $3;
    cpu_idle_sum += $4;
    mem_used_sum += $5;
    mem_free_sum += $6;
    count++;
} END {
    if (count > 0) {
        printf "CPU Usuario Promedio: %.2f%%\n", cpu_user_sum / count;
        printf "CPU Sistema Promedio: %.2f%%\n", cpu_system_sum / count;
        printf "CPU Ocioso Promedio: %.2f%%\n", cpu_idle_sum / count;
        printf "Memoria Usada Promedio: %.2f MB\n", mem_used_sum / count;
        printf "Memoria Libre Promedio: %.2f MB\n", mem_free_sum / count;
    } else {
        print "No se recopilaron datos para promedios.";
    }
}' "${LOG_FILE}"