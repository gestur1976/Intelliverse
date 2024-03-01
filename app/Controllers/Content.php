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
        // We keep only the first part of the title and we remove the taglines
        //$parts = explode(":", $title);
        //$title = $parts[0];
        // We replace all occurrences of non-alphanumeric characters with hyphens
        return preg_replace('/[^a-z^A-Z^0-9\-]/', '-', $title);
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
                "content" => "You are a helpful assistant.",
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
            'temperature' => 0.8,
            'max_tokens' => 4096,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
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



    public static function generateFromTopic(string $slug): ?array
    {
        $topic = html_entity_decode($slug);
        $topic = str_replace('-', ' ', $topic);
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
            'Write concrete examples, cultural fact, key actors, and don\'t be ' .
            'excessive generic. Thr article should have 8000 words if possible. ' .
            'Divide the article in a non associative array of paragraphs. ' .
            'Output in JSON a non associative array of strings of the paragraphs. ```json';
        $paragraphs = null;
        while (!$paragraphs) {
            $content = self::getOpenAIResponse($prompt);
            /*$content = '{
            "title": "Consciousness",
        "content": [
                "Have you ever pondered the enigmatic nature of consciousness? It\'s a topic that has intrigued philosophers, scientists, and curious minds for centuries. From the question of what consciousness actually is to how it arises in the human brain, the exploration of this phenomenon is a journey into the depths of our existence.",

                "To delve into the realm of consciousness, we must first understand the concept itself. Consciousness can be described as the state of being aware of and able to think about one\'s own existence, sensations, thoughts, and surroundings. It\'s what allows us to experience the world around us in a subjective manner, shaping our perceptions and interactions with reality.",

                "Did you know that the study of consciousness has led to various theories and hypotheses, yet it remains one of the most elusive aspects of human experience? Despite advances in neuroscience and psychology, the true nature of consciousness continues to puzzle researchers and thinkers alike.",

                "One intriguing aspect of consciousness is the Anthropic Principle, which suggests that the universe must be compatible with the conscious life that observes it. This principle raises profound questions about the relationship between consciousness and the cosmos, hinting at a deeper connection between the two.",

                "Imagine consciousness as a vast ocean, with each individual mind representing a unique wave in this infinite sea of awareness. Just as each wave is distinct yet interconnected with the whole, our individual consciousnesses are part of a larger, universal consciousness that binds us together in the tapestry of existence.",

                "As the great philosopher Descartes once said, \'Cogito, ergo sum\' - \'I think, therefore I am\'. This famous quote encapsulates the essence of consciousness, highlighting the inseparable link between thought and existence. Descartes believed that the act of thinking itself proves one\'s existence, laying the foundation for modern philosophical inquiries into consciousness.",

                "The exploration of consciousness extends beyond the confines of the human mind, encompassing the realms of artificial intelligence and even the potential for consciousness in non-human entities. Could machines one day possess true consciousness, or is it a uniquely human phenomenon? These questions challenge our understanding of what it means to be conscious.",

                "In the quest to unravel the mysteries of consciousness, scientists and philosophers have proposed various theories, from the integrated information theory to the global workspace model. Each theory offers a different perspective on how consciousness emerges from the complex interactions of the brain, shedding light on the intricate workings of the mind.",

                "Just as a mirror reflects the image before it, consciousness reflects the world within us. It\'s a mirror that not only shows us our external reality but also reveals the depths of our inner thoughts, emotions, and perceptions. Through introspection and self-awareness, we can begin to understand the intricacies of our consciousness.",

                "In the grand tapestry of existence, consciousness is the thread that weaves together the fabric of reality. It\'s the spark of awareness that illuminates our experiences, giving meaning to our lives and shaping our understanding of the world. As we continue to explore the depths of consciousness, we embark on a journey of self-discovery and enlightenment.",

                "So, the next time you ponder the enigma of consciousness, remember that it\'s not just a philosophical concept but a fundamental aspect of our existence. From the depths of our thoughts to the vast expanse of the universe, consciousness is the lens through which we perceive the wonders of reality."
            ]
    }';*/
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
        $sourceTitle = self::getArticleTitleFromSlug($article->getSourceSlug());
        /*
        $previousPrompt = 'Generate a blog article called "' . $article->getTitle() . '" linked ' .
            'from another called "'. $sourceTitle .'" for disambiguation purposes. ' .
            'The article will be interesting, enjoyable and ' .
            'it has to capture the reader\'s attention. Use examples or analogies if a concept ' .
            'is difficult to understand, write one or two quotes if applicable and its authors ' .
            'and eventually write something funny if possible. The content has to be long enough ' .
            'to have a good understanding of the subject. Use between 20 and 50 words per paragraph ' .
            'and more than 10 paragraphs. ' .
            'Output the paragraphs in a non associative array of strings in RFC8259 compliant JSON. ' .
            'For example ["foo","bar"]. The JSON output: ';

        // We encode the $paragraph array into JSON text.
        $previousAnswer = json_encode($article->getContentParagraphs());
        */
        $articleContent = implode('. ', $article->getContentParagraphs());
        $prompt = 'This is the content of a blog article: ' . $articleContent . '. ' .
            'Create a glossary of 8 terms used in the article with a brief definition. They ' .
            'will be used as anchor text for links, so don\'t be extensive. Output in JSON an ' .
            'associative array of 8 elements, each one with an associative array of two elements: ' .
            '"term" for the term and "definition" for the term definition. ```json';

        $terms = null;
        while (!$terms) {
            $content = self::getOpenAIResponse($prompt);
            /*        $content = '[
                {
                    "term": "Consciousness",
                    "definition": "The state of being aware of and able to think about one\'s own existence, sensations, thoughts, and surroundings."
                },
                {
                    "term": "Anthropic Principle",
                    "definition": "The idea that the universe must be compatible with the conscious life that observes it, suggesting a deep connection between consciousness and the cosmos."
                },
                {
                    "term": "Descartes",
                    "definition": "René Descartes, a French philosopher known for his famous statement \'Cogito, ergo sum\' (\'I think, therefore I am\'), which emphasizes the link between thought and existence."
                },
                {
                    "term": "Integrated Information Theory",
                    "definition": "A theory proposing that consciousness arises from the integrated processing of information in the brain, highlighting the interconnected nature of cognitive functions."
                },
                {
                    "term": "Global Workspace Model",
                    "definition": "A model of consciousness suggesting that the brain functions as a global workspace where information is shared and integrated, leading to conscious awareness."
                },
                {
                    "term": "Self-awareness",
                    "definition": "The ability to recognize oneself as an individual separate from others, often linked to introspection and introspective awareness of one\'s thoughts and actions."
                },
                {
                    "term": "Existentialism",
                    "definition": "A philosophical movement emphasizing individual existence, freedom, and choice, often exploring themes related to human consciousness, identity, and the meaning of life."
                },
                {
                    "term": "Neural Correlates of Consciousness",
                    "definition": "The neural processes and brain activities associated with conscious experiences, providing insights into how the brain generates subjective awareness and perception."
                }
            ]';*/
            $content = self::trimJSON($content);
            $content = self::minifyJSON($content);
            $terms = json_decode($content, true);
        }
        if (is_array($terms)) {
            if (count($terms) === 1 && is_array($terms[array_key_first($terms)])) {
                $article->setGlossaryOfTerms($terms[array_key_first($terms)]);
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
            'Generate a non associative array of 8 related interesting facts.' .
            'Output in JSON an non associative array of strings with the ' .
            'interesting facts. ```json';


        $facts = null;
        while (!$facts) {
            $content = self::getOpenAIResponse($interestingFactsPrompt);
            //$content = "[\n\"Despite centuries of study, the true nature of consciousness remains one of the greatest mysteries of the human experience.\",\n\"Consciousness is not limited to humans; many animals exhibit varying degrees of self-awareness and consciousness.\",\n\"The concept of consciousness has inspired diverse fields of study, from philosophy and psychology to neuroscience and artificial intelligence.\",\n\"Research into consciousness has led to the development of intriguing theories and models that attempt to explain how consciousness arises in the brain.\",\n\"Exploring consciousness can lead to profound insights into the nature of reality, the self, and the interconnectedness of all living beings.\"\n]";

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
            'Generate 8 article titles for this blog ' .
            'about closely related topics for a "further read" section. The titles will ' .
            'use a bit of "click bait" but they have to be rigorous, educative and overall ' .
            'interesting and oriented to capture the reader\'s attention. Output them in RFC8259 ' .
            ' compliant JSON. Here\'s an example of the output: ["First article title", "Second ' .
            'article title"] but create instead 8 elements using a non associative array. ' .
            'The JSON output: ';

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

