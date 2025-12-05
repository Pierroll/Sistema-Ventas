#!/bin/bash
set -euo pipefail

# ==================== CONFIGURACIÓN ====================
# Variables de entorno para SonarQube
export SONAR_HOST_URL="http://localhost:9000"
export SONAR_TOKEN="sqa_e405a96aa1e81803787a6188f9b2d7745d241a1f"
export PROJECT_KEY="php-mvc-local"

echo "🔍 Iniciando análisis completo de SonarQube..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# ==================== PASO 1: COBERTURA ====================
echo ""
echo "📊 [1/4] Generando reporte de cobertura..."
if [ -f "generate_coverage.php" ]; then
    php generate_coverage.php
    echo "✅ Cobertura generada"
else
    echo "⚠️  generate_coverage.php no encontrado"
fi

# ==================== PASO 2: TESTS ====================
echo ""
echo "🧪 [2/4] Ejecutando pruebas unitarias..."
if [ -f "vendor/bin/phpunit" ]; then
    ./vendor/bin/phpunit --no-coverage || echo "⚠️  Algunas pruebas fallaron"
else
    echo "⚠️  PHPUnit no encontrado. Instala con: composer require --dev phpunit/phpunit"
fi

# ==================== PASO 3: ANÁLISIS SONARQUBE ====================
echo ""
echo "🔎 [3/4] Ejecutando análisis de SonarQube..."
if command -v sonar-scanner &> /dev/null; then
    sonar-scanner
    echo "✅ Análisis de SonarQube completado"
    echo "⏳ Esperando 10 segundos para que SonarQube procese los datos..."
    sleep 10
else
    echo "⚠️  SonarQube Scanner no encontrado"
    echo "   Instala SonarQube Scanner o usa Docker:"
    echo "   docker run --rm -v \$(pwd):/usr/src sonarsource/sonar-scanner-cli"
    exit 1
fi

# ==================== PASO 4: GENERAR REPORTE PDF ====================
echo ""
echo "📄 [4/4] Generando reporte PDF..."

# Verificar dependencias
if ! command -v jq &> /dev/null; then
    echo "❌ jq no está instalado. Instala con:"
    echo "   - Ubuntu/Debian: sudo apt-get install jq"
    echo "   - macOS: brew install jq"
    exit 1
fi

if ! command -v pandoc &> /dev/null; then
    echo "⚠️  pandoc no está instalado. Solo se generará el Markdown."
    echo "   Para PDF instala: sudo apt-get install pandoc texlive-xetex"
fi

# Descargar script de reporte
if [ ! -f "sonar-report-generator.sh" ]; then
    echo "Descargando generador de reportes..."
    cat > sonar-report-generator.sh << 'SCRIPT_EOF'
#!/usr/bin/env bash
set -euo pipefail

# ---------- CONFIG ----------
SONAR_HOST_URL="${SONAR_HOST_URL:-}"
SONAR_TOKEN="${SONAR_TOKEN:-}"
PROJECT_KEY="${PROJECT_KEY:-}"
OUTDIR="reports"
TOP_ISSUES=30
PER_PAGE=500
DAYS_HISTORY=30

# ---------- VALIDACIONES ----------
if [[ -z "${SONAR_HOST_URL}" || -z "${SONAR_TOKEN}" || -z "${PROJECT_KEY}" ]]; then
  echo "❌ Faltan variables. Exporta SONAR_HOST_URL, SONAR_TOKEN y PROJECT_KEY."
  exit 1
fi

mkdir -p "$OUTDIR"

auth() {
  curl -fsS -u "${SONAR_TOKEN}:" "$@"
}

# ---------- 1. Info general del proyecto ----------
echo "[1/8] Cargando información del proyecto..."
auth "${SONAR_HOST_URL}/api/components/show?component=${PROJECT_KEY}" \
  > "${OUTDIR}/component.json" || echo '{"component":{"name":"'"${PROJECT_KEY}"'"}}' > "${OUTDIR}/component.json"

PROJECT_NAME=$(jq -r '.component.name // "'${PROJECT_KEY}'"' "${OUTDIR}/component.json" 2>/dev/null || echo "${PROJECT_KEY}")

# ---------- 2. Métricas principales ----------
echo "[2/8] Cargando métricas..."
METRICS="bugs,vulnerabilities,code_smells,coverage,duplicated_lines_density,ncloc,files,functions,complexity,reliability_rating,security_rating,sqale_rating,security_hotspots,tests,test_errors,test_failures,test_success_density"
auth "${SONAR_HOST_URL}/api/measures/component?component=${PROJECT_KEY}&metricKeys=${METRICS}" \
  > "${OUTDIR}/measures.json" || echo '{"component":{"measures":[]}}' > "${OUTDIR}/measures.json"

# ---------- 3. Quality Gate ----------
echo "[3/8] Quality Gate..."
auth "${SONAR_HOST_URL}/api/qualitygates/project_status?projectKey=${PROJECT_KEY}" \
  > "${OUTDIR}/quality_gate.json" || echo '{"projectStatus":{"status":"UNKNOWN"}}' > "${OUTDIR}/quality_gate.json"

# ---------- 4. Últimos análisis ----------
echo "[4/8] Últimos análisis..."
auth "${SONAR_HOST_URL}/api/project_analyses/search?project=${PROJECT_KEY}&ps=10" \
  > "${OUTDIR}/analyses.json" || echo '{"analyses":[]}' > "${OUTDIR}/analyses.json"

# ---------- 5. Issues (con paginación) ----------
echo "[5/8] Descargando issues (paginado)..."
> "${OUTDIR}/issues_all.jsonl"
page=1
while : ; do
  JSON=$(auth "${SONAR_HOST_URL}/api/issues/search?componentKeys=${PROJECT_KEY}&ps=${PER_PAGE}&p=${page}&additionalFields=_all" || echo '{"issues":[],"total":0}')
  echo "${JSON}" | jq -c '.issues[]' >> "${OUTDIR}/issues_all.jsonl" 2>/dev/null || true
  TOTAL=$(echo "${JSON}" | jq -r '.total // 0')
  PAGE_IDX=$(( (page-1) * PER_PAGE + PER_PAGE ))
  echo "   página ${page} (${PAGE_IDX}/${TOTAL})"
  if [[ "${PAGE_IDX}" -ge "${TOTAL}" || "${TOTAL}" -eq 0 ]]; then break; fi
  page=$((page+1))
done
jq -s '{issues: .}' "${OUTDIR}/issues_all.jsonl" > "${OUTDIR}/issues.json" 2>/dev/null || echo '{"issues":[]}' > "${OUTDIR}/issues.json"

# ---------- 6. Hotspots de seguridad ----------
echo "[6/8] Hotspots..."
auth "${SONAR_HOST_URL}/api/hotspots/search?projectKey=${PROJECT_KEY}&ps=500" \
  > "${OUTDIR}/hotspots.json" || echo '{"hotspots":[]}' > "${OUTDIR}/hotspots.json"

# ---------- 7. Cobertura/duplicación por archivo ----------
echo "[7/8] Métricas por archivo..."
auth "${SONAR_HOST_URL}/api/measures/component_tree?component=${PROJECT_KEY}&metricKeys=coverage,duplicated_lines_density&ps=${PER_PAGE}&strategy=leaves" \
  > "${OUTDIR}/tree_page1.json" || echo '{"components":[],"paging":{"total":0}}' > "${OUTDIR}/tree_page1.json"

TOTAL_TREE=$(jq -r '.paging.total // 0' "${OUTDIR}/tree_page1.json" 2>/dev/null || echo 0)
> "${OUTDIR}/tree_all.jsonl"
if [[ "${TOTAL_TREE}" -gt 0 ]]; then
  jq -c '.components[]' "${OUTDIR}/tree_page1.json" >> "${OUTDIR}/tree_all.jsonl" 2>/dev/null || true
  TOTAL_PAGES=$(( (TOTAL_TREE + PER_PAGE - 1) / PER_PAGE ))
  for ((p=2; p<=TOTAL_PAGES; p++)); do
    echo "   árbol página ${p}/${TOTAL_PAGES}"
    auth "${SONAR_HOST_URL}/api/measures/component_tree?component=${PROJECT_KEY}&metricKeys=coverage,duplicated_lines_density&ps=${PER_PAGE}&p=${p}&strategy=leaves" \
      | jq -c '.components[]' >> "${OUTDIR}/tree_all.jsonl" 2>/dev/null || true
  done
fi
jq -s '{components: .}' "${OUTDIR}/tree_all.jsonl" > "${OUTDIR}/tree_all.json" 2>/dev/null || echo '{"components":[]}' > "${OUTDIR}/tree_all.json"

# ---------- 8. Tendencias ----------
echo "[8/8] Historia de métricas..."
since=$(date -d "${DAYS_HISTORY} days ago" +"%Y-%m-%d" 2>/dev/null || date -v-"${DAYS_HISTORY}"d +"%Y-%m-%d")
HIST_KEYS="coverage,bugs,vulnerabilities,code_smells,duplicated_lines_density"
auth "${SONAR_HOST_URL}/api/measures/search_history?component=${PROJECT_KEY}&metrics=${HIST_KEYS}&from=${since}" \
  > "${OUTDIR}/history.json" || echo '{"measures":[]}' > "${OUTDIR}/history.json"

# ---------- Helper ----------
get_measure() {
  local key="$1"
  jq -r --arg k "$key" '.component.measures[]? | select(.metric==$k) | .value // empty' "${OUTDIR}/measures.json"
}
qg_status=$(jq -r '.projectStatus.status // "UNKNOWN"' "${OUTDIR}/quality_gate.json")

bugs=$(get_measure bugs); vulns=$(get_measure vulnerabilities); smells=$(get_measure code_smells)
cov=$(get_measure coverage); dup=$(get_measure duplicated_lines_density)
ncloc=$(get_measure ncloc); files=$(get_measure files); funcs=$(get_measure functions)
rel=$(get_measure reliability_rating); sec=$(get_measure security_rating); debt=$(get_measure sqale_rating)
tests=$(get_measure tests); tfail=$(get_measure test_failures); terr=$(get_measure test_errors); tsucc=$(get_measure test_success_density)

# ---------- Markdown ----------
MD="${OUTDIR}/sonar-report.md"
echo "Generando ${MD}..."
{
  echo "---"
  echo "title: Informe de Calidad de Código"
  echo "subtitle: ${PROJECT_NAME}"
  echo "date: $(date '+%d/%m/%Y %H:%M')"
  echo "---"
  echo
  echo "# Resumen Ejecutivo"
  echo
  echo "**Proyecto:** ${PROJECT_KEY}  "
  echo "**Quality Gate:** **${qg_status}**"
  echo
  echo "## Métricas Principales"
  echo
  echo "### Confiabilidad y Seguridad"
  echo "- **Bugs:** ${bugs:-0}"
  echo "- **Vulnerabilidades:** ${vulns:-0}"
  echo "- **Hotspots de Seguridad:** $(jq -r '.hotspots | length' "${OUTDIR}/hotspots.json" 2>/dev/null || echo 0)"
  echo "- **Reliability Rating:** ${rel:-N/A}/5"
  echo "- **Security Rating:** ${sec:-N/A}/5"
  echo
  echo "### Mantenibilidad"
  echo "- **Code Smells:** ${smells:-0}"
  echo "- **Technical Debt Rating:** ${debt:-N/A}/5"
  echo
  echo "### Tamaño del Proyecto"
  echo "- **Líneas de código:** ${ncloc:-N/A}"
  echo "- **Archivos:** ${files:-N/A}"
  echo "- **Funciones:** ${funcs:-N/A}"
  echo
  echo "### Calidad de Código"
  echo "- **Cobertura de tests:** ${cov:-N/A}%"
  echo "- **Duplicación:** ${dup:-N/A}%"
  echo "- **Tests totales:** ${tests:-N/A}"
  echo "- **Tasa de éxito:** ${tsucc:-N/A}%"
  echo
  echo "\\newpage"
  echo
  echo "# Issues Críticos"
  echo
  jq -r --argjson N "${TOP_ISSUES}" '
    .issues? // [] |
    map(select(.severity == "CRITICAL" or .severity == "BLOCKER")) |
    sort_by(.severity, .creationDate) |
    reverse |
    .[:$N] |
    .[] |
    "## " + (.severity // "UNKNOWN") + " - " + (.type // "") + "\n" +
    "**Regla:** " + (.rule // "") + "  \n" +
    "**Archivo:** `" + ((.component // "") | gsub(".*:"; "")) + "` (línea " + ((.line // "?")|tostring) + ")  \n" +
    "**Mensaje:** " + ((.message // "") | gsub("\\n"; " ") | gsub("_"; " ")) + "\n"
  ' "${OUTDIR}/issues.json" 2>/dev/null || echo "_No hay issues críticos_"
  echo
  echo "\\newpage"
  echo
  echo "# Todos los Issues (Top ${TOP_ISSUES})"
  echo
  jq -r --argjson N "${TOP_ISSUES}" '
    .issues? // [] |
    sort_by(.severity, .creationDate) |
    reverse |
    .[:$N] |
    .[] |
    "### " + (.severity // "") + " | " + (.type // "") + "\n" +
    "- **Regla:** " + (.rule // "") + "\n" +
    "- **Archivo:** `" + ((.component // "") | gsub(".*:"; "")) + "`\n" +
    "- **Línea:** " + ((.line // "?")|tostring) + "\n" +
    "- **Mensaje:** " + ((.message // "") | gsub("\\n"; " ") | gsub("_"; " ")) + "\n"
  ' "${OUTDIR}/issues.json" 2>/dev/null || echo "_Sin issues_"
  echo
  echo "\\newpage"
  echo
  echo "# Hotspots de Seguridad"
  echo
  jq -r '
    .hotspots? // [] |
    .[] |
    "## " + (.vulnerabilityProbability // "UNKNOWN") + " - " + (.status // "") + "\n" +
    "**Archivo:** `" + ((.component // "") | gsub(".*:"; "")) + "` (línea " + ((.line // "?")|tostring) + ")  \n" +
    "**Mensaje:** " + ((.message // "") | gsub("\\n"; " ") | gsub("_"; " ")) + "\n"
  ' "${OUTDIR}/hotspots.json" 2>/dev/null || echo "_Sin hotspots_"
  echo
  echo "\\newpage"
  echo
  echo "# Archivos con Baja Cobertura"
  echo
  jq -r '
    .components? // [] |
    map(select(.measures[]? | select(.metric=="coverage" and (.value | tonumber) < 80))) |
    sort_by(.measures[]? | select(.metric=="coverage") | .value | tonumber) |
    .[:20] |
    .[] |
    "- **" + ((.path // .key // "") | gsub(".*:"; "")) + "**: " +
    ( (.measures[]? | select(.metric=="coverage") | .value) // "0" ) + "% de cobertura\n"
  ' "${OUTDIR}/tree_all.json" 2>/dev/null || echo "_Todos los archivos tienen buena cobertura_"
  echo
  echo "\\newpage"
  echo
  echo "# Historial de Análisis"
  echo
  jq -r '
    .analyses[]? |
    "- **" + (.date // "") + "**: " + ((.events // []) | map(.name) | join(", "))
  ' "${OUTDIR}/analyses.json" 2>/dev/null || echo "_Sin historial_"
  echo
  echo "---"
  echo
  echo "_Generado desde: ${SONAR_HOST_URL}_"
} > "${MD}"

# ---------- PDF ----------
PDF="${OUTDIR}/sonar-report.pdf"

if command -v pandoc &> /dev/null; then
  echo "Generando PDF..."
  pandoc "${MD}" -o "${PDF}" \
    --pdf-engine=xelatex \
    -V geometry:margin=0.75in \
    -V fontsize=10pt \
    -V documentclass=article \
    -V linkcolor=blue \
    -V urlcolor=blue \
    --toc \
    --toc-depth=2 \
    2>/dev/null || {
      echo "⚠️  XeLaTeX falló, intentando con pdflatex..."
      pandoc "${MD}" -o "${PDF}" \
        --pdf-engine=pdflatex \
        -V geometry:margin=0.75in \
        -V fontsize=10pt \
        2>/dev/null || echo "⚠️  Error generando PDF. Revisa ${MD}"
    }

  if [[ -f "${PDF}" ]]; then
    echo "✅ Listo ➜ ${PDF}"
    echo "📄 También disponible: ${MD}"
  else
    echo "⚠️  PDF no generado. Abre el markdown: ${MD}"
  fi
else
  echo "✅ Markdown generado ➜ ${MD}"
  echo "⚠️  Instala pandoc para generar PDF"
fi
SCRIPT_EOF
    chmod +x sonar-report-generator.sh
fi

# Ejecutar generador de reportes
./sonar-report-generator.sh

# ==================== RESUMEN FINAL ====================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Proceso completado"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 Cobertura actual:"
if [ -f "build/coverage.txt" ]; then
    cat build/coverage.txt | head -10
else
    echo "   (archivo build/coverage.txt no encontrado)"
fi
echo ""
echo "🌐 Ver en SonarQube: ${SONAR_HOST_URL}/dashboard?id=${PROJECT_KEY}"
echo "📄 Reporte PDF: reports/sonar-report.pdf"
echo "📝 Reporte MD: reports/sonar-report.md"
echo ""