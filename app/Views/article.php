<!-- app/Views/articles.php -->
<div class="container article">
    <div class="row">
        <div class="col-md-12 title">
            <h1><?php echo $article->getTitle() ?></h1>
        </div>
        <div class="col-md-12 content">
            <?php foreach($article->getContentParagraphs() as $paragraph): ?>
            <p class="paragraph">
                <?php echo $paragraph ?>
            </p>
            <?php endforeach; ?>
            <hr>
            <h3>Glossary of terms:</h3>
            <ul>
                <?php foreach($article->getGlossaryOfTerms() as $term): ?>
                <li><?php echo $term->term . ': ' . $term->definition ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
