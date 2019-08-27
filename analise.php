<?php

require("classes/ProcessingData.php");

$processamento = new ProcessingData();

	//pega os arquivos com os comentários e separa 75% para treino e 25% para testes;	
$processamento->processFiles();

	//pega a frequência de cada palavra em frases positivas
$processamento->getFrequency();

	//calcula os pontos conforme a frequência
$processamento->calculatePoints();



	//Pronto para testes

$test_comments = $processamento->data['tests'];

$acertos = 0;

$verdadeiros_positivos = 0;
$verdadeiros_negativos = 0;

$falsos_positivos = 0;
$falsos_negativos = 0;

$desconsiderar = 0;

foreach ($test_comments as $key => $value) {
	$result_ok = $value[1];

	$result_test = $processamento->getAvaliation($value[0]);

	if($result_test == -1){
		$desconsiderar++;
	}

	if($result_test == 0){
		
		if($result_ok == 0){
			$verdadeiros_negativos++;
		}else{
			$falsos_negativos++;
		}

	}else{

		if($result_ok == 1){
			$verdadeiros_positivos++;
		}else{
			$falsos_positivos++;
		}

	}

	if($result_ok == $result_test){
		$acertos++;
	}
}

echo "Acertos: " . $acertos . " , Total: " . (count($test_comments) - $desconsiderar);

echo ", Taxa de acertos: " . $acertos / (count($test_comments) - $desconsiderar) * 100;

echo "<br><br> -- Mais dados --<br><br>";

echo "Verdadeiros positivos: " . $verdadeiros_positivos . "<br>";
echo "Falsos positivos: " . $falsos_positivos . "<br><br>";

echo "Verdadeiros negativos: " . $verdadeiros_negativos . "<br>";
echo "Falsos negativos: " . $falsos_negativos . "<br><br>";

	//$frase = $_POST['frase'];

	//$processamento->getAvaliation($frase);


?>