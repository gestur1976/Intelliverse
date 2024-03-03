<!-- app/Views/article_further_readings.php -->
                <div class="col-md-12 further-readings">
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
