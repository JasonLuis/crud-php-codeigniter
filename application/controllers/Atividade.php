<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Atividade extends CI_Controller{
	function __construct(){
		parent::__construct();
		header('Content-Type: application/json');
	}
	
	public function projeto($id){
		$data = [];
		$atividades = $this->doctrine->em->getRepository("Entity\Atividade")
									 ->findBy(array("idProjeto"=>$id),array("dataCadastro"=>"asc"));	
		foreach($atividades as $ativadade){
			$data[] = [
				"id"=>$ativadade->getId(),
				"data"=>$ativadade->getDataCadastro(),
				"descricao"=>$ativadade->getDescricao()
			];
		}				 			
		echo json_encode($data);
    }

    public function get($id){
		$data = [];
		$atividade = $this->doctrine->em->find("Entity\Atividade",$id);
		
		if(!$atividade) {
			echo json_encode(['error' => 'Atividade não encontrada.']);
			return;
		}
		$data[] = [
            "id"=>$atividade->getId(),
            "data"=>$atividade->getDataCadastro(),
            "descricao"=>$atividade->getDescricao()
        ];			 			
		echo json_encode($data);
    }

	public function delete($id) {
		$atividade = $this->doctrine->em->find("Entity\Atividade", $id);
		if ($atividade !== null) {
            $this->doctrine->em->remove($atividade);
            $this->doctrine->em->flush();

            echo json_encode(['message' => 'Atividade deletada com sucesso.']);
        } else {
            echo json_encode(['error' => 'Atividade não encontrada.']);
        }
	}

	public function add() {
		$json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

		if (!$data) {
            echo json_encode(['error' => 'JSON inválido.']);
            return;
        }

		$projeto = $this->doctrine->em->find("Entity\Projeto", $data['idProjeto']);

		if(!$projeto) {
			echo json_encode(['error' => 'Projeto não encontrado']);
            return;
		}

		$atividade = new Entity\Atividade;
		$atividade->setDescricao($data['descricao']);
		$atividade->setDataCadastro(date("Y-m-d H:i:s"));
		$atividade->setIdProjeto($projeto);

		$this->doctrine->em->persist($atividade);
		$this->doctrine->em->flush();

		echo json_encode(['message' => 'Atividade criada com sucesso.']);
	}

	public function update($id) {
		$json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

		if (!$data) {
            echo json_encode(['error' => 'JSON inválido.']);
            return;
        }

		$atividade = $this->doctrine->em->find("Entity\Atividade", $id);

		if(!$atividade) {
			echo json_encode(['error' => 'Atividade não encontrada.']);
			return;
		}

		$projeto = $this->doctrine->em->find("Entity\Projeto", $data['idProjeto']);

		if(!$projeto) {
			echo json_encode(['error' => 'Projeto não encontrado']);
            return;
		}

		$atividade->setDescricao($data['descricao']);
		$atividade->setIdProjeto($projeto);

		$this->doctrine->em->persist($atividade);
        $this->doctrine->em->flush();

		echo json_encode(['message' => 'Atividade atualizada com sucesso.']);
	}
    
}
