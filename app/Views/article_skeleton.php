<!-- app/Views/article_skeleton.php -->
<div class="container article-container d-block mb-4">
    <div class="row article mb-2 border">
        <div id="article-title" class="col-md-12 title d-block my-2">
            <h4 class="loading text-center display-6 mt-2">Generating article ... please wait</h4>
            <h1 id="title" class="display-4"></h1>
        </div>
        <div id="article-content" class="col-md-12 content d-block my-4">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row glossary d-none border my-2">
        <div id="glossary-title" class="col-md-12 title d-block d-none my-2">
            <h3 class="display-6 title-section">Glossary of terms:</h3>
        </div>
        <div id="glossary-content" class="col-md-12 content d-block my-4">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row facts d-none my-2 border">
        <div id="interesting-facts-title" class="col-md-12 title d-block d-none my-2">
            <h3 class="display-6 title-section ">Did you know?:</h3>
        </div>
        <div id="interesting-facts-content" class="col-md-12 content d-block my-4">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row mb-2 further-readings d-none border">
        <div id="further-reading-title" class="col-md-12 title d-block d-none my-2">
            <h3 class="display-6 title-section">Further reading:</h3>
        </div>
        <div id="further-reading-content" class="col-md-12 content d-block my-4">
            <div class="loader loading">
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="/assets/js/content-helper.js"></script>
<script type="application/javascript" src="/assets/js/article-ajax-load.js"></script>
<script type="application/javascript">
    var sourceSlug = '<?= $slugs["source_slug"] ?>';
    var targetSlug = '<?= $slugs["target_slug"] ?>';
    var articleContentArray = [];

    // We wait for the document to be ready
    $(document).ready(function () {
        ajaxLoadArticle('/json/get-title-and-content/', sourceSlug, targetSlug);
    });
</script>

