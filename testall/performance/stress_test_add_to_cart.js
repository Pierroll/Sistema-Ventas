const axios = require('axios');

// --- Configuración de la Prueba de Estrés ---
const TOTAL_REQUESTS = 100;
const PRODUCT_CODE = '0987654321'; // Código del producto a agregar
const SEARCH_URL = 'http://localhost/venta/ventas/buscarProducto';
const ADD_URL_BASE = 'http://localhost/venta/ventas/agregarVenta';

// --- Función para una sola petición ---
const performRequest = async (productId) => {
    const startTime = Date.now();
    try {
        const response = await axios.get(`${ADD_URL_BASE}/${productId}`);
        const endTime = Date.now();
        const duration = endTime - startTime;

        // El backend responde con un JSON que tiene una propiedad 'icono'
        if (response.status === 200 && response.data.icono === 'success') {
            return { status: 'fulfilled', time: duration, error: null };
        } else {
            return { status: 'rejected', time: duration, error: response.data.msg || `Status ${response.status}` };
        }
    } catch (error) {
        const endTime = Date.now();
        const duration = endTime - startTime;
        return { status: 'rejected', time: duration, error: error.message };
    }
};

// --- Función Principal de la Prueba ---
const runStressTest = async () => {
    console.log(`--- Prueba de Estrés: Agregar Producto al Carrito ---`);
    console.log(`Buscando ID para el producto con código: ${PRODUCT_CODE}...`);

    // 1. Obtener el ID del producto
    let productId;
    try {
        const searchResponse = await axios.get(SEARCH_URL, { params: { pro: PRODUCT_CODE } });
        if (searchResponse.data && searchResponse.data.length > 0) {
            productId = searchResponse.data[0].id;
            console.log(`✅ Producto encontrado. ID: ${productId}`);
        } else {
            console.error(`❌ Error: No se pudo encontrar el producto con el código '${PRODUCT_CODE}'. Abortando prueba.`);
            return;
        }
    } catch (e) {
        console.error(`❌ Error al buscar el producto: ${e.message}`);
        return;
    }

    // 2. Lanzar peticiones concurrentes
    console.log(`
🚀 Lanzando ${TOTAL_REQUESTS} peticiones concurrentes para agregar el producto ID ${productId} al carrito...`);

    const requests = [];
    for (let i = 0; i < TOTAL_REQUESTS; i++) {
        requests.push(performRequest(productId));
    }

    const results = await Promise.allSettled(requests);

    // 3. Procesar y mostrar resultados
    console.log("\n--- Resultados de la Prueba de Estrés ---");

    const successfulRequests = [];
    const failedRequests = [];

    results.forEach((result, index) => {
        if (result.status === 'fulfilled' && result.value.status === 'fulfilled') {
            successfulRequests.push(result.value.time);
        } else {
            const errorInfo = result.value || { error: 'Unknown promise rejection' };
            failedRequests.push({ id: index + 1, error: errorInfo.error });
        }
    });

    const totalSuccessful = successfulRequests.length;
    const totalFailed = failedRequests.length;

    console.log(`Total de Peticiones: ${TOTAL_REQUESTS}`);
    console.log(`✅ Peticiones Exitosas: ${totalSuccessful}`);
    console.log(`❌ Peticiones Fallidas: ${totalFailed}`);

    if (totalSuccessful > 0) {
        const sum = successfulRequests.reduce((a, b) => a + b, 0);
        const avg = (sum / totalSuccessful).toFixed(2);
        const min = Math.min(...successfulRequests);
        const max = Math.max(...successfulRequests);
        
        console.log("\n--- Tiempos de Respuesta (Exitosas) ---");
        console.log(`Promedio: ${avg} ms`);
        console.log(`Mínimo:   ${min} ms`);
        console.log(`Máximo:   ${max} ms`);
    }

    if (totalFailed > 0) {
        console.error("\n--- Detalles de Peticiones Fallidas (primeros 5) ---");
        // Agrupar errores para un reporte más limpio
        const errorSummary = {};
        failedRequests.forEach(req => {
            errorSummary[req.error] = (errorSummary[req.error] || 0) + 1;
        });
        for (const [error, count] of Object.entries(errorSummary)) {
             console.error(`- ${error} (ocurrió ${count} veces)`);
        }
    }

    console.log("\n✅ Prueba de estrés finalizada.");
};

// Ejecutar la prueba
runStressTest();
