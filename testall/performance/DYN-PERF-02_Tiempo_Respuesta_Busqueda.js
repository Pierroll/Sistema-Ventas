const axios = require('axios');
const { wrapper } = require('axios-cookiejar-support');
const { CookieJar } = require('tough-cookie');

// --- Configuración de la Prueba de Performance ---
const TOTAL_REQUESTS = 100;
const BASE_URL = 'http://localhost/venta';
const SEARCH_URL = `${BASE_URL}/ventas/buscarProducto`;
const LOGIN_URL = `${BASE_URL}/usuarios/validar`;
const SEARCH_TERMS = ['a', 'pro', '0987654321', 'test', 'ca'];

// --- Usuario para el login ---
const adminUser = {
    correo: 'admin@agmail.com',
    clave: 'admin'
};

/**
 * Realiza una única petición GET al endpoint de búsqueda de productos.
 * @param {import('axios').AxiosInstance} client - El cliente axios con la cookie de sesión.
 * @param {number} requestId - El ID de la petición.
 * @returns {Promise<{status: string, time: number, error: string|null}>}
 */
const performRequest = async (client, requestId) => {
    const startTime = Date.now();
    const searchTerm = SEARCH_TERMS[requestId % SEARCH_TERMS.length];

    try {
        const response = await client.get(SEARCH_URL, {
            params: { pro: searchTerm }
        });

        const endTime = Date.now();
        const duration = endTime - startTime;

        if (response.status === 200 && Array.isArray(response.data)) {
            return { status: 'fulfilled', time: duration, error: null };
        } else {
            return { status: 'rejected', time: duration, error: `Respuesta inesperada. Status: ${response.status}` };
        }
    } catch (error) {
        const endTime = Date.now();
        const duration = endTime - startTime;
        return { status: 'rejected', time: duration, error: error.message };
    }
};

// --- Función Principal de la Prueba ---
const runLoadTest = async () => {
    console.log(`--- Iniciando Prueba de Performance: Búsqueda de Productos ---`);
    
    // 1. Iniciar sesión y obtener la cookie
    const jar = new CookieJar();
    const client = wrapper(axios.create({ jar }));
    
    console.log(`🔒 Intentando iniciar sesión como ${adminUser.correo}...`);
    try {
        const loginResponse = await client.post(LOGIN_URL, new URLSearchParams(adminUser), {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        // Axios-cookiejar-support maneja la cookie automáticamente.
        // Verificamos si el login fue exitoso buscando un texto en la respuesta esperada del home.
        if (!loginResponse.data.includes('Dashboard')) {
             console.error('❌ Error de login: La respuesta no contiene el texto esperado del dashboard. Verifica las credenciales.');
             return;
        }
        console.log('✅ Login exitoso. Cookie de sesión guardada.');
    } catch (e) {
        console.error(`❌ Error fatal durante el login: ${e.message}`);
        return;
    }

    // 2. Lanzar peticiones concurrentes
    console.log(`\n🚀 Lanzando ${TOTAL_REQUESTS} peticiones concurrentes de búsqueda...`);

    const requests = [];
    for (let i = 0; i < TOTAL_REQUESTS; i++) {
        requests.push(performRequest(client, i));
    }

    const results = await Promise.allSettled(requests);

    console.log("\n--- Resultados de la Prueba ---");

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
        console.error("\n--- Detalles de Peticiones Fallidas ---");
        // Agrupar errores para un reporte más limpio
        const errorSummary = {};
        failedRequests.forEach(req => {
            errorSummary[req.error] = (errorSummary[req.error] || 0) + 1;
        });
        for (const [error, count] of Object.entries(errorSummary)) {
             console.error(`- ${error} (ocurrió ${count} veces)`);
        }
    }

    console.log("\n✅ Prueba de búsqueda finalizada.");
};

// Ejecutar la prueba
runLoadTest();
