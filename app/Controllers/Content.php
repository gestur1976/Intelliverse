<?php

namespace App\Controllers;

use App\Models\Article;
use Config\Services;

class Content
{
    /*
     * In this function we create an associative array using the passed array values as keys and the values
     * are the keys lower cased and spaces are replaced with hyphens and the rest of characters are escaped
     * into html entities to create slugs to be used as links.
     */
    public static function generateSlugFromAnchor(string $title): string
    {
        // We replace all occurrences of non-alphanumeric characters with hyphens
        return preg_replace('/[^a-z^0-9]/', '-', strtolower($title));
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

    public static function generateTopicsArray(): array
    {
        $prompt = 'Create a non associative array of 10 titles for articles about ' . $topic. '. ' .
            'The titles must catch the reader\'s attention and will be relevant in ' . $topic .
            ' and they must use the right slang. ' .
            'Be diverse and don\'t write similar elements. Output in JSON the array. ```json';
        $titles = null;
        while (!$titles) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            // Convert the JSON response into an array of titles
            $titles = json_decode($content, false);
            $titles = self::extractValues($titles);
        }
        $articles = [];
        foreach ($titles as $title) {
            $slug = self::generateSlugFromAnchor($title);
            $articles[] = [
                "slug" => $slug,
                "title" => $title,
            ];
        }
        return $articles;
    }

    public static function getOpenAIResponse(string $prompt, array $previousMessages = null): string
    {
        $examplePrompt = 'You encode PHP arrays into JSON objects. For non associative arrays of strings like ["String 1", "String 2" and "String 3"] you will output ["String 1", "String 2", "String 3"]. And for associative arrays like [ "strings" => ["String 1", "String 2", "String 3", ], ]  you will output: {"strings": ["String 1", "String 2", "String 3"]}. Output in JSON: [ "examples" => [ "foo", "bar", "John Doe" ] ]. ```json';
        $exampleResponse = '```json
{
  "examples": [
    "foo",
    "bar",
    "John Doe"
  ]
}
```';

        $messages = [
            [
                "role" => "system",
                "content" => "You are a helpful assistant who answers exactly what you asked for without giving any explanation.",
            ],
            [
                "role" => "user",
                "content" => $examplePrompt,
            ],
            [
                "role" => "assistant",
                "content" => $exampleResponse,
            ],
        ];

        if (!$messages) {
            $messages [] = $previousMessages;
        } else {
            $messages [] = [
                'role' => "user",
                'content' => $prompt,
            ];
        }

        $openAI = Services::OpenAI();
        $content = $openAI->chat([
            'model' => env('OPENAI_MODEL'),
            'messages' => $messages,
            'temperature' => 2,
            'max_tokens' => 16384,
            'frequency_penalty' => 0.1,
            'presence_penalty' => 0.1,
        ]);
        $jsonComplete = json_decode($content);
        return $jsonComplete->choices[0]->message->content;
    }

    /*
     * This function minifies a JSON string.
     * Got it from: https://github.com/t1st3/php-json-minify/blob/master/src/t1st3/JSONMin/JSONMin.php
     */

    public static function minifyJSON ($json) {
        $tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
        $in_string = false;
        $in_multiline_comment = false;
        $in_singleline_comment = false;
        $tmp; $tmp2; $new_str = array(); $ns = 0; $from = 0; $lc; $rc; $lastIndex = 0;
        while (preg_match($tokenizer,$json,$tmp,PREG_OFFSET_CAPTURE,$lastIndex)) {
            $tmp = $tmp[0];
            $lastIndex = $tmp[1] + strlen($tmp[0]);
            $lc = substr($json,0,$lastIndex - strlen($tmp[0]));
            $rc = substr($json,$lastIndex);
            if (!$in_multiline_comment && !$in_singleline_comment) {
                $tmp2 = substr($lc,$from);
                if (!$in_string) {
                    $tmp2 = preg_replace("/(\n|\r|\s)*/","",$tmp2);
                }
                $new_str[] = $tmp2;
            }
            $from = $lastIndex;
            if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
                preg_match("/(\\\\)*$/",$lc,$tmp2);
                if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) { // start of string with ", or unescaped " character found to end string
                    $in_string = !$in_string;
                }
                $from--; // include " character in next catch
                $rc = substr($json,$from);
            }
            else if ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
                $in_multiline_comment = true;
            }
            else if ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) {
                $in_multiline_comment = false;
            }
            else if ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
                $in_singleline_comment = true;
            }
            else if (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment && $in_singleline_comment) {
                $in_singleline_comment = false;
            }
            else if (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/",$tmp[0]))) {
                $new_str[] = $tmp[0];
            }
        }
        if (!isset($rc)) {
            $rc = $json;
        }
        $new_str[] = $rc;
        return implode("",$new_str);
    }

    /*
     * This function trims extra text before and after a JSON object,
     * something usual in ChatGPT responses (```json, ...).
     */
    public static function trimJSON(string $textContainingJSON): ?string
    {
        $tmpJSON = $textContainingJSON;
        $squareBracketStartPos = strpos($tmpJSON, '[');
        $curlyBracketStartPos = strpos($tmpJSON, '{');
        $startPos = false;
        if ($curlyBracketStartPos !== false && $squareBracketStartPos !== false) {
            $startPos = min($squareBracketStartPos, $curlyBracketStartPos);
        } else {
            if ($squareBracketStartPos === false) {
                $startPos = $curlyBracketStartPos;
            }
            if ($curlyBracketStartPos === false) {
                $startPos = $squareBracketStartPos;
            }
            if ($startPos === false) {
                return false;
            }
        }
        $tmpJSON = substr($tmpJSON, $startPos);

        // Now backwards

        $tmpJSON = strrev($tmpJSON);

        $squareBracketEndPos = strpos($tmpJSON, ']');
        $curlyBracketEndPos = strpos($tmpJSON, '}');
        $endPos = false;
        if ($curlyBracketEndPos !== false && $squareBracketEndPos !== false) {
            $endPos = min($squareBracketEndPos, $curlyBracketEndPos);
        } else {
            if ($squareBracketEndPos === false) {
                $endPos = $curlyBracketEndPos;
            }
            if ($curlyBracketEndPos === false) {
                $endPos = $squareBracketEndPos;
            }
            if ($endPos === false) {
                return false;
            }
        }
        $tmpJSON = substr($tmpJSON, $endPos);

        return strrev($tmpJSON);
    }

    /*
     * Generate an array of values if the given array is an associative array,
     * an array of stdObjects or just a simple non-associative array.
     */
    public static function extractValues(mixed $amalgamatedArray): array
    {
        $values = [];
        if (is_array($amalgamatedArray)) {
            foreach ($amalgamatedArray as $key => $value) {
                if (is_string($value)) {
                    $values[$key] = \ucfirst($value);
                } elseif ($value instanceof \stdClass) {
                    array_merge($values, self::extractValues($value));
                }
            }
        }
        elseif ($amalgamatedArray instanceof \stdClass) {
            $objectVars = get_object_vars($amalgamatedArray);
            foreach ($objectVars as $var) {
                array_merge($values, self::extractValues($var));
            }
            return $values;
        }
        elseif (is_string($amalgamatedArray)) {
            return [\ucfirst($amalgamatedArray)];
        }
        return $values;
    }

    /*
     * This function generates a list of categories to show at the homepage.
     */
    public static function generateCategoriesArray(): array
    {
        $prompt = 'Generate a non associative array of 20 categories for a blog homepage. Sort them ' .
            'from most interesting to less interesting, but all of them must be interesting to the ' .
            '80% of the people. Output the non associative array of categories in JSON without keys, ' .
            'only values .\n```json';
        $categories = null;
        while (!$categories) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            // Convert the JSON response into an array of titles
            $categories = json_decode($content, false);
            $categories = self::extractValues($categories);
        }
        $categoriesArray = [];
        foreach ($categories as $category) {
            $slug = self::generateSlugFromAnchor($category);
            $categoriesArray[] = [
                "slug" => $slug,
                "title" => $category,
            ];
        }
        return $categoriesArray;
    }



    public static function generateFromTopic(string $slug): ?array
    {
        $topic = html_entity_decode($slug);
        $topic = str_replace('-', ' ', $topic);
        $prompt = 'Create a non associative array of 12 titles for articles about ' . $topic. '. ' .
            'The titles must catch the reader\'s attention and will be relevant in ' . $topic .
             'and they must use the right slang. Don\'t be generic in the titles. Write about ' .
            'concrete events, people, key actors, companies or any concrete concepts related to the topic ' .
            'Be diverse and don\'t write about conspiracies. Output in JSON the array. ```json';
        $titles = null;
        while (!$titles) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            // Convert the JSON response into an array of titles
            $titles = json_decode($content, false);
            $titles = self::extractValues($titles);
        }
        $articles = [];
        foreach ($titles as $title) {
            $slug = self::generateSlugFromAnchor($title);
            $articles[] = [
                "slug" => $slug,
                "title" => $title,
            ];
        }
        return $articles;
    }

    public static function setArticleTitleFromSlug(Article $article): Article
    {
        $title = self::getArticleTitleFromSlug($article->getTargetSlug());
        $article->setTitle($title);

        return $article;
    }

    public static function generateArticleContent(Article $article): Article
    {
        $sourceTitle = self::getArticleTitleFromSlug($article->getSourceSlug());

        $prompt = 'Generate a blog article called "' . $article->getTitle() . '" linked ' .
            'from another called "'. $sourceTitle .'" for disambiguation purposes. ' .
            'The article will be interesting, enjoyable and ' .
            'it has to capture the reader\'s attention. Use examples or analogies if a concept ' .
            'is difficult to understand, write one or two quotes if applicable and its authors ' .
            'and eventually write something funny if possible. Include historical events. ' .
            'Write concrete examples, cultural fact, key actors, related products or brands, ' .
            'Don\'t be excessive generic or repetitive. The article should have more than ' .
            '24000 words. Divide the article in a non associative array of paragraphs. Output in JSON ' .
            'a non associative array of strings of the paragraphs. ```json';
        $paragraphs = null;
        while (!$paragraphs) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            // Convert the JSON response into an array of titles
            $paragraphs = json_decode($content);
        }
        $paragraphs = self::extractValues($paragraphs);
        $article->setContentParagraphs($paragraphs);

        $prompt = "Here's the content of an article: ";
        $prompt .= implode('.', $paragraphs);
        $prompt .= 'Generate a good, click bait style title in english for the article. Output in JSON ' .
            'a non associative array of a string with the title. ```json ';
        $content = null;
        while (!$content) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            // Convert the JSON response into an array of titles
            $content = json_decode($content);
        }
        $values = self::extractValues($content);
        if (!empty($values[array_key_first($values)])) {
            $article->setTitle($values[array_key_first($values)]);
        }
        return $article;
    }

    public static function generateGlossaryOfTerms(Article $article): Article
    {
        $articleContent = implode('. ', $article->getContentParagraphs());
        $prompt = 'This is the content of a blog article: ' . $articleContent . '. ' .
            'Create a glossary of 5 terms used in the article with a brief definition. They ' .
            'will be used as anchor text for links, so don\'t be extensive. Output in JSON an ' .
            'associative array of 5 elements, each one with an associative array of two elements: ' .
            '"term" for the term and "definition" for the term definition. ```json';

        $terms = null;
        while (!$terms) {
            $content = self::getOpenAIResponse($prompt);
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            $terms = json_decode($content, true);
        }
        if (is_array($terms)) {
            if (count($terms) === 1 && is_array($terms[array_key_first($terms)])) {
                $article->setGlossaryOfTerms(ucfirst($terms[array_key_first($terms)]));
            }
            else {
                $article->setGlossaryOfTerms($terms);
            }
        }
        else {
            $article->setGlossaryOfTerms(self::extractValues($terms));
        }

        return $article;
    }
    public static function generateInterestingFacts(Article $article): Article
    {
        $articleContent = implode('. ', $article->getContentParagraphs());

        $interestingFactsPrompt = 'This is the content of a blog article: ' . $articleContent . '. ' .
            'Generate a non associative array of 5 elements of less known interesting facts.' .
            'Output in JSON an non associative array of strings with the ' .
            'interesting facts. ```json';


        $facts = null;
        while (!$facts) {
            $content = self::getOpenAIResponse($interestingFactsPrompt);

            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            $facts = json_decode($content);
            $facts = self::extractValues($facts);
        }
        $article->setDidYouKnowFacts($facts);

        return $article;
    }
    public static function generateFurtherReads(Article $article): Article
    {
        $articleContent = implode('. ', $article->getContentParagraphs());

        $furtherReadArticlesPrompt = 'This is the content of a blog article: ' . $articleContent . '. ' .
            'Generate 5 article titles of the same category but not exactly related to the article' .
            ' for a "further read" section. The titles will ' .
            'use a bit of "click bait" but they have to be rigorous, educative and overall ' .
            'interesting and oriented to capture the reader\'s attention. Output them in RFC8259 ' .
            ' compliant JSON. Here\'s an example of the output: ["First article title", "Second ' .
            'article title"] but create instead 5 elements using a non associative array. ' .
            '```json';

        $furtherReadings = null;
        while(!$furtherReadings) {
            $content = self::getOpenAIResponse($furtherReadArticlesPrompt);
            // $content = "[\"The Mind-Blowing Link Between Quantum Physics and Consciousness\",\"Unlocking the Secrets of Lucid Dreaming: A Gateway to Consciousness Exploration\",\"10 Surprising Ways Animals Exhibit Consciousness\",\"The Future of AI: Can Machines Develop True Consciousness?\",\"The Power of Meditation: Transforming Consciousness and Inner Peace\"]";
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            $furtherReadings = json_decode($content);
        }
        $furtherReadings = self::extractValues($furtherReadings);
        $article->setFurtherReadings($furtherReadings);

        return $article;
    }
}

