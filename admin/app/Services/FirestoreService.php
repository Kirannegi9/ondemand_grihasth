<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirestoreService
{
    private $projectId;
    private $collection;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->collection = 'rides'; // set default collection here
    }

    private function getAccessToken()
    {
        $jsonKey = json_decode(file_get_contents(storage_path('app/firebase/credentials.json')), true);

        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtClaimSet = base64_encode(json_encode([
            'iss' => $jsonKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ]));

        $jwtUnsigned = $jwtHeader . '.' . $jwtClaimSet;

        // Sign JWT
        openssl_sign($jwtUnsigned, $signature, $jsonKey['private_key'], 'sha256WithRSAEncryption');
        $jwtSigned = $jwtUnsigned . '.' . base64_encode($signature);

        // Exchange JWT for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwtSigned,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    public function clearCollection($collectionName = null)
    {
        $collection = $collectionName ?? $this->collection;
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return "Failed to fetch access token!";
        }

        // List documents
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";
        $documents = Http::withToken($accessToken)->get($url)->json();

        if (!isset($documents['documents'])) {
            return "No documents found in $collection";
        }

        // Delete each document
        foreach ($documents['documents'] as $doc) {
            $docName = $doc['name']; // full path
            Http::withToken($accessToken)->delete("https://firestore.googleapis.com/v1/$docName");
        }

        return "Collection $collection cleared!";
    }
}
