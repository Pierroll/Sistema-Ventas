#!/bin/bash

# Script para simular carga concurrente en el endpoint de login

echo "🚀 Lanzando 50 peticiones de login concurrentes..."

for i in {1..50}
do
   # Alternar entre usuario admin y vendedor
   if (( i % 2 == 0 )); then
     user="admin@agmail.com"
     pass="admin"
   else
     user="vendedor@agmail.com"
     pass="vendedor"
   fi

   # Ejecutar curl en segundo plano (&)
   curl -s -o /dev/null -w "Petición $i: %{time_total}s\n" -X POST -d "correo=$user&clave=$pass" http://localhost/venta/usuarios/validar &

done

# Esperar a que todos los procesos en segundo plano terminen
wait

echo "✅ Prueba de carga finalizada."


