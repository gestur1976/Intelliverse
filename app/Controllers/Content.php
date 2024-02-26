<?php

namespace App\Controllers;

use App\Models\Article;
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

    public static function getOpenAIResponse(string $prompt, array $messages = null): string
    {
        $openAI = Services::OpenAI();
        if (!$messages) {
            $messages = [
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
            ];
        }
        $complete = $openAI->chat([
            'model' => env('OPENAI_MODEL'),
            $messages,
            'temperature' => 0.2,
            'max_tokens' => 4092,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $jsonComplete = json_decode($complete);
        return $jsonComplete->choices[0]->message->content;
    }

    public static function generateFromTopic(string $slug): ?array
    {
        $topic = html_entity_decode($slug);
        $topic = str_replace('-', ' ', $topic);
        $prompt = "Output in JSON a non associative array of 20 invented $topic article titles oriented to capture the ".
            "readers attention. Don't write anything else than the json content! Don't put \"articles\" key for the ".
            "array, just start with the first element until last one.";

        self::getOpenAIResponse($prompt);
        // Convert the JSON response into an array of titles
        $titles = json_decode($content);

        // Create an associative array of slugs and titles
        return $titles ?? self::generateSlugsFromAnchors($titles);
    }

    public static function setArticleTitleFromSlug(Article $article): Article
    {
        $title = self::getArticleTitleFromSlug($article->getTargetSlug());
        $article->setTitle($title);

        return $article;
    }

    public static function generateNextArticle(Article $article): Article
    {
        $sourceTitle = $article->getTitle() ?? self::getArticleTitleFromSlug($article->getSourceSlug());
        $prompt = 'Generate a blog article. The title is "' . $article->getTitle() . '". The ' .
            'referral page title is "' . $sourceTitle .'" and it has a link to this article ' .
            'with "'. $sourceTitle .'" as anchor text. Use the previous page as context ' .
            'to write about the right subject because a simple title could apply to many ' .
            'contexts. The article has to be interesting, easy to read, entertaining and ' .
            'has to capture the reader\'s attention, Write it in an easy to understand ' .
            'language, use examples or analogies if a concept is difficult to understand ' .
            'and eventually write something funny if possible. Write more than 10 paragraphs ' .
            'and don\'t be repetitve. Write the title and the content in a JSON associative ' .
            'array with "title" and "content" as keys. "content" will contain another array ' .
            'with each paragraph written. Just output raw JSON and nothing else.';

        $content = self::getOpenAIResponse($prompt);

        // Convert the JSON response into an array of titles
        $jsonData = json_decode($content);

        $article->setTitle($jsonData->title);
        $article->setContentParagraphs($jsonData->content);


        // Create an associative array of slugs and titles
        return $article;
    }

    public static function createGlossaryOfTermsChatMessages(Article $article): array
    {
        $previousPrompt = 'Generate a blog article. The title is "' . $article->getTitle() . '". The ' .
            'referral page title is "' . $sourceTitle .'" and it has a link to this article ' .
            'with "'. $sourceTitle .'" as anchor text. Use the previous page as context ' .
            'to write about the right subject because a simple title could apply to many ' .
            'contexts. The article has to be interesting, easy to read, entertaining and ' .
            'has to capture the reader\'s attention, Write it in an easy to understand ' .
            'language, use examples or analogies if a concept is difficult to understand ' .
            'and eventually write something funny if possible. Write more than 10 paragraphs ' .
            'and don\'t be repetitve. Write the title and the content in a JSON associative ' .
            'array with "title" and "content" as keys. "content" will contain another array ' .
            'with each paragraph written. Just output raw JSON and nothing else.';

        // We encode the $paragraph array into JSON text.
        $previousAnswer = json_encode($article->getContentParagraphs());

        $prompt = 'Write an array with 8 terms for a glossary with article related terms. ' .
            'Create an associative array in JSON with the keys "term" for the term and ' .
            '"definition" for term definition.';

        $messages = [
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant.",
                ],
                [
                    "role" => "user",
                    "content" => $previousPrompt,
                ],
                [
                    "role" => "assistant",
                    "content" => $previousAnswer,
                ],
                [
                    "role" => "user",
                    "content" => $prompt,
                ],
            ],
        ];

        $content = self::getOpenAIResponse($prompt, $messages);
        $termsArray = json_decode($content);
        $article->getGlossaryOfTerms($termsArray);

        return $article;
    }
}

