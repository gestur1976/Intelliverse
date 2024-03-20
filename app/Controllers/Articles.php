<?php

namespace App\Controllers;

use App\Entities\Article;
use App\Models\ArticleModel;
use App\Models\AIArticle;
use App\Models\TopicModel;

class Articles extends BaseController
{
    /*
     * Home page where you can see a list of autogenerated topics to start browsing.
     */
    private ArticleModel $articleModel;
    private TopicModel $topicModel;
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->topicModel = new TopicModel();
    }
    public function index(): string
    {
        $topicsArray = $this->topicModel->getTopicsArray();
        helper('form');
        $page = view('header', ['title' => 'Select a category as a start point to infinite browsing!']);
        $page .= view('topics', [ 'categories' => $topicsArray]);
        $page .= view('paste_new');
        $page .= view('footer');
        return $page;
    }

    /*public function generateFromURL()
    {
        $article = Content::generateFromURL($this->request->getPost('article-url'));

        Content::generateGlossaryOfTerms($article);
        Content::generateInterestingFacts($article);
        Content::generateFurtherReads($article);
        return view('header', ['article' => $article]) .
            view('article_content') .
            view('article_glossary') .
            view('article_interesting_facts') .
            view('article_further_readings') .
            view('footer');
    }*/

    public function generateFromURL(): string
    {
        $url = $this->request->getPost('article-url');
        $data = [
            'article_source' => 'url',
            'data' => $url,
        ];
        return view('header') .
            view('article_skeleton', $data) .
            view('footer');
    }
    public function generateFromNewsArticle()
    {
        $article = Content::copyWriteArticle($this->request->getPost('article-content'));
        Content::generateGlossaryOfTerms($article);
        Content::generateInterestingFacts($article);
        Content::generateFurtherReads($article);
        return view('header', ['article' => $article]) .
            view('article_content') .
            view('article_glossary') .
            view('article_interesting_facts') .
            view('article_further_readings') .
            view('footer');
    }

    public function fromTopic(string $topic): string
    {
        $articleList = Content::generateFromTopic($topic);

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
        $article = new AIArticle($targetSlug, $sourceSlug);
        Content::generateArticleContent($article);
        Content::generateGlossaryOfTerms($article);
        Content::generateInterestingFacts($article);
        Content::generateFurtherReads($article);
        return view('header', ['article' => $article]) .
            view('article_content') .
            view('article_glossary') .
            view('article_interesting_facts') .
            view('article_further_readings') .
            view('footer');
    }

    /*
     *  This functions returns a view with the article "skeleton" to display
     *  while the content is generated and received via AJAX requests
     */

    public function nextArticleTemplate(string $sourceSlug, string $targetSlug): string
    {
        $sourceSlug = preg_replace('/[^a-z^A-Z^0-9]+/', '-', $sourceSlug);
        $targetSlug = preg_replace('/[^a-z^A-Z^0-9]+/', '-', $targetSlug);
        $data = [
            'article_source' => 'slugs',
            'url' => null,
            'source_slug' => $sourceSlug,
            'target_slug' => $targetSlug,
        ];
        return view('header') .
            view('article_skeleton', $data) .
            view('footer');
    }

    public function newArticleFromURL(): string
    {
        $URL = $this->request->getPost('article-url');
        $data = [
            'article_source' => 'url',
            'url' => $URL,
            'source_slug' => 'news',
            'target_slug' => 'from-url'
        ];
        return view('header') .
            view('article_skeleton', $data) .
            view('footer');
    }


    /*
     * This functions returns a JSON object containing the title and content of the article
     * to be used in an AJAX request.
     */
    public function getTitleAndContentParagraphs(string $sourceSlug, string $targetSlug): string
    {
        $existingArticle = $this->articleModel->where(
            'source_slug', $sourceSlug
        )->where(
            'target_slug', $targetSlug
        )->first();
        if ($existingArticle && $existingArticle->generated) {
            return json_encode([
                'title' => $existingArticle->title,
                'contentParagraphs' => json_decode($existingArticle->content_paragraphs),
                'topic' => $existingArticle->source_slug,
                'source_slug' => $existingArticle->source_slug,
                'target_slug' => $existingArticle->target_slug,
            ]);
        }

        $article = new AIArticle($targetSlug, $sourceSlug);
        Content::generateArticleContent($article);

        if ($existingArticle) {
            $existingArticle->title = $article->getTitle();
            $existingArticle->content_paragraphs = json_encode($article->getContentParagraphs());
            $existingArticle->source_slug = $article->getSourceSlug();
            $existingArticle->target_slug = $article->getTargetSlug();
            $existingArticle->generated = true;
            $this->articleModel->update($existingArticle->id, $existingArticle);
        } else {
            $newArticle = new Article();
            $newArticle->title = $article->getTitle();
            $newArticle->content_paragraphs = json_encode($article->getContentParagraphs());
            $newArticle->source_slug = $article->getSourceSlug();
            $newArticle->target_slug = $article->getTargetSlug();
            $newArticle->generated = true;
            $this->articleModel->insert($newArticle);
        }

        return json_encode([
            'title' => $article->getTitle(),
            'contentParagraphs' => $article->getContentParagraphs(),
            'topic' => $sourceSlug,
            'source_slug' => $article->getSourceSlug(),
            'target_slug' => $article->getTargetSlug(),
        ]);
    }

    public function getTitleAndContentParagraphsFromURL(): string
    {
        $URL = $this->request->getPost('article_url');
        $article = Content::generateFromURL($URL);
        $newArticle = new Article();
        $newArticle->title = $article->getTitle();
        $newArticle->content_paragraphs = json_encode($article->getContentParagraphs());
        $newArticle->source_slug = $article->getSourceSlug();
        $newArticle->target_slug = $article->getTargetSlug();
        $newArticle->generated = true;
        $this->articleModel->insert($newArticle);

        return json_encode([
            'title' => $article->getTitle(),
            'contentParagraphs' => $article->getContentParagraphs(),
            'topic' => $article->getTopic(),
            'source_slug' => $article->getSourceSlug(),
            'target_slug' => $article->getTargetSlug(),
        ]);
    }

    public function getGlossary(): string
    {
        $contentParagraphs = $this->request->getPost('content_paragraphs');
        $title = $this->request->getPost('title');
        $targetSlug = $this->request->getPost('target_slug');
        $sourceSlug = $this->request->getPost('source_slug');

        $article = new AIArticle($targetSlug, $sourceSlug);
        $article->setContentParagraphs($contentParagraphs);
        $article->setTitle($title);
        Content::classifyArticle($article);
        $sourceSlug = $article->getTopic();
        Content::generateGlossaryOfTerms($article);
        return json_encode([
            'title' => $title,
            'topic' => $sourceSlug,
            'content_paragraphs' => $contentParagraphs,
            'source_slug' => $sourceSlug,
            'target_slug' => $targetSlug,
            'glossary' => $article->getGlossaryOfTerms(),
        ]);
    }
    public function getInterestingFacts(): string
    {
        $contentParagraphs = $this->request->getPost('content_paragraphs');
        $title = $this->request->getPost('title');
        $sourceSlug = $this->request->getPost('source_slug');
        $targetSlug = $this->request->getPost('target_slug');
        $topic = $this->request->getPost('topic');
        
        $article = new AIArticle($targetSlug, $sourceSlug);
        $article->setContentParagraphs($contentParagraphs);
        $article->setTitle($title);

        Content::generateInterestingFacts($article);
        return json_encode([
            'title' => $sourceSlug,
            'topic' => $topic,
            'content_paragraphs' => $contentParagraphs,
            'source_slug' => $sourceSlug,
            'target_slug' => $targetSlug,
            'facts' => $article->getDidYouKnowFacts(),
        ]);
    }
    public function getFurtherReads(): string
    {
        $contentParagraphs = $this->request->getPost('content_paragraphs');
        $title = $this->request->getPost('title');
        $sourceSlug = $this->request->getPost('source_slug');
        $targetSlug = $this->request->getPost('target_slug');
        $topic = $this->request->getPost('topic');

        $article = new AIArticle($targetSlug, $sourceSlug);
        $article->setContentParagraphs($contentParagraphs);
        $article->setTitle($title);

        Content::generateFurtherReads($article);
        return json_encode([
            'title' => $title,
            'topic' => $topic,
            'content_paragraphs' => $contentParagraphs,
            'source_slug' => $sourceSlug,
            'target_slug' => $targetSlug,
            'further_readings' => $article->getFurtherReadings(),
        ]);
    }
}