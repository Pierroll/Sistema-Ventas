<?php
// test_setup.php
header('Content-Type: application/json');

require_once 'Config/Config.php';

// Conexión a la BD
$mysqli = new mysqli(HOST, USER, PASS, DB);

if ($mysqli->connect_errno) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'error' => "Falló la conexión: " . $mysqli->connect_error
    ]));
}

$mysqli->begin_transaction();

try {
    // 1. Limpiamos datos de prueba anteriores
    $mysqli->query("DELETE FROM detalle_permisos WHERE id_usuario IN (1, 2)");
    $mysqli->query("DELETE FROM usuarios WHERE id = 2");
    $mysqli->query("DELETE FROM clientes WHERE dni = '12345678'");
    
    // 2. Insertamos el cliente de prueba
    $sql_cliente = "INSERT INTO clientes (id, dni, nombre, telefono, direccion, estado)
                    VALUES (2, '12345678', 'Cliente Test', '987654321', 'DIRECCION TEST', 1)
                    ON DUPLICATE KEY UPDATE 
                    nombre = 'Cliente Test',
                    telefono = '987654321',
                    direccion = 'DIRECCION TEST',
                    estado = 1";
    
    if (!$mysqli->query($sql_cliente)) {
        throw new Exception("Error al insertar cliente: " . $mysqli->error);
    }
    
    // 3. Hash de la contraseña usando SHA256
    $password_admin = hash('sha256', 'admin');
    $password_vendedor = hash('sha256', 'vendedor');
    
    // 4. Insertar el VENDEDOR
    $sql_vendedor = "INSERT INTO usuarios (id, nombre, apellido, correo, telefono, direccion, perfil, clave, id_caja, token, estado)
                     VALUES (2, 'VENDEDOR', NULL, 'vendedor@agmail.com', NULL, NULL, 'avatar.svg', ?, 1, NULL, 1)
                     ON DUPLICATE KEY UPDATE 
                     clave = ?,
                     estado = 1";
    
    $stmt = $mysqli->prepare($sql_vendedor);
    $stmt->bind_param("ss", $password_vendedor, $password_vendedor);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar vendedor: " . $stmt->error);
    }
    $stmt->close();
    
    // 5. Asignar TODOS los permisos al ADMIN (id=1)
    $permisos_admin = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 46, 47];
    
    foreach ($permisos_admin as $id_permiso) {
        $sql_permiso = "INSERT INTO detalle_permisos (id_usuario, id_permiso) 
                        VALUES (1, ?)
                        ON DUPLICATE KEY UPDATE id_permiso = ?";
        $stmt = $mysqli->prepare($sql_permiso);
        $stmt->bind_param("ii", $id_permiso, $id_permiso);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al asignar permiso $id_permiso al admin: " . $stmt->error);
        }
        $stmt->close();
    }
    
    // 6. Asignar PERMISOS LIMITADOS al VENDEDOR (NO puede ver usuarios)
    $permisos_vendedor = [
        13, // crear_cliente
        14, // modificar_cliente
        15, // eliminar_cliente
        16, // restaurar_cliente
        37, // nueva_venta
        39, // reporte_ventas
        43  // abrir_caja
    ];
    
    foreach ($permisos_vendedor as $id_permiso) {
        $sql_permiso = "INSERT INTO detalle_permisos (id_usuario, id_permiso) 
                        VALUES (2, ?)
                        ON DUPLICATE KEY UPDATE id_permiso = ?";
        $stmt = $mysqli->prepare($sql_permiso);
        $stmt->bind_param("ii", $id_permiso, $id_permiso);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al asignar permiso $id_permiso al vendedor: " . $stmt->error);
        }
        $stmt->close();
    }
    
    // 7. Commit de la transacción
    $mysqli->commit();
    
    // 8. Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Datos de prueba configurados correctamente',
        'data' => [
            'admin' => [
                'id' => 1,
                'email' => 'admin@agmail.com',
                'password' => 'admin',
                'permisos' => count($permisos_admin)
            ],
            'vendedor' => [
                'id' => 2,
                'email' => 'vendedor@agmail.com',
                'password' => 'vendedor',
                'permisos' => count($permisos_vendedor)
            ],
            'cliente' => [
                'id' => 2,
                'nombre' => 'Cliente Test',
                'dni' => '12345678'
            ]
        ]
    ]);
    
} catch (Exception $e) {
    $mysqli->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

$mysqli->close();
?>