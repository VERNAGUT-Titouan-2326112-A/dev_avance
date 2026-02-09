<?php

namespace App\Service;

use Smalot\PdfParser\Parser;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class QuizGeneratorService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $groqKey;
    private string $projectDir;

    public function __construct(
        HttpClientInterface $httpClient,
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire(env: 'MISTRAL_API_KEY')] string $apiKey,
        #[Autowire(env: 'GROQ_API_KEY')] string $groqKey
    ) {
        $this->httpClient = $httpClient;
        $this->projectDir = $projectDir;
        $this->apiKey = $apiKey;
        $this->groqKey = $groqKey;
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

    public function generateFromVideo(string $filename): array
    {
        $filePath = $this->projectDir . '/public/uploads/video/' . $filename;
        if (!file_exists($filePath)) throw new \Exception("Fichier vidéo introuvable.");

        $text = $this->transcribeAudioWithGroq($filePath);

        $text = substr($text, 0, 15000);

        return $this->callMistral($text);
    }

    private function callMistral(string $text): array
    {
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
                'model' => 'mistral-small-latest',
                'response_format' => ['type' => 'json_object'],
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

    private function transcribeAudioWithGroq(string $filePath): string
    {
        $formFields = [
            'file' => DataPart::fromPath($filePath),
            'model' => 'whisper-large-v3',
            'response_format' => 'json'
        ];
        $formData = new FormDataPart($formFields);

        $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/audio/transcriptions', [
            'headers' => [
                    'Authorization' => 'Bearer ' . $this->groqKey,
                    'Content-Type' => 'multipart/form-data',
                ] + $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Erreur Groq : " . $response->getContent(false));
        }

        $data = $response->toArray();
        return $data['text'] ?? '';
    }
}