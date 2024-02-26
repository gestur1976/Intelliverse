<?php

namespace App\Controllers;

use Config\Services;

class Content
{
    private const TOPICS = ["World", "Business", "Technology", "Sports", "Entertainment", "Science", "Health", "People", "Art", "Education"];

    /*
     * In this function we create an associative array using the passed array values as keys and the values
     * are the keys lower cased and spaces are replaced with hyphens and the rest of characters are escaped
     * into html entities to create slugs to be used as links.
     */
    public static function generateSlugFromAnchor(string $title): string
    {
        $slug = str_replace(' ', '-', strtolower($title));
        return htmlentities($slug, ENT_QUOTES, 'UTF-8');
    }

    public static function generateSlugsFromAnchors(array $topics): array
    {
        $slugs = [];
        foreach ($topics as $topic) {
            $slugs[self::generateSlugFromAnchor($topic)] = $topic;
        }
        return $slugs;
    }

    public static function getArticleTitleFromSlug(string $slug): string
    {
        // We decode the html entities, remove the hyphens and capitalize the letters
        $title = html_entity_decode($slug);
        return ucwords(str_replace('-', ' ', $title));
    }

    public static function getTopicsArray(): array
    {
        return self::TOPICS;
    }

    public static function generateFromTopic(string $slug): ?array
    {
        $topic = html_entity_decode($slug);
        $topic = str_replace('-', ' ', $topic);
        $openAI = Services::OpenAI();
        $prompt = "Output in JSON a non associative array of 20 invented $topic article titles oriented to capture the ".
            "readers attention. Don't write anything else than the json content! Don't put \"articles\" key for the ".
            "array, just start with the first element until last one.";

        //  $prompt = "Generate a list of 20 hypothetical ".$topic." blog articles with titles oriented ".
        //      "to capture the reader's attention. The output will be an array of the titles in JSON format. ".
        //      "Don't output anything else, just raw JSON, no headers, no HTML and no escaped characters";

        $complete = $openAI->chat([
            'model' => env('OPENAI_MODEL'),
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant.",
                ],
                [
                    "role" => "user",
                    "content" => $prompt,
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => 4092,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $jsonComplete = json_decode($complete);
        $content = $jsonComplete->choices[0]->message->content;

        // Convert the JSON response into an array of titles
        $titles = json_decode($content);

        // Create an associative array of slugs and titles
        return $titles ?? self::generateSlugsFromAnchors($titles);
    }
}
