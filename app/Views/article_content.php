<!-- app/Views/article_content.php -->
        <div class="container article">
            <div class="row">
                <div class="col-md-12 title">
                    <h1 class="display-4"><?php echo $article->getTitle() ?></h1>
                </div>
                <div class="col-md-12 content">
                    <?php foreach($article->getContentParagraphs() as $index => $paragraph): ?>
                    <p class="paragraph<?php
                        if ($index === 0) echo ' lead">' . $paragraph;
                        else if ($index % 10 === 1) echo '"><em>' . $paragraph;
                        else if ($index % 10 === 3) echo ' blockquote bg-gray"><strong><em>' . $paragraph;
                        else echo '">' . $paragraph;
                        if ($index % 10 === 1) echo '</em>';
                        if ($index % 10 === 3) echo '</em></strong>' ?>
                    </p>
                    <?php endforeach; ?>
                </div>
