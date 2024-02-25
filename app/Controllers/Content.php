<?php

namespace App\Controllers;

use Config\Services;
use Orhanerday\OpenAi\OpenAi;

class Content
{
    private OpenAi $openAi;
    private const TOPICS = ["World", "Business", "Technology", "Sports", "Entertainment", "Science", "Health", "People", "Art", "Education"];

    public function __construct()
    {
        $this->openAi = Services::openai();
    }

    /*
     * In this function we create an associative array using the passed array values as keys and the values
     * are the keys lowercased and spaces are replaced with hyphens and the rest of characters are escaped
     * into html entities to create slugs to be used as links.
     */
    public function generateSlugFromText(string $title): array
    {
        $slug = str_replace(' ', '-', strtolower($title));
        $slug = htmlentities($slug, ENT_QUOTES, 'UTF-8');

        return [
            "slug" => $slug,
            "title" => $title
        ];
    }

    public function generateSlugsFromTopics(array $topics): array
    {
        $slugs = [];
        foreach ($topics as $topic) {
            $slugs[] = $this->generateSlugFromText($topic);
        }
        return $slugs;
    }

    public function getTopicsArray(): array
    {
        return self::TOPICS;
    }

    public function generateFromTopic(array $topic): ?string
    {
        if (empty($this->openAi)) {
            return null;
        }
        $topicSlug = array_key_last($topic);
        $topicText = $topic[$topicSlug];

        $prompt = "Generate a 80 lines blog article about " . $topicText;
        $complete = $this->openAI->chat([
            'model' => env('OPENAI_MODEL'),
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => 8192,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $jsonCompletion = json_decode($complete, false, 512, JSON_THROW_ON_ERROR);
        return $jsonCompletion->choices[0]->message->content;
    }
}
