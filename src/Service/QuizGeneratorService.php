<?php

namespace App\Service;

use Smalot\PdfParser\Parser;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class QuizGeneratorService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $projectDir;

    public function __construct(
        HttpClientInterface $httpClient,
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire(env: 'MISTRAL_API_KEY')] string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->projectDir = $projectDir;
        $this->apiKey = $apiKey;
    }

    public function generateFromDocument(string $filename): array
    {
        $filePath = $this->projectDir . '/public/uploads/pdf/' . $filename;

        if (!file_exists($filePath)) {
            throw new \Exception("Fichier introuvable sur le serveur : " . $filePath);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        $text = substr($text, 0, 12000);

        return $this->callMistral($text);
    }

    private function callMistral(string $text): array
    {
        // Structure JSON attendue
        $jsonStructure = '{
            "title": "Titre du QCM",
            "questions": [
                {
                    "text": "Question ?",
                    "points": 1,
                    "type": "multiple_choice",
                    "answers": [
                        {"text": "Reponse A", "isCorrect": true},
                        {"text": "Reponse B", "isCorrect": false}
                    ]
                }
            ]
        }';

        $response = $this->httpClient->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'model' => 'mistral-small-latest', // 👈 Modèle Mistral (rapide et efficace)
                'response_format' => ['type' => 'json_object'], // Mistral supporte le mode JSON
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert pédagogique. Génère un QCM au format JSON strict respectant cette structure : ' . $jsonStructure
                    ],
                    [
                        'role' => 'user',
                        'content' => "Voici le cours :\n\n" . $text
                    ]
                ]
            ]
        ]);

        $content = $response->toArray()['choices'][0]['message']['content'];
        return json_decode($content, true);
    }
}