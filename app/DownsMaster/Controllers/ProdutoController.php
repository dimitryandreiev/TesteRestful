<?php
namespace DownsMaster\Controllers;
use DownsMaster\Controllers\Controller;

Class ProdutoController extends Controller
{
	public function showError($response, $code)	{
	    $response
	        ->withStatus($code)
	        ->withHeader('Content-Type', 'text/html')
	        ->write('Page not found');
	}

	public function ProdutoList($request, $response, $args)	{
	    $stmt = $this->getConn->query("SELECT * FROM Produtos");
	    $produtos = $stmt->fetchAll($this->fetchAll);
	    //$produtos = json_encode($produtos);

		return $this->view->render(
			$response, 
			'produtos.twig',
			[
				'produtos' => $produtos
			]
		);
	}

	public function getProduto($request, $response, $args)
	{
	    $id = $request->getAttribute('id');
	    if (!is_numeric($id)) {
	        return showError($response, 404);
	    }

	    $conn = $this->getConn;
	    $sql = "SELECT * FROM produtos WHERE id=:id";
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam("id",$id);
	    $stmt->execute();
	    $produto = $stmt->fetchObject();
	    
	    if(empty($produto)) {
	        return showError($response, 404);
	    }

	    //categoria
	    $sql = "SELECT * FROM categorias WHERE id=:id";
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam("id",$produto->idCategoria);
	    $stmt->execute();
	    $produto->categoria = $stmt->fetchObject();
	    var_dump($produto);

	    return $this->view->render(
			$response, 
			'produto.twig',
			[
				'produto' => $produto
			]
		);
	}

	public function saveProduto($request, $response, $args)	{
	    $id = $args['id'];
	    $produto = $request->getParsedBody();
	    $sql = "UPDATE produtos SET nome=:nome,preco=:preco,dataInclusao=:dataInclusao,idCategoria=:idCategoria WHERE   id=:id";
	    $conn = $this->getConn;
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam("nome",$produto['nome']);
	    $stmt->bindParam("preco",$produto['preco']);
	    $stmt->bindParam("dataInclusao",$produto['dataInclusao']);
	    $stmt->bindParam("idCategoria",$produto['idCategoria']);
	    $stmt->bindParam("id",$id);
	    $stmt->execute();

	    echo json_encode($produto);
	}

	public function deleteProduto($request, $response, $args)	{
	    $id = $args['id'];
	    $sql = "DELETE FROM produtos WHERE id=:id";
	    $conn = $this->getConn;
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam("id",$id);
	    $stmt->execute();
	    echo "{'message':'Produto apagado'}";
	}

	public function addProduto($request, $response, $args)	{
	    $produto = $request->getParsedBody();
	    $sql = "INSERT INTO "
	            . "produtos (nome,preco,dataInclusao,idCategoria) "
	            . "values (:nome,:preco,:dataInclusao,:idCategoria) ";
	    $conn = $this->getConn;
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam("nome",$produto['nome']);
	    $stmt->bindParam("preco",$produto['preco']);
	    $stmt->bindParam("dataInclusao",$produto['dataInclusao']);
	    $stmt->bindParam("idCategoria",$produto['idCategoria']);
	    $stmt->execute();
	    $produto['id'] = $conn->lastInsertId();
	    echo json_encode($produto);
	}
}