<?php
require 'vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();
$container['view'] = function($container){
    $folder = __DIR__;
    $view = new \Slim\Views\Twig(
        $folder.'/app/views',
        [
            'cache' => false
        ]
    );

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$app->get('/', function () {
    echo "SlimProdutos";
});

$container['CategoriaController'] = function($container) use ($app) {
    return new DownsMaster\Controllers\CategoriaController($container);
};

$container['ProdutoController'] = function($container) use ($app) {
    return new DownsMaster\Controllers\ProdutoController($container);
};

$container['getConn'] = function ($container) use ($app)
{
    return new PDO('mysql:host=localhost;dbname=teste',
        'root',
        '',
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
    );
};
$container['fetchAll'] = function ($container) use ($app)
{
    return PDO::FETCH_OBJ;
};


$app->get('/categorias','CategoriaController:CategoriaList')->setName('categorias');
$app->get('/produtos','ProdutoController:ProdutoList');
$app->get('/produtos/{id}','ProdutoController:getProduto');
$app->post('/produtos/{id}','ProdutoController:saveProduto');
$app->delete('/produtos/{id}','ProdutoController:deleteProduto');
$app->post('/produtos','ProdutoController:addProduto');

$app->run();

function saveProduto($request, $response, $args)
{
    $id = $args['id'];
    $produto = $request->getParsedBody();
    $sql = "UPDATE produtos SET nome=:nome,preco=:preco,dataInclusao=:dataInclusao,idCategoria=:idCategoria WHERE   id=:id";
    $conn = getConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("nome",$produto['nome']);
    $stmt->bindParam("preco",$produto['preco']);
    $stmt->bindParam("dataInclusao",$produto['dataInclusao']);
    $stmt->bindParam("idCategoria",$produto['idCategoria']);
    $stmt->bindParam("id",$id);
    $stmt->execute();

    echo json_encode($produto);
}

function deleteProduto($request, $response, $args)
{
    $id = $args['id'];
    $sql = "DELETE FROM produtos WHERE id=:id";
    $conn = getConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("id",$id);
    $stmt->execute();
    echo "{'message':'Produto apagado'}";
}

function addProduto($request, $response, $args)
{
    $produto = $request->getParsedBody();
    $sql = "INSERT INTO "
            . "produtos (nome,preco,dataInclusao,idCategoria) "
            . "values (:nome,:preco,:dataInclusao,:idCategoria) ";
    $conn = getConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("nome",$produto['nome']);
    $stmt->bindParam("preco",$produto['preco']);
    $stmt->bindParam("dataInclusao",$produto['dataInclusao']);
    $stmt->bindParam("idCategoria",$produto['idCategoria']);
    $stmt->execute();
    $produto['id'] = $conn->lastInsertId();
    echo json_encode($produto);
}