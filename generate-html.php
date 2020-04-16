<?php

require __DIR__ . '/vendor/autoload.php';

$data = json_decode(file_get_contents(__DIR__ . '/admin-schema.json'), true);

printf("| Endpoint        | Method             | %s | ------------- | -----:| %s", PHP_EOL, PHP_EOL);

foreach ($data['paths'] as $endpoint => $path) {

    foreach ($path as $method => $methodData) {

        $upMethod = strtoupper($method);

        $content = '';

        $content .= "<p style='font-family: Arial; font-size: 20px;'>{$endpoint}</p>";
        $content .= "<p style='font-family: Arial; font-size: 11px;'>{$data['basePath']}{$endpoint}</p>";
        $content .= "<p></p>";
        $content .= "<p style='font-family: Arial; font-size: 14px;'>{$upMethod}</p>";
        $content .= "<hr>";
        $content .= "<p></p>";
        $content .= "<p style='font-family: Arial; font-size: 12px;'>{$methodData['description']}</p>";
        $content .= "<p></p>";
        $content .= "<p></p>";
        $content .= "<p style='font-family: Arial; font-size: 14px; color: #A9A9AD;'>PARAMETRI</p>";
        $content .= "<p></p>";

        if (isset($methodData['parameters'])) {
            foreach ($methodData['parameters'] as $key => $parameter) {

                $secondLine = [];
                if (isset($parameter['in'])) {
                    $secondLine[] = "In: " . $parameter['in'];
                }
                if (isset($parameter['type'])) {
                    $secondLine[] = "Type: " . $parameter['type'];
                }
                if (isset($parameter['description'])) {
                    $secondLine[] = "Description: " . $parameter['description'];
                }
                $secondLine = implode(" - ", $secondLine);

                $content .= "<p style='font-family: Arial; font-size: 11px;'>{$parameter['name']}</p>";
                $content .= "<p style='font-family: Arial; font-size: 10px; color: #4E4E51;'>{$secondLine}</p>";

                if (isset($parameter['schema'])) {

                    $content .= "<p></p>";
                    $object = [];
                    foreach ($parameter['schema']['properties'] as $propertyName => $property) {

                        $object[$propertyName] = [];
                        $content .= "<p style='font-family: Arial; font-size: 11px;'>{$propertyName}</p>";

                        if (isset($property['$ref'])) {
                            $ref = $property['$ref'];
                            $ref = str_replace("#/definitions/", "", $ref);

                            $object[$propertyName] = $data['definitions'][$ref]['properties'];
                        }
                    }
                    $json = json_encode($object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

                    for ($j = 0; $j < 2; $j++) {
                        if (preg_match_all('/"#\/definitions\/(.*?)"/', $json, $matches)) {
                            $max = max(count($matches[0]), count($matches[1]));
                            for ($i = 0; $i < $max; $i++) {
                                $definitionKey = $matches[1][$i];
                                $replaceKey = $matches[0][$i];
                                if (isset($data['definitions'][$definitionKey]['properties'])) {
                                    $json = str_replace($replaceKey, json_encode($data['definitions'][$definitionKey]['properties']), $json);
                                    $json = json_decode($json, true);
                                    $json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                                }
                            }
                        }
                    }

                    $content .= "<pre style='font-family: Arial; font-size: 10px;'>{$json}</pre>";
                    $content .= "<p></p>";
                }
                $content .= "<p></p>";
            }
        }

        $content .= "<p></p>";
        $content .= "<p></p>";

        $content .= "<p style='font-family: Arial; font-size: 14px; color: #A9A9AD;'>RISPOSTA</p>";
        $content .= "<p></p>";

        if (isset($methodData['responses'])) {
            foreach ($methodData['responses'] as $responseCode => $response) {

                $content .= "<p style='font-family: Arial; font-size: 11px;'>{$responseCode}</p>";
                $content .= "<p style='font-family: Arial; font-size: 10px; color: #4E4E51;'>{$response['description']}</p>";
                if (isset($response['schema'])) {

                    $content .= "<p></p>";

                    if (isset($response['schema']['$ref'])) {
                        $ref = $response['schema']['$ref'];
                        $ref = str_replace("#/definitions/", "", $ref);

                        $object = $data['definitions'][$ref]['properties'];

                        $json = json_encode($object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

                        for ($j = 0; $j < 2; $j++) {
                            if (preg_match_all('/"#\/definitions\/(.*?)"/', $json, $matches)) {
                                $max = max(count($matches[0]), count($matches[1]));
                                for ($i = 0; $i < $max; $i++) {
                                    if (
                                        isset($matches[00][$i])
                                        && isset($matches[1][$i])
                                    ) {
                                        $definitionKey = $matches[1][$i];
                                        $replaceKey = $matches[0][$i];

                                        if (isset($data['definitions'][$definitionKey]['properties'])) {
                                            $json = str_replace($replaceKey, json_encode($data['definitions'][$definitionKey]['properties']), $json);
                                            $json = json_decode($json, true);
                                            $json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                                        }
                                    }
                                }
                            }
                        }

                        $content .= "<pre style='font-family: Arial; font-size: 10px;'>{$json}</pre>";
                    }
                    $content .= "<p></p>";
                }
                $content .= "<p></p>";
            }
        }

        $dirEndpoint = str_replace([
//            "{", "}",
        ], "_", $endpoint);

        $docUrl = sprintf("http://htmlpreview.github.io/?https://github.com/capimichi/magento2-rest-api-docs/master/html%s/%s.html", $endpoint, $method);
        $docUrl = sprintf("| [%s](%s) | %s |", $endpoint, $docUrl, strtoupper($method));

        printf("%s %s", $docUrl, PHP_EOL);

        $docPath = __DIR__ . '/html' . $dirEndpoint . "/" . $method . '.html';
        $docDir = dirname($docPath);
        if (!file_exists($docDir)) {
            mkdir($docDir, 0777, true);
        }

        $html = "<html><head><style>

.content{
max-width: 700px;
width: 100%;
margin: 20px auto;
}

pre{
background-color: rgb(38, 50, 56);
display: block;
color: #ffffff;
padding: 10px;
}
</style></head><body><div class='content'>{$content}</div></body></html>";

        file_put_contents($docPath, $html);
    }
}

