<!-- app/Views/article_terms.php -->
                <div class="col-md-12 terms">
                    <h3>Glossary of terms:</h3>
                    <ul>
                        <?php foreach($article->getGlossaryOfTerms() as $term): ?>
                        <li>
                            <a href="<?php echo "/" . $article->getTargetSlug() . "/" .
                                $article->generateSlugFromAnchor($term) .
                                '"><strong>' . ucfirst($term) . "</strong>"?>
                            </a>
                         </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
