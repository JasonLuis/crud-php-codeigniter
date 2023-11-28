<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Projeto extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');
	}

	public function add()
	{
		$json_data = file_get_contents('php://input');
		$data = json_decode($json_data, true);

		if (!$data) {
			echo json_encode(['error' => 'JSON inválido.']);
			return;
		}

		$projeto = new Entity\Projeto;
		$projeto->setDescricao($data['descricao']);
		$this->doctrine->em->persist($projeto);
		$this->doctrine->em->flush();

		echo json_encode(['message' => 'Proejto criada com sucesso.']);
	}

	public function update($id)
	{
		$json_data = file_get_contents('php://input');
		$data = json_decode($json_data, true);

		if (!$data) {
			echo json_encode(['error' => 'JSON inválido.']);
			return;
		}

		$projeto = $this->doctrine->em->find("Entity\Projeto", $id);

		if (!$projeto) {
			echo json_encode(['error' => 'Projeto não encontrado.']);
			return;
		}

		$projeto->setDescricao($data['descricao']);

		$this->doctrine->em->persist($projeto);
		$this->doctrine->em->flush();

		echo json_encode(['message' => 'Projeto atualizado com sucesso.']);
	}

	public function delete($id)
	{

		$projeto = $this->doctrine->em->find("Entity\Projeto", $id);

		if (!$projeto) {
			echo json_encode(['error' => 'Projeto não encontrado.']);
			return;
		}

		$atividades = $this->doctrine->em->getRepository("Entity\Atividade")->findBy(array("idProjeto" => $id));

		foreach ($atividades as $ativadade) {
			$this->doctrine->em->remove($ativadade);
			$this->doctrine->em->flush();
		}

		$this->doctrine->em->remove($projeto);
		$this->doctrine->em->flush();

		echo json_encode(["message" => "Projeto deletado com sucesso."]);
	}

	public function listar() {
		$data = [];
		$projetos = $this->doctrine->em->getRepository("Entity\Projeto")->findAll();

		foreach ($projetos as $projeto) {
			$data[] = [
				"id" => $projeto->getId(),
				"descricao" => $projeto->getDescricao()
			];
		}
	
		echo json_encode($data);
	}
}
