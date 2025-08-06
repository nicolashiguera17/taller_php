<?php

require_once "db.php";


// se va a implementar el : GET, POST, PUT, DELETE 
$method = $_SERVER['REQUEST_METHOD']; 

$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));


$recurso = $uri[0]; 
$id = $uri[1] ?? null; 

header('Content-Type: application/json'); 
// EndPoint
if ($recurso !== 'productos' AND $recurso !== 'categorias' AND $recurso !== 'promociones' AND $recurso !== 'productos-promocion') {
    http_response_code(404);
    echo json_encode([
        'error' => 'Recurso no encontrado',
        'code' => 404,
        'errorUrl  ' => 'https://http.cat/404'
    ]);
    exit;
}

function IDNoEncontrado($id){
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'error' => 'ID no encontrado',
            'code' => 400,
            'errorUrl' => 'https://http.cat/400'
        ]);
        exit;
    }
}


switch ($recurso) {
    /* ----------------------------------------------------------------------------------------------------------------------------------------------------------
 *                                                         SECCIÓN: CATEGORÍAS
 *                                  Aquí se gestionan las operaciones CRUD para la entidad categorías.
 * --------------------------------------------------------------------------------------------------------------------------------------------------------------- */

    case 'categorias':
        switch ($method) {
            // ---------------------------------------------------- seccion de get categorias ----------------------------------------------
            case 'GET':

                if ($id) {
                    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
                    $stmt->execute([$id]);
                    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($categoria) {
                        echo json_encode($categoria);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Categoria no encontrada']);
                    }
    
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM categorias");
                    $stmt->execute();
                    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($response);
                }
                break;

            // ---------------------------------------------------- seccion de post categorias ----------------------------------------------    
            case 'POST':

                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("INSERT INTO categorias(nombre) VALUES(?)");
                $stmt->execute([
                    $data['nombre']
                ]);
                http_response_code(201);
                $data['id'] = $pdo->lastInsertId();
                echo json_encode($data);
                break;

            // ---------------------------------------------------- seccion de put categorias  ------------------------------------------------
            case 'PUT':


                IDNoEncontrado($id);
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE categorias SET id=?, nombre=? WHERE id=?");
                $stmt->execute([
                    $data['id'],
                    $data['nombre'],
                    $id,
                ]);
                echo json_encode($data);
                break;

            // ----------------------------------------------------seccion de delete de categorias  ------------------------------------------------
            case 'DELETE':

                IDNoEncontrado($id);
                $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if (!$product) {
                    http_response_code(404);
                    echo json_encode([
                        'error' => 'Categorias no encontrado',
                        'code' => 404,
                        'errorUrl' => 'https://http.cat/404'
                    ]);
                    exit;
                }
                $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode($product);
                break;
        }
        break;

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------------
 *                                                         SECCIÓN: PRODUCTOS
 *                                     Aquí se gestionan las operaciones CRUD para la entidad productos.
 * --------------------------------------------------------------------------------------------------------------------------------------------------------------- */

    case 'productos':
        switch ($method) {
            // --------------------------------------------- -seccion de get de productos----------------------------------------
            case 'GET':


                if ($id) {
                    $stmt = $pdo->prepare("
                        SELECT 
                            productos.*,
                            IF(promociones.id IS NULL, 'Sin promocion', 'Con promocion') AS promocion
                        FROM productos
                        LEFT JOIN promociones ON productos.id = promociones.producto_id
                        WHERE productos.id = ?
                    ");
                    
                    $stmt->execute([$id]);
                    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    if ($producto) {
                        echo json_encode($producto);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Producto no encontrado']);
                    }
                
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM productos");
                    $stmt->execute();
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($productos);
                }
                break;

            // --------------------------------------------- -seccion de post de productos ----------------------------------------
            case 'POST':

                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("INSERT INTO productos(nombre,precio, categoria_id) VALUES(?,?,?)");
                $stmt->execute([
                    $data['nombre'], $data['precio'], $data['categoria_id']
                ]);
                http_response_code(201);
                $data['id'] = $pdo->lastInsertId();
                echo json_encode($data);
                break;

            // --------------------------------------------- -seccion de put de productos  ----------------------------------------
            case 'PUT':

                IDNoEncontrado($id);
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE productos SET id=?, nombre=?, precio=?, categoria_id=? WHERE id=?");
                $stmt->execute([
                    $data['id'],
                    $data['nombre'],
                    $data['precio'],
                    $data['categoria_id'],
                    $id,
                ]);
                echo json_encode($data);
                break;

            // --------------------------------------------- seccion de delete de productos  ----------------------------------------
            case 'DELETE':

                IDNoEncontrado($id);
                $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if (!$product) {
                    http_response_code(404);
                    echo json_encode([
                        'error' => 'Producto no encontrado',
                        'code' => 404,
                        'errorUrl' => 'https://http.cat/404'
                    ]);
                    exit;
                }
                $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode($product);
                break;
        }
        break;

            /* ----------------------------------------------------------------------------------------------------------------------------------------------------------
 *                                                                         SECCIÓN: PROMOCIONES
 *                                                 Aquí se gestionan las operaciones CRUD para la entidad promociones.
 * --------------------------------------------------------------------------------------------------------------------------------------------------------------- */

        switch ($method) {
            // ---------------------------------------------  seccion de get de promociones  ----------------------------------------
            case 'GET':
                if ($id) {
                    // Si viene un ID traer solo una promociones
                    $stmt = $pdo->prepare("SELECT * FROM promociones WHERE id = ?");
                    $stmt->execute([$id]);
                    $promociones = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($promociones) {
                        echo json_encode($promociones);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'promociones no encontrada']);
                    }
    
                } else {
                    // Si NO hay ID traer todas
                    $stmt = $pdo->prepare("SELECT * FROM promociones");
                    $stmt->execute();
                    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($response);
                }
                break;
            // ---------------------------------------------  seccion de post de promociones  ----------------------------------------
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("INSERT INTO promociones(descripcion,descuento, producto_id) VALUES(?,?,?)");
                $stmt->execute([
                    $data['descripcion'], $data['descuento'], $data['producto_id']
                ]);
                http_response_code(201);
                $data['id'] = $pdo->lastInsertId();
                echo json_encode($data);
            
                break;
            // --------------------------------------------- seccion de put de promociones  ----------------------------------------
            case 'PUT':
                IDNoEncontrado($id);
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE promociones SET id=?, descripcion=?, descuento=?, producto_id=? WHERE id=?");
                $stmt->execute([
                    $data['id'],
                    $data['descripcion'],
                    $data['descuento'],
                    $data['producto_id'],
                    $id,
                ]);
                echo json_encode($data);
                break;
            // --------------------------------------------- seccion de delete de promociones ----------------------------------------
            case 'DELETE':
                IDNoEncontrado($id);
                $stmt = $pdo->prepare("SELECT * FROM promociones WHERE id = ?");
                $stmt->execute([$id]);
                $promociones = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if (!$promociones) {
                    http_response_code(404);
                    echo json_encode([
                        'error' => 'Promociones no encontrado',
                        'code' => 404,
                        'errorUrl' => 'https://http.cat/404'
                    ]);
                    exit;
                }
                $stmt = $pdo->prepare("DELETE FROM promociones WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode($promociones);
                break;
        }
        break;

}