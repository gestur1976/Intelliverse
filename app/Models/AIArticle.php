<?php

namespace App\Models;

class AIArticle
{
    private array $contentParagraphs;
    private string $topic = '';
    private array $glossaryOfTerms = [];
    private array $didYouKnowFacts = [];
    private array $furtherReadings = [];

    public function __construct(
        private string $targetSlug = '',
        private string $sourceSlug = '',
        private string $title = ''
    ) {
        if (!$title && $targetSlug) {
            // We decode the html entities, remove the hyphens and capitalize the letters
            $title = html_entity_decode($targetSlug);
            $title = ucwords(str_replace('-', ' ', $title));
            $this->title = $title;
        }
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getSourceSlug(): string
    {
        return $this->sourceSlug;
    }

    public function setSourceSlug(string $sourceSlug): void
    {
        $this->sourceSlug = $sourceSlug;
    }

    public function getTargetSlug(): string
    {
        return $this->targetSlug;
    }

    public function setTargetSlug(string $targetSlug): void
    {
        $this->targetSlug = $targetSlug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContentParagraphs(): array
    {
        return $this->contentParagraphs;
    }

    public function setContentParagraphs(array $contentParagraphs): void
    {
        $this->contentParagraphs = $contentParagraphs;
    }

    public function getGlossaryOfTerms(): array
    {
        return $this->glossaryOfTerms;
    }

    public function setGlossaryOfTerms(array $glossaryOfTermsArray): void
    {
        $glossaryOfTerms = [];
        foreach ($glossaryOfTermsArray as $term) {
            if (isset($term['term'], $term['definition'])) {
                $glossaryOfTerms[] = ucfirst($term["term"]) . ': ' . ucfirst($term["definition"]);
            } else {
                if (is_array($term)) {
                    $glossaryOfTerms[] = array_values($term);
                } else {
                    $glossaryOfTerms[] = $term;
                }
            }
        }
        $this->glossaryOfTerms = $glossaryOfTerms;
    }

    public function addTermToGlossary(array $term): void
    {
        $this->glossaryOfTerms[] = $term;
    }

    public function getDidYouKnowFacts(): array
    {
        return $this->didYouKnowFacts;
    }

    public function setDidYouKnowFacts(mixed $facts): void
    {
        $this->didYouKnowFacts = $facts ?: [];
    }

    public function addDidYouKnowFact(string $fact): void
    {
        $this->didYouKnowFacts[] = $fact;
    }

    public function getFurtherReadings(): array
    {
        return $this->furtherReadings;
    }

    public function setFurtherReadings(array $furtherReadings): void
    {
        $this->furtherReadings = $furtherReadings;
    }

    public function addFurtherReading(string $furtherReading): void
    {
        $this->furtherReadings[] = $furtherReading;
    }
    public function generateSlugFromAnchor(string $title): string
    {
        $slug = str_replace(' ', '-', strtolower($title));
        return htmlentities($slug, ENT_QUOTES, 'UTF-8');
    }
}
