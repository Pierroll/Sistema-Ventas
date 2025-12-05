#!/bin/bash

# Duración de la prueba en segundos (10 minutos = 600 segundos)
END_TIME=$((SECONDS + 600))

# URL del endpoint de login en el contenedor Docker
URL="http://localhost:8081/usuarios/validar"

echo "🚀 Iniciando prueba de carga sostenida por 10 minutos..."
echo "Manteniendo 50 usuarios concurrentes en $URL"
echo "-----------------------------------------------------"

# Bucle principal que se ejecuta durante 10 minutos
while [ $SECONDS -lt $END_TIME ]; do
  
  # Lanza un lote de 50 peticiones concurrentes
  for i in {1..50}; do
    # Alterna entre usuario admin y vendedor
    if (( i % 2 == 0 )); then
      user="admin@agmail.com"
      pass="admin"
    else
      user="vendedor@agmail.com"
      pass="vendedor"
    fi

    # Ejecuta curl en segundo plano, registrando tiempo y estado HTTP
    curl -s -o /dev/null -w "Timestamp: $(date +%T) | Request: %-2s | Status: %{http_code} | Time: %{time_total}s\n" \
      -X POST -d "correo=$user&clave=$pass" "$URL" &
  
done

  # Espera a que el lote actual de 50 peticiones termine
  wait
  
  # Pequeña pausa de 1 segundo entre lotes para no saturar la máquina cliente
  sleep 1 

done

echo "-----------------------------------------------------"
echo "✅ Prueba de carga de 10 minutos finalizada."
