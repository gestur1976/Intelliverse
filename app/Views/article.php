<!-- app/Views/articles.php -->
<div class="container article">
    <div class="row">
        <div class="col-md-12 title">
            <h1 class="display-4"><?php echo $article->getTitle() ?></h1>
        </div>
        <div class="col-md-12 content">
            <?php foreach($article->getContentParagraphs() as $index => $paragraph): ?>
            <p class="paragraph<?php
                if ($index === 0) echo ' lead">' . $paragraph;
                else if ($index === 1) echo '"><em>' . $paragraph;
                else if ($index === 4) echo ' blockquote bg-gray"><strong><em>' . $paragraph;
                else echo '">' . $paragraph;
                if ($index === 1) echo '</em>';
                if ($index === 4) echo '</em></strong>' ?>
            </p>
            <?php endforeach; ?>
            <hr>
            <h3>Glossary of terms:</h3>
            <ul>
                <?php foreach($article->getGlossaryOfTerms() as $term): ?>
                <li>
                    <a href="<?php echo "/" . $article->getTargetSlug() . "/" .
                        $article->generateSlugFromAnchor($term) .
                        '"><strong>' . $term . "</strong>"?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <hr>
            <h3>Did you know?:</h3>
            <ul>
                <?php foreach($article->getDidYouKnowFacts() as $fact): ?>
                    <li>
                        <a href="<?php echo "/" . $article->getTargetSlug() . "/" .
                        $article->generateSlugFromAnchor($fact) .
                        '">' . $fact ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr>
            <h3>Further reading:</h3>
            <ul>
                <?php foreach($article->getFurtherReadings() as $nextArticle): ?>
                    <li>
                        <a href="<?php echo "/" . $article->getTargetSlug() . "/" .
                            $article->generateSlugFromAnchor($nextArticle)?>">
                            <?php echo ($nextArticle) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
