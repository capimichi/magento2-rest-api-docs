<?php

require __DIR__ . '/vendor/autoload.php';

$data = json_decode(file_get_contents(__DIR__ . '/admin-schema.json'), true);

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection();

foreach ($data['paths'] as $endpoint => $path) {
	$section->addText($endpoint, [
		'name' => 'Arial',
		'size' => 20,
		'bold' => 'true'
	]);	
	$section->addText($data['basePath'] . $endpoint, [
		'name' => 'Arial',
		'size' => 11,
		'bold' => 'true'
	]);	
	$section->addTextBreak();

	foreach ($path as $method => $methodData) {
		$section->addText(strtoupper($method), [
			'name' => 'Arial',
			'size' => 14,
		]);	

		$section->addLine([
			'weight' => 1, 
			'width' => 450, 
			'height' => 0, 
			'color' => 'black'
		]);

		$section->addTextBreak();

		$section->addText($methodData['description'], [
			'name' => 'Arial',
			'size' => 12,
		]);	

		$section->addTextBreak();
		$section->addTextBreak();

		$section->addText('PARAMETRI', [
			'name' => 'Arial',
			'size' => 14,
			'color' => 'A9A9AD'
		]);	
		$section->addTextBreak();

		if(isset($methodData['parameters'])){
			foreach ($methodData['parameters'] as $key => $parameter) {

				$secondLine = [];
				if(isset($parameter['in'])){
					$secondLine[] = "In: " . $parameter['in'];
				}
				if(isset($parameter['type'])){
					$secondLine[] = "Type: " . $parameter['type'];
				}
				if(isset($parameter['description'])){
					$secondLine[] = "Description: " . $parameter['description'];
				}

				$section->addText($parameter['name'], [
					'name' => 'Arial',
					'size' => 11,
				]);
				$section->addText(implode(" - ", $secondLine), [
					'name' => 'Arial',
					'size' => 10,
					'color' => '4E4E51'
				]);
				if(isset($parameter['schema'])){
					
					$section->addTextBreak();
					foreach ($parameter['schema']['properties'] as $propertyName => $property) {

						$section->addText($propertyName, [
							'name' => 'Arial',
							'size' => 11,
						]);

						if(isset($property['$ref'])){
							$ref = $property['$ref'];
							$ref = str_replace("#/definitions/", "", $ref);

							$object = $data['definitions'][$ref]['properties'];

							$section->addText(json_encode($object, JSON_UNESCAPED_UNICODE), [
								'name' => 'Arial',
								'size' => 10,
								'color' => '4E4E51'
							]);
						}
					}
					$section->addTextBreak();
				}
				$section->addTextBreak();
			}
		}

		$section->addTextBreak();
		$section->addTextBreak();

		$section->addText('RISPOSTA', [
			'name' => 'Arial',
			'size' => 14,
			'color' => 'A9A9AD'
		]);	
		$section->addTextBreak();

		if(isset($methodData['responses'])){
			foreach ($methodData['responses'] as $responseCode => $response) {

				$section->addText($responseCode, [
					'name' => 'Arial',
					'size' => 11,
				]);
				$section->addText($response['description'], [
					'name' => 'Arial',
					'size' => 10,
					'color' => '4E4E51'
				]);
				if(isset($response['schema'])){
					
					$section->addTextBreak();

					if(isset($response['schema']['$ref'])){
						$ref = $response['schema']['$ref'];
						$ref = str_replace("#/definitions/", "", $ref);

						$object = $data['definitions'][$ref]['properties'];

						$section->addText(json_encode($object, JSON_UNESCAPED_UNICODE), [
							'name' => 'Arial',
							'size' => 10,
							'color' => '4E4E51'
						]);
					}
					$section->addTextBreak();
				}
				$section->addTextBreak();
			}
		}
	}


	$section->addPageBreak();
}

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save(__DIR__ . '/rest.docx');

foreach ($data['paths'] as $endpoint => $path) {

	foreach ($path as $method => $methodData) {

		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$section = $phpWord->addSection();

		$section->addText($endpoint, [
			'name' => 'Arial',
			'size' => 20,
			'bold' => 'true'
		]);	
		$section->addText($data['basePath'] . $endpoint, [
			'name' => 'Arial',
			'size' => 11,
			'bold' => 'true'
		]);	
		$section->addTextBreak();

		$section->addText(strtoupper($method), [
			'name' => 'Arial',
			'size' => 14,
		]);	

		$section->addLine([
			'weight' => 1, 
			'width' => 450, 
			'height' => 0, 
			'color' => 'black'
		]);

		$section->addTextBreak();

		$section->addText($methodData['description'], [
			'name' => 'Arial',
			'size' => 12,
		]);	

		$section->addTextBreak();
		$section->addTextBreak();

		$section->addText('PARAMETRI', [
			'name' => 'Arial',
			'size' => 14,
			'color' => 'A9A9AD'
		]);	
		$section->addTextBreak();

		if(isset($methodData['parameters'])){
			foreach ($methodData['parameters'] as $key => $parameter) {

				$secondLine = [];
				if(isset($parameter['in'])){
					$secondLine[] = "In: " . $parameter['in'];
				}
				if(isset($parameter['type'])){
					$secondLine[] = "Type: " . $parameter['type'];
				}
				if(isset($parameter['description'])){
					$secondLine[] = "Description: " . $parameter['description'];
				}

				$section->addText($parameter['name'], [
					'name' => 'Arial',
					'size' => 11,
				]);
				$section->addText(implode(" - ", $secondLine), [
					'name' => 'Arial',
					'size' => 10,
					'color' => '4E4E51'
				]);
				if(isset($parameter['schema'])){
					
					$section->addTextBreak();
					foreach ($parameter['schema']['properties'] as $propertyName => $property) {

						$section->addText($propertyName, [
							'name' => 'Arial',
							'size' => 11,
						]);

						if(isset($property['$ref'])){
							$ref = $property['$ref'];
							$ref = str_replace("#/definitions/", "", $ref);

							$object = $data['definitions'][$ref]['properties'];

							$section->addText(json_encode($object, JSON_UNESCAPED_UNICODE), [
								'name' => 'Arial',
								'size' => 10,
								'color' => '4E4E51'
							]);
						}
					}
					$section->addTextBreak();
				}
				$section->addTextBreak();
			}
		}

		$section->addTextBreak();
		$section->addTextBreak();

		$section->addText('RISPOSTA', [
			'name' => 'Arial',
			'size' => 14,
			'color' => 'A9A9AD'
		]);	
		$section->addTextBreak();

		if(isset($methodData['responses'])){
			foreach ($methodData['responses'] as $responseCode => $response) {

				$section->addText($responseCode, [
					'name' => 'Arial',
					'size' => 11,
				]);
				$section->addText($response['description'], [
					'name' => 'Arial',
					'size' => 10,
					'color' => '4E4E51'
				]);
				if(isset($response['schema'])){
					
					$section->addTextBreak();

					if(isset($response['schema']['$ref'])){
						$ref = $response['schema']['$ref'];
						$ref = str_replace("#/definitions/", "", $ref);

						$object = $data['definitions'][$ref]['properties'];

						$section->addText(json_encode($object, JSON_UNESCAPED_UNICODE), [
							'name' => 'Arial',
							'size' => 10,
							'color' => '4E4E51'
						]);
					}
					$section->addTextBreak();
				}
				$section->addTextBreak();
			}
		}

		$docPath = __DIR__ . '/docx/' . str_replace("/", "_", $endpoint) . "_" . $method . '.docx';
		$pdfPath = __DIR__ . '/docs/' . str_replace("/", "_", $endpoint) . "_" . $method . '.pdf';

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save($docPath);
		//\Gears\Pdf::convert($docPath, $pdfPath);

	}
}

