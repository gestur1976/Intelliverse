<!-- app/Views/article_interesting_facts.php -->
                <div class="col-md-12 facts">
                    <h3>Did you know?:</h3>
                    <ul>
                        <?php foreach($article->getDidYouKnowFacts() as $fact): ?>
                        <li>
                            <a href="<?php echo "/" . $article->getTargetSlug() . "/" .
                                $article->generateSlugFromAnchor($fact) .
                                '">' . ucfirst($fact) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
