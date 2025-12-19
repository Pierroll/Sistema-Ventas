const axios = require('axios');

// --- Configuración de la Prueba de Carga ---
const TOTAL_REQUESTS = 50;
const URL = 'http://localhost/venta/usuarios/validar';

const users = [
    { correo: 'admin@agmail.com', clave: 'admin' },
    { correo: 'vendedor@agmail.com', clave: 'vendedor' }
];

// --- Función para una sola petición ---
/**
 * Realiza una única petición POST al endpoint de login.
 * Mide el tiempo de respuesta y devuelve el resultado.
 * @param {number} requestId - El ID de la petición.
 * @returns {Promise<{status: string, time: number, error: string|null}>}
 */
const performRequest = async (requestId) => {
    const startTime = Date.now();
    const user = users[requestId % users.length]; // Alternar entre usuarios

    try {
        // Usamos axios para enviar la petición POST con los datos del formulario
        const response = await axios.post(URL, new URLSearchParams(user), {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });

        const endTime = Date.now();
        const duration = endTime - startTime;

        if (response.status === 200) {
            return { status: 'fulfilled', time: duration, error: null };
        } else {
            return { status: 'rejected', time: duration, error: `Status ${response.status}` };
        }
    } catch (error) {
        const endTime = Date.now();
        const duration = endTime - startTime;
        return { status: 'rejected', time: duration, error: error.message };
    }
};

// --- Función Principal de la Prueba ---
const runLoadTest = async () => {
    console.log(`🚀 Lanzando ${TOTAL_REQUESTS} peticiones de login concurrentes a ${URL}...`);

    const requests = [];
    for (let i = 0; i < TOTAL_REQUESTS; i++) {
        requests.push(performRequest(i));
    }

    // Promise.allSettled espera a que todas las promesas terminen (resueltas o rechazadas)
    const results = await Promise.allSettled(requests);

    console.log("\n--- Resultados de la Prueba de Carga ---");

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

    // --- Cálculo de Estadísticas ---
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
        console.error("\n--- Detalles de Peticiones Fallidas ---");
        // Imprimir hasta 5 errores para no saturar la consola
        failedRequests.slice(0, 5).forEach(req => {
            console.error(`Petición ${req.id} falló. Razón: ${req.error}`);
        });
        if (totalFailed > 5) {
            console.error(`... y ${totalFailed - 5} más.`);
        }
    }

    console.log("\n✅ Prueba de carga finalizada.");
};

// Ejecutar la prueba
runLoadTest();
