<?php

namespace App\Controllers;

use App\Models\Article;
class Articles extends BaseController
{
    /*
     * Home page where you can see a list of autogenerated topics to start browsing.
     */
    public function index(): string
    {
        $topicList = Content::getTopicsArray();
        $page = view('header', ['title' => 'Select a topic as a start point!']);
        $page .= view('topics');
        $page .= view('footer');
        return $page;
    }

    public function fromTopic(string $topic): string
    {
        $articleList = Content::generateFromTopic($topic);

        /*$articleList = [
             "Unraveling the Mysteries of Fractal Geometry",
             "The Beauty of Prime Numbers: A Visual Exploration",
             "From Zero to Infinity: A Journey Through Number Theory",
             "The Power of Mathematical Induction: Unlocking Complex Proofs",
             "Exploring the Golden Ratio in Art and Nature",
             "Chaos Theory: Finding Order in Disorder",
             "The Enigma of Riemann Hypothesis: A Deep Dive",
             "Game Theory: Strategies for Success in Life and Business",
             "The Fascinating World of Cryptography: Securing Data with Math",
             "Solving P vs NP: The Greatest Unsolved Problem in Computer Science",
             "The Magic of Symmetry: Patterns in Mathematics and Nature",
             "Diving into Differential Equations: Applications in Science and Engineering",
             "The Infinite Possibilities of Calculus: Beyond Limits and Derivatives",
             "Topology Unraveled: Understanding Shapes and Spaces",
             "The Art of Problem Solving: Techniques for Mathematical Olympiads",
             "Quantum Computing: Bridging Math and Physics for the Future",
             "The Elegance of Group Theory: Transforming Algebraic Structures",
             "Data Science Essentials: Statistical Analysis and Machine Learning",
             "Mathematics of Music: Harmonies and Frequencies",
             "The Curious Case of Collatz Conjecture: A Number Theory Puzzle",
        ];
        */
        $page = view('header', [ 'topic' => $topic]);
        $page .= view('topic_articles', [
            'topic' => $topic,
            'articles' => $articleList,
        ]);
        $page .= view('footer');
        return $page;
    }

    public function nextArticle(string $sourceSlug, string $targetSlug): string
    {
        /*
         * TODO: Using the slug passed through get method to get the article title.
         *       Planning in the future to use POST method to get directly the title
         *        
         */
        $article = new Article($targetSlug, $sourceSlug);
        Content::generateArticleContent($article);
        if ($article->getTitle() === "Error:") {
            $page = view('header', ['article' => $article]);
            $page .= view('article');
            $page .= view('footer');
            return $page;
        }
        Content::generateGlossaryOfTerms($article);
        Content::generateInterestingFacts($article);
        Content::generateFurtherReads($article);

        $page = view('header', ['article' => $article]);
        $page .= view('article');
        $page .= view('footer');
        return $page;
    }
}