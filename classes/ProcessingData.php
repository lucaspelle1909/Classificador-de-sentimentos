<?php

class ProcessingData {

	private $dir_base;

	private $files;

	private $percent_for_training = 0.75;

	public $data;

	private $frequency;

	function __construct(){

		$this->dir_base = "C:/xampp/htdocs/analise_sentimentos/arquivos_br/";

		$this->files = array(
			"amazon_cells_labelled.txt",
			"imdb_labelled.txt",
			"yelp_labelled.txt"
		);

		$this->data = array();
		$this->frequency = array();
	}

	function processFiles(){
		$dados = $this->readFiles();
		$this->formatData($dados);
	}

	function formatData($data){

		$comments = array();

		foreach ($data as $key => $comentario) {
			
			//verifica se o comentário está divido em 'comentario' + (tab) + '0 ou 1'
			if(count(explode("\t", $comentario)) == 2){

				//verifica se o texto do comentário não está vazio
				if(!empty(explode("\t", $comentario)[0])){

					$comentario = $this->formatWord($comentario);

					array_push($comments, explode("\t", $comentario));
				}
			}
		}

		$this->data = $this->splitTrainingAndTest($comments);
	}

	function formatWord($comentario){

		$comentario = str_replace(".", "", $comentario);
		$comentario = str_replace("-", "", $comentario);
		$comentario = str_replace(",", "", $comentario);
		$comentario = str_replace("!", "", $comentario);

		$comentario = strtolower($comentario);

		return $comentario;
	}

	function splitTrainingAndTest($comments){

		$array_test = array();
		$array_training = array();

		$quantidade_total = count($comments);

		foreach ($comments as $key => $comment) {

			if($key < $quantidade_total * $this->percent_for_training){
				array_push($array_training, $comment);
			}else{
				array_push($array_test, $comment);
			}
		}

		return array(
			"tests" => $array_test,
			"training" => $array_training
		);
	}

	function readFiles(){

		$conteudo = "";

		foreach ($this->files as $key => $value) {
			$conteudo .= file_get_contents($this->dir_base . $value);
		}

		return explode("\n", $conteudo);
	}


	function getFrequency(){

		foreach ($this->data['training'] as $key => $comment) {

			foreach (explode(" ", $comment[0]) as $key2 => $value) {
				if(!isset($this->frequency[$value])){

					$this->frequency[$value] = array(
						"frequency" => 1,
						"frequency_positive" => 0
					);
				}else{
					$this->frequency[$value]['frequency']++;
				}

				if(intval($comment[1]) == 1){
					$this->frequency[$value]['frequency_positive']++;
				}
			}
		}
	}

	function calculatePoints(){
		foreach ($this->frequency as $key => $value) {
			$this->frequency[$key]["points"] = $value['frequency_positive'] / $value['frequency']; 
		}
	}

	function getAvaliation($frase){
		$frase_original = $frase;

		$frase = explode(" ", $this->formatWord($frase));

		$prob_positive = 1;

		$prob_negative = 1;

		foreach ($frase as $key => $value) {
			if(isset($this->frequency[$value])){
				$prob_positive *= $this->frequency[$value]['points'];
				$prob_negative *= 1 - $this->frequency[$value]['points'];
			}else{
				//echo "<br> A palavra " . $value . " não foi encontrada na base de dados <br>";
			}
		}

		$prob_total = $prob_positive + $prob_negative;

		if($prob_total == 0){
			return -1;
		}

		$prob_positive /= $prob_total;

		if($prob_positive > 0.5){
			return 1;
		}else{
			return 0;
		}

		//echo $frase_original . " - Positive:  " . number_format($prob_positive / $prob_total * 100, 2) . "%   Negative: " . number_format($prob_negative / $prob_total * 100, 2) . "% <br>";
	}
}

?>