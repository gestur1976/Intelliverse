<!-- app/Views/topic_articles.php -->
<div class="container">
    <div class="row my-4">
        <div class="col-md-12">
            <h1>A List of articles about <?= esc($topic) ?>.</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul>
            <?php foreach ($articles as $slug => $title) : ?>
                <li class="my-2">
                    <a href="<?php echo $topic . '/' . $slug ?>">
                        <?= esc($title) ?>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
