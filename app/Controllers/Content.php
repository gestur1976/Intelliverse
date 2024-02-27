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
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant.",
                ],
                [
                    "role" => "user",
                    "content" => $prompt,
                ],
            ];
        }
        $complete = $openAI->chat([
            'model' => env('OPENAI_MODEL'),
            'messages' => $messages,
            'temperature' => 0.2,
            'max_tokens' => 8192,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $jsonComplete = json_decode($complete);
        return $jsonComplete->choices[0]->message->content;
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
        if ($squareBracketStartPos === false && $curlyBracketStartPos === false) {
            return false;
        }
        $startPos = ((int)$squareBracketStartPos < (int)$curlyBracketStartPos) ? (int)$squareBracketStartPos : (int)$curlyBracketStartPos;
        $tmpJSON = substr($tmpJSON, $startPos);

        // Now backwards

        $tmpJSON = strrev($tmpJSON);

        $squareBracketStartPos = strpos($tmpJSON, ']');
        $curlyBracketStartPos = strpos($tmpJSON, '}');
        if ($squareBracketStartPos === false && $curlyBracketStartPos === false) {
            return false;
        }
        $startPos = ((int)$squareBracketStartPos < (int)$curlyBracketStartPos) ? (int)$squareBracketStartPos : (int)$curlyBracketStartPos;
        $tmpJSON = substr($tmpJSON, $startPos);

        // We reverse it again to get the original JSON text.

        $tmpJSON = strrev($tmpJSON);

        return $tmpJSON ?: $textContainingJSON;
    }
    public static function generateFromTopic(string $slug): ?array
    {
        $topic = html_entity_decode($slug);
        $topic = str_replace('-', ' ', $topic);
        $prompt = "Output in JSON a non associative array of 10 titles of $topic blog articles. " .
            "The articles must be interesting, entertaining and formal at the same time. The titles " .
            "will use a bit of \"click bait\" but they must be correct. Don't be repetitive. " .
            "Just output raw JSON in plain text and nothing else, don't use markup, no boxes, " .
            "no triple quotes. The first character of the answer must be the opening bracket " .
            "and the final character must be the closing bracket, THIS IS A NON ASSOCIATIVE ARRAY," .
            "WRITE ONLY THE VALUES, NOT KEYS";

        $content = self::getOpenAIResponse($prompt);
        $content = self::trimJSON($content);
        // Convert the JSON response into an array of titles
        $titles = json_decode($content, true);

        // Create an associative array of slugs and titles
        return $titles ?? self::generateSlugsFromAnchors($titles);
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
        $prompt = 'Generate a blog article. The title is "' . $article->getTitle() . '". The ' .
            'referral page title is "' . $sourceTitle .'" and it has a link to this article ' .
            'with "'. $sourceTitle .'" as anchor text. Use the previous page as context ' .
            'to write about the right subject because a simple title could apply to many ' .
            'contexts. The article has to be interesting, easy to read, entertaining and ' .
            'has to capture the reader\'s attention, Write it in an easy to understand ' .
            'language, use examples or analogies if a concept is difficult to understand ' .
            'and eventually write something funny if possible. Write more than 10 paragraphs ' .
            'and don\'t be repetitve. The third paragraph will be an interesting fact starting with "Did you know", and the seventh paragraph will be a famous quote, its context and why its author said it if applicable. Write the title and the content in a JSON associative ' .
            'array with "title" and "content" as keys. "content" will contain another array ' .
            'with each paragraph written. Just output raw JSON and nothing else, including ' .
            'any markup or boxes. Just start with the opening bracket.';

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
        // Convert the JSON response into an array of titles
        $jsonObject = json_decode($content);

        $article->setTitle($jsonObject->title);
        $article->setContentParagraphs($jsonObject->content);

        return $article;
    }

    public static function generateGlossaryOfTerms(Article $article): Article
    {
        $sourceTitle = self::getArticleTitleFromSlug($article->getSourceSlug());
        $previousPrompt = 'Generate a blog article. The title is "' .
            $article->getTitle() . 'The referral page title is "' . $sourceTitle .'" and it ' .
            'has a link to this article with "'. $sourceTitle .'" as anchor text. Use the ' .
            'previous page as context to write about the right subject because a simple ' .
            'title could apply to many contexts. The article has to be written in an , ' .
            'interesting but formal way at the same time, it has to be correct and and also a bit speculative about each concept implications, it has to capture the reader\'s attention, ' .
            ' Write it in an easy to understand language, use examples or analogies if ' .
            'a concept is difficult to understand and eventually write something funny ' .
            'if possible. Write more than 10 paragraphs and don\'t be repetitive. Write ' .
            'the title and the content in a JSON associative array with "title" and ' .
            '"content" as keys. "content" will contain another array with each paragraph ' .
            'written. Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        // We encode the $paragraph array into JSON text.
        $previousAnswer = json_encode($article->getContentParagraphs());

        $prompt = 'Write an array with 5 terms for a glossary with article related terms. ' .
            'Create an associative array called terms in JSON with the elements inside using the keys "term" for the term and ' .
            '"definition" for term definition. Just output raw JSON.';

        $messages = [
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
        ];

        $content = self::getOpenAIResponse($prompt, $messages);
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
        $terms = json_decode($content, true);
        $article->setGlossaryOfTerms($terms);

        return $article;
    }
    public static function generateInterestingFacts(Article $article): Article
    {
        $sourceTitle = self::getArticleTitleFromSlug($article->getSourceSlug());
        $previousParagraphsPrompt = 'Generate a blog article. The title is "' .
            $article->getTitle() . 'The referral page title is "' . $sourceTitle .'" and it ' .
            'has a link to this article with "'. $sourceTitle .'" as anchor text. Use the ' .
            'previous page as context to write about the right subject because a simple ' .
            'title could apply to many contexts. The article has to be written in an , ' .
            'interesting but formal way at the same time, it has to be correct and and also a bit speculative about each concept implications, it has to capture the reader\'s attention, ' .
            ' Write it in an easy to understand language, use examples or analogies if ' .
            'a concept is difficult to understand and eventually write something funny ' .
            'if possible. Write more than 10 paragraphs and don\'t be repetitive. Write ' .
            'the title and the content in a JSON associative array with "title" and ' .
            '"content" as keys. "content" will contain another array with each paragraph ' .
            'written. Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        // We encode the $paragraph array into JSON text.
        $previousParagraphsAnswer = json_encode($article->getContentParagraphs());

        $prompt = 'Write an array with 5 terms for a glossary with article related terms. ' .
            'Create an associative array called terms in JSON with the elements inside using the keys "term" for the term and ' .
            '"definition" for term definition. Just output raw JSON.';

        $previousGlossaryAnswer = json_encode($article->getGlossaryOfTerms());

        $interestingFactsPrompt = 'Write a non associative array of 5 interesting facts in JSON.' .
            'Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        $messages = [
            [
                "role" => "system",
                "content" => "You are a helpful assistant.",
            ],
            [
                "role" => "user",
                "content" => $previousParagraphsPrompt,
            ],
            [
                "role" => "assistant",
                "content" => $previousParagraphsAnswer,
            ],
            [
                "role" => "user",
                "content" => $previousGlossaryPrompt
            ],
            [
                "role" => "assistant",
                "content" => $previousGlossaryAnswer,
            ],
            [
                "role" => "user",
                "content" => $interestingFactsPrompt,
            ],
        ];

        $content = self::getOpenAIResponse($interestingFactsPrompt, $messages);
        //$content = "[\n\"Despite centuries of study, the true nature of consciousness remains one of the greatest mysteries of the human experience.\",\n\"Consciousness is not limited to humans; many animals exhibit varying degrees of self-awareness and consciousness.\",\n\"The concept of consciousness has inspired diverse fields of study, from philosophy and psychology to neuroscience and artificial intelligence.\",\n\"Research into consciousness has led to the development of intriguing theories and models that attempt to explain how consciousness arises in the brain.\",\n\"Exploring consciousness can lead to profound insights into the nature of reality, the self, and the interconnectedness of all living beings.\"\n]";

        $content = self::trimJSON($content);
        $facts = json_decode($content, true);
        $article->setDidYouKnowFacts($facts);

        return $article;
    }
    public static function generateFurtherReads(Article $article): Article
    {
        $sourceTitle = self::getArticleTitleFromSlug($article->getSourceSlug());
        $previousParagraphsPrompt = 'Generate a blog article. The title is "' .
            $article->getTitle() . 'The referral page title is "' . $sourceTitle .'" and it ' .
            'has a link to this article with "'. $sourceTitle .'" as anchor text. Use the ' .
            'previous page as context to write about the right subject because a simple ' .
            'title could apply to many contexts. The article has to be written in an , ' .
            'interesting but formal way at the same time, it has to be correct and and also a bit speculative about each concept implications, it has to capture the reader\'s attention, ' .
            ' Write it in an easy to understand language, use examples or analogies if ' .
            'a concept is difficult to understand and eventually write something funny ' .
            'if possible. Write more than 10 paragraphs and don\'t be repetitive. Write ' .
            'the title and the content in a JSON associative array with "title" and ' .
            '"content" as keys. "content" will contain another array with each paragraph ' .
            'written. Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        // We encode the $paragraph array into JSON text.
        $previousParagraphsAnswer = json_encode($article->getContentParagraphs());

        $previousGlossaryPrompt = 'Write an array with 5 terms for a glossary with article related terms. ' .
            'Create an associative array in JSON with the keys "term" for the term and ' .
            '"definition" for term definition. Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        $previousGlossaryAnswer = json_encode($article->getGlossaryOfTerms());

        $previousInterestingFactsPrompt = 'Write a non associative array of 5 interesting facts in JSON.' .
            'Just output raw JSON in plain text and nothing else, including any ' .
            'markup or boxes. Just start with the opening bracket.';

        $previousInterestingFactsAnswer = json_encode($article->getDidYouKnowFacts());

        $furtherReadArticlesPrompt = 'Write in a non associative array of the titles of ' .
            '5 blog articles in the same context but not very related for a further read section. The titles will use a bit of "click bait" ' .
            'but they have to be real and overall interesting and oriented to capture the ' .
            'reader\'s attention. Just output raw JSON array of titles in plain text and ' .
            'nothing else, including any markup or boxes. Just start with the opening bracket.';

        $messages = [
            [
                "role" => "system",
                "content" => "You are a helpful assistant.",
            ],
            [
                "role" => "user",
                "content" => $previousParagraphsPrompt,
            ],
            [
                "role" => "assistant",
                "content" => $previousParagraphsAnswer,
            ],
            [
                "role" => "user",
                "content" => $previousGlossaryPrompt
            ],
            [
                "role" => "assistant",
                "content" => $previousGlossaryAnswer,
            ],
            [
                "role" => "user",
                "content" => $previousInterestingFactsPrompt,
            ],
            [
                "role" => "assistant",
                "content" => $previousInterestingFactsAnswer,
            ],
            [
                "role" => "user",
                "content" => $furtherReadArticlesPrompt,
            ],
        ];

        $content = self::getOpenAIResponse($furtherReadArticlesPrompt, $messages);
        // $content = "[\"The Mind-Blowing Link Between Quantum Physics and Consciousness\",\"Unlocking the Secrets of Lucid Dreaming: A Gateway to Consciousness Exploration\",\"10 Surprising Ways Animals Exhibit Consciousness\",\"The Future of AI: Can Machines Develop True Consciousness?\",\"The Power of Meditation: Transforming Consciousness and Inner Peace\"]";
        $content = self::trimJSON($content);
        $furtherReadings = json_decode($content, true);
        $article->setFurtherReadings($furtherReadings);

        return $article;
    }
}

