<!-- app/Views/topic_articles.php -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>A List of articles about <?= esc($topic) ?>.</h1>
        </div>
        <div class="col-md-12">
            <ul>
            <?php foreach ($articlelist as $article) : ?>
                <li><?= esc($article) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
