<!-- app/Views/article_skeleton.php -->
<div class="container article-container d-block mb-4">
    <div class="row article mb-2 border">
        <div id="article-title" class="col-md-12 title d-block my-2">
            <h1 id="title" class="display-4">Pending articles generation page</h1>
        </div>
        <?php foreach ($articles as $article) : ?>
        <div class="pending-article"
             data-source-slug="<?= $article['source_slug'] ?>"
             data-target-slug="<?= $article['target_slug'] ?>">
            <div class="col-md-12 content d-block my-4 title">
                <p><?= $article['title'] ?></p>
            </div>
            <div class="col-md-12 progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script type="application/javascript" src="/assets/js/content-helper.js"></script>
<script type="application/javascript" src="/assets/js/article-ajax-load.js"></script>
<script type="application/javascript">

    var progressBar;
    var currentArticle;
    // We wait for the document to be ready
    $(document).ready(function () {
        currentArticle = document.querySelector('.pending-article');
        if (currentArticle) {
            progressBar = currentArticle.querySelector('.progress-bar');
            // We load the first article
            loadArticle();
        }
    });

    function loadArticle() {
        const sourceSlug = currentArticle.dataset.sourceSlug;
        const targetSlug = currentArticle.dataset.targetSlug;
        progressBar.innerText = 'Loading...';
        loadArticleContent(sourceSlug, targetSlug);
    }

    function loadArticleContent(sourceSlug, targetSlug) {
        $.ajax({
            url: '/json/get-title-and-content/' + '/' + sourceSlug + '/' + targetSlug,
            type: 'GET',
            dataType: 'json',
            success: function (articleData) {
                // We set the progressbar to 25%
                progressBar.setAttribute('aria-valuenow', 25);
                progressBar.style.width = 25;
                progressBar.innerText = '25%';
                loadArticleGlossary(articleData['source_slug'], articleData['target_slug'], articleData["title"], articleData["content_paragraphs"]);
            }
        });
    }

    function loadArticleGlossary(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
        $.ajax({
            url: '/json/get-glossary',
            type: 'POST',
            dataType: 'json',
            data: {
                title: articleTitle,
                content_paragraphs: contentParagraphs,
                source_slug: sourceSlug,
                target_slug: targetSlug,
            },
            success: function (termsData) {
                // We set the progressbar to 50%
                progressBar.setAttribute('aria-valuenow', 50);
                progressBar.style.width = 50;
                progressBar.innerText = '50%';
                loadArticleInterestingFacts(termsData['source_slug'], termsData['target_slug'], termsData["title"], termsData["content_paragraphs"]);
            }
        });
    }
    function loadArticleInterestingFacts(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
        $.ajax({
            url: '/json/get-interesting-facts',
            type: 'POST',
            dataType: 'json',
            data: {
                title: articleTitle,
                content_paragraphs: contentParagraphs,
                source_slug: sourceSlug,
                target_slug: targetSlug,
            },
            success: function (factsData) {
                // We set the progressbar to 75%
                progressBar.setAttribute('aria-valuenow', 75);
                progressBar.style.width = 75;
                progressBar.innerText = '75%';
                loadArticleFurtherReading(factsData['source_slug'], factsData['target_slug'], factsData["title"], factsData["content_paragraphs"]);
            }
        });
    }
    function loadArticleFurtherReading(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
        $.ajax({
            url: '/json/get-further-readings',
            type: 'POST',
            dataType: 'json',
            data: {
                title: articleTitle,
                content_paragraphs: contentParagraphs,
                source_slug: sourceSlug,
                target_slug: targetSlug,
            },
            success: function (furtherReadingData) {
                // We set the progressbar to 100%
                progressBar.setAttribute('aria-valuenow', 100);
                progressBar.style.width = 100;
                progressBar.innerText = 'Done.';
                processNextArticle();
            }
        });
    }

    function processNextArticle() {
        currentArticle.classList.remove('pending-article');
        currentArticle.classList.add('d-none');
        currentArticle = document.querySelector('.pending-article');
        if (currentArticle) {
            progressBar = currentArticle.querySelector('.progress-bar');
            // We load the first article
            loadArticle();
        }
    }
</script>

