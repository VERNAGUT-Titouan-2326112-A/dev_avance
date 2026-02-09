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

    public function generateFromDocument(string $filename, int $nbQuestions = 10, string $type = 'mcq'): array
    {
        $filePath = $this->projectDir . '/public/uploads/pdf/' . $filename;

        if (!file_exists($filePath)) {
            throw new \Exception("Fichier introuvable sur le serveur : " . $filePath);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        $text = substr($text, 0, 12000);

        return $this->callMistral($text, $nbQuestions, $type);
    }

    public function generateFromVideo(string $filename, int $nbQuestions = 10, string $type = 'mcq'): array
    {
        $filePath = $this->projectDir . '/public/uploads/video/' . $filename;
        if (!file_exists($filePath)) throw new \Exception("Fichier vidéo introuvable.");

        $text = $this->transcribeAudioWithGroq($filePath);

        $text = substr($text, 0, 15000);

        return $this->callMistral($text, $nbQuestions, $type);
    }

    private function callMistral(string $text, int $nbQuestions, string $type): array
    {
        if ($type === 'true_false') {
            $systemInstruction = "Tu es un générateur de quiz. Tu dois générer un questionnaire de type Vrai ou Faux.";
            $rules = "RÈGLES STRICTES : 1. Génère exactement $nbQuestions questions. 2. Chaque question doit avoir EXACTEMENT 2 réponses : 'Vrai' et 'Faux'. 3. Le champ 'type' dans le JSON doit être 'true_false'.";
            $jsonExample = '{
                "title": "Titre du Quiz",
                "questions": [
                    {
                        "text": "Le ciel est vert ?",
                        "points": 1,
                        "type": "true_false",
                        "answers": [
                            {"text": "Vrai", "isCorrect": false},
                            {"text": "Faux", "isCorrect": true}
                        ]
                    }
                ]
            }';
        } else {
            $systemInstruction = "Tu es un générateur de QCM. Tu dois générer un questionnaire à choix multiples.";
            $rules = "RÈGLES STRICTES : 1. Génère exactement $nbQuestions questions. 2. Chaque question doit avoir 4 choix de réponse. 3. Une seule réponse est correcte. 4. Le champ 'type' dans le JSON doit être 'multiple_choice'.";
            $jsonExample = '{
                "title": "Titre du Quiz",
                "questions": [
                    {
                        "text": "Quelle est la capitale de la France ?",
                        "points": 1,
                        "type": "multiple_choice",
                        "answers": [
                            {"text": "Paris", "isCorrect": true},
                            {"text": "Londres", "isCorrect": false},
                            {"text": "Berlin", "isCorrect": false},
                            {"text": "Madrid", "isCorrect": false}
                        ]
                    }
                ]
            }';
        }

        $finalPrompt = $systemInstruction . " " . $rules . " Le format de sortie doit être EXCLUSIVEMENT ce JSON : " . $jsonExample;

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
                        'content' => $finalPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => "Voici le contenu du cours à utiliser pour les questions :\n\n" . $text
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