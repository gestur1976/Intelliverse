<!-- app/Views/article_skeleton.php -->
<div class="container article-container d-block">
    <div class="row article mb-2 border">
        <div id="article-title" class="col-md-12 title d-block my-2">
            <h4 class="loading text-center display-6 mt-2">Generating article ... please wait</h4>
            <h1 id="title" class="display-4"></h1>
        </div>
        <div id="article-content" class="col-md-12 content d-block my-2">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row glossary d-none border my-2">
        <div id="glossary-title" class="col-md-12 title d-block d-none">
            <h3 class="display-6 title-section">Glossary of terms:</h3>
        </div>
        <div id="glossary-content" class="col-md-12 content d-block">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row facts d-none my-2 border">
        <div id="interesting-facts-title" class="col-md-12 title d-block d-none">
            <h3 class="display-6 title-section ">Did you know?:</h3>
        </div>
        <div id="interesting-facts-content" class="col-md-12 content d-block">
            <div class="loader loading">
            </div>
        </div>
    </div>
    <div class="row mb-2 further-readings d-none border">
        <div id="further-reading-title" class="col-md-12 title d-block d-none">
            <h3 class="display-6 title-section">Further reading:</h3>
        </div>
        <div id="further-reading-content" class="col-md-12 content d-block">
            <div class="loader loading">
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var sourceSlug = '<?= $slugs["source_slug"] ?>';
    var targetSlug = '<?= $slugs["target_slug"] ?>';
    var articleContentArray = [];
    $.ajax({
        url: '/json/get-title-and-content/' + sourceSlug + '/' + targetSlug,
        type: 'GET',
        dataType: 'json',
        success: function (paragraphs) {
            document.querySelector(".article-container").classList.remove("loading");
            document.querySelector("#title").innerText = paragraphs["title"];
            const articleContent = document.querySelector("#article-content");
            document.querySelector('.article').querySelectorAll('.loading').forEach((element) => {
                element.classList.add('d-none');
            });
            paragraphs["contentParagraphs"].forEach((paragraph) => {
                let line = articleContent.appendChild(document.createElement('p'));
                line.innerText = paragraph;
                line.classList.add('paragraph');
            });
            document.querySelector('.glossary').classList.remove('d-none');
            $.ajax({
                url: '/json/get-glossary',
                type: 'POST',
                dataType: 'json',
                data: {
                    title: paragraphs["title"],
                    content_paragraphs: paragraphs["contentParagraphs"],
                    source_slug: sourceSlug,
                    target_slug: targetSlug
                },
                success: function (termsData) {
                    const glossarySection = document.querySelector('.glossary');
                    glossarySection.querySelector('#glossary-title').classList.remove('d-none');
                    glossarySection.querySelectorAll('.loading').forEach((element) => {
                        element.classList.add('d-none');
                    });
                    const glossaryContent = document.querySelector('#glossary-content');
                    termsData["glossary"].forEach((term) => {
                        const line = glossaryContent.appendChild(document.createElement('p'));
                        line.innerText = term;
                        line.classList.add('paragraph');
                    });
                    document.querySelector('.facts').classList.remove('d-none');
                    $.ajax({
                        url: '/json/get-interesting-facts',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            title: termsData["title"],
                            content_paragraphs: termsData["content_paragraphs"],
                            source_slug: termsData["source_slug"],
                            target_slug: termsData["target_slug"]
                        },
                        success: function (factsData) {
                            let factsSection = document.querySelector('.facts');
                            factsSection.querySelector('#interesting-facts-title').classList.remove('d-none');
                            factsSection.querySelectorAll('.loading').forEach((element) => {
                                element.classList.add('d-none');
                            });
                            const factsContent = document.querySelector('#interesting-facts-content');
                            factsData["facts"].forEach((fact) => {
                                const line = factsContent.appendChild(document.createElement('p'));
                                line.innerText = fact;
                                line.classList.add('paragraph');
                            });
                            document.querySelector('.further-readings').classList.remove('d-none');
                            $.ajax({
                                url: '/json/get-further-readings',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    title: factsData["title"],
                                    content_paragraphs: factsData["content_paragraphs"],
                                    source_slug: factsData["source_slug"],
                                    target_slug: factsData["target_slug"]
                                },
                                success: function (furtherReadingData) {
                                    let furtherReadingSection = document.querySelector('.further-readings');
                                    furtherReadingSection.querySelector('#further-reading-title').classList.remove('d-none');
                                    furtherReadingSection.querySelectorAll('.loading').forEach((element) => {
                                        element.classList.add('d-none');
                                    });
                                    const furtherReadingContent = document.querySelector('#further-reading-content');
                                    furtherReadingData["further_readings"].forEach((furtherReading) => {
                                        const line = furtherReadingContent.appendChild(document.createElement('p'));
                                        line.innerText = furtherReading;
                                        line.classList.add('paragraph');
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }
    });
</script>

