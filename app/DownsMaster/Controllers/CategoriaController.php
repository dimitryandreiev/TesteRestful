<?php
namespace DownsMaster\Controllers;
use DownsMaster\Controllers\Controller;

Class CategoriaController extends Controller{
	public function CategoriaList($request, $response, $args)
	{
	    $stmt = $this->getConn->query("SELECT * FROM Categorias");
	    $categorias = $stmt->fetchAll($this->fetchAll);
	    //$categorias = json_encode($categorias);

		return $this->view->render(
			$response, 
			'categorias.twig',
			[
				'categorias' => $categorias
			]
		);
	}
}