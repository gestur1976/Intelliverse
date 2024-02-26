<?php

namespace App\Models;

class Article
{

    private array $contentParagraphs;
    private array $glossaryOfTerms = [];
    private array $didYouKnowFacts = [];
    private array $furtherReadings = [];

    public function __construct(
        private string $targetSlug,
        private string $sourceSlug = '',
        private string $title = ''
    ) {
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

    public function setGlossaryOfTerms(array $glossaryOfTerms): void
    {
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

    public function setDidYouKnowFacts(array $facts): void
    {
        $this->didYouKnowFacts = $facts;
    }

    public function addDidYouKnowFact(string $fact): void
    {
        $this->didYouKnowFacts[] = $fact;
    }

    public function getFurtherReadings(): array
    {
        return $this->furtherReadings;
    }

    public function setFurtherReading(array $furtherReadings): void
    {
        $this->furtherReadings = $furtherReadings;
    }

    public function addFurtherReading(string $furtherReading): void
    {
        $this->furtherReadings[] = $furtherReading;
    }
}
