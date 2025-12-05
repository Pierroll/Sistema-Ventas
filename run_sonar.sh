#!/bin/bash

# Script para ejecutar análisis de SonarQube
# Asegúrate de tener SonarQube instalado y ejecutándose

echo "🔍 Iniciando análisis de SonarQube..."

# Generar cobertura de código
echo "📊 Generando reporte de cobertura..."
php generate_coverage.php

# Ejecutar pruebas
echo "🧪 Ejecutando pruebas unitarias..."
./vendor/bin/phpunit --no-coverage

# Ejecutar SonarQube Scanner
echo "🔎 Ejecutando análisis de SonarQube..."
if command -v sonar-scanner &> /dev/null; then
    sonar-scanner
    echo "✅ Análisis de SonarQube completado"
else
    echo "⚠️  SonarQube Scanner no encontrado"
    echo "   Instala SonarQube Scanner o usa Docker:"
    echo "   docker run --rm -v \$(pwd):/usr/src sonarqube:latest sonar-scanner"
fi

echo "📈 Cobertura actual:"
cat build/coverage.txt | head -10
