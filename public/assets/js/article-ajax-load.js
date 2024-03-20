function ajaxGenerateArticleFromSlugs(sourceSlug, targetSlug) {
    $.ajax({
        url: '/json/get-title-and-content/' + '/' + sourceSlug + '/' + targetSlug,
        type: 'GET',
        dataType: 'json',
        success: function (articleData) {
            const articleSection = document.querySelector('.article');
            articleSection.querySelectorAll('.loading')
                .forEach((element) => {
                    element.classList.add('d-none');
                });
            document.querySelector("#title").innerText = articleData["title"];
            const articleContentSection = document.querySelector("#article-content");
            articleContentSection.innerHTML = formatParagraphs(articleData["contentParagraphs"]);
            ajaxLoadGlossaryOfTerms(sourceSlug, targetSlug, articleData["title"], articleData["contentParagraphs"]);
        }
    });
}

function ajaxGenerateArticleFromURL(articleURL) {
    $.ajax({
        url: '/json/generate-from-url',
        type: 'POST',
        dataType: 'json',
        data: {
            article_url: articleURL
        },
        success: function (articleData) {
            const articleSection = document.querySelector('.article');
            const sourceSlug = articleData["source_slug"];
            const targetSlug = articleData["target_slug"];
            articleSection.querySelectorAll('.loading')
                .forEach((element) => {
                    element.classList.add('d-none');
                });
            document.querySelector("#title").innerText = articleData["title"];
            const articleContentSection = document.querySelector("#article-content");
            articleContentSection.innerHTML = formatParagraphs(articleData["contentParagraphs"]);
            ajaxLoadGlossaryOfTerms(sourceSlug, targetSlug, articleData["title"], articleData["contentParagraphs"]);
        }
    });
}

// We load using AJAX the Glossary section
function ajaxLoadGlossaryOfTerms(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
    document.querySelector('.glossary').classList.remove('d-none');
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
            const glossarySection = document.querySelector('.glossary');
            glossarySection.querySelector('#glossary-title').classList.remove('d-none');
            glossarySection.querySelectorAll('.loading').forEach((element) => {
                element.classList.add('d-none');
            });
            const glossaryContent = document.querySelector('#glossary-content');
            let htmlOutput = '<ul class="list-unstyled">';
            termsData["glossary"].forEach((term) => {
                htmlOutput += paragraphToHTML(createLinkFromAnchor(term, sourceSlug));
                //htmlOutput += '<li class="term">' + term + '</li>';
            });
            htmlOutput += '</ul>';
            glossaryContent.innerHTML = htmlOutput
            ajaxLoadInterestingFacts(sourceSlug, targetSlug, articleTitle, contentParagraphs);
        }
    });
}

// We load using AJAX the Interesting Facts section
function ajaxLoadInterestingFacts(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
    document.querySelector('.facts').classList.remove('d-none');
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
            let factsSection = document.querySelector('.facts');
            factsSection.querySelector('#interesting-facts-title').classList.remove('d-none');
            factsSection.querySelectorAll('.loading').forEach((element) => {
                element.classList.add('d-none');
            });
            const factsContent = document.querySelector('#interesting-facts-content');
            let htmlOutput = '<ul class="list-unstyled">';
            factsData["facts"].forEach((fact) => {
                htmlOutput += '<li>' + paragraphToHTML(createLinkFromAnchor(fact, sourceSlug), 'interesting-fact') + '</li>';
                //htmlOutput += '<li class="interesting-fact">' + fact + '</li>';
            });
            htmlOutput += '</ul>';
            factsContent.innerHTML = htmlOutput;

            ajaxLoadFurtherReading(sourceSlug, targetSlug, articleTitle, contentParagraphs);
        }
    });
}

function ajaxLoadFurtherReading(sourceSlug, targetSlug, articleTitle, contentParagraphs) {
    document.querySelector('.further-readings').classList.remove('d-none');
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
            let furtherReadingSection = document.querySelector('.further-readings');
            furtherReadingSection.querySelector('#further-reading-title').classList.remove('d-none');
            furtherReadingSection.querySelectorAll('.loading').forEach((element) => {
                element.classList.add('d-none');
            });
            const furtherReadingContent = document.querySelector('#further-reading-content');
            let htmlOutput = '';
            furtherReadingData["further_readings"].forEach((furtherReading) => {
                htmlOutput += paragraphToHTML(createLinkFromAnchor(furtherReading, sourceSlug), 'further-reading');
            });
            furtherReadingContent.innerHTML = htmlOutput;
        }
    });
}