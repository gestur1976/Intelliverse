function ajaxLoadArticle(URL, sourceSlug, targetSlug) {
    $.ajax({
        url: URL + '/' + sourceSlug + '/' + targetSlug,
        type: 'GET',
        dataType: 'json',
        success: function (articleData) {
            document.querySelector(".article-container").classList.remove("loading");
            document.querySelector("#title").innerText = articleData["title"];
            const articleContentSection = document.querySelector("#article-content");
            document.querySelector('.article')
                .querySelectorAll('.loading')
                .forEach((element) => {
                    element.classList.add('d-none');
                });
            articleData["contentParagraphs"].forEach((paragraph) => {
                const paragraphClasses = getParagraphClasses(paragraph);

                let line = articleContentSection.appendChild(document.createElement('p'));
                line.innerText = paragraph;
                line.classList.add('paragraph');
                paragraphClasses.forEach((paragraphClass) => {
                    line.classList.add(paragraphClass);
                });
            });
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
            target_slug: targetSlug
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
            target_slug: targetSlug
        },
        success: function (furtherReadingData) {
            let furtherReadingSection = document.querySelector('.further-readings');
            furtherReadingSection.querySelector('#further-reading-title').classList.remove('d-none');
            furtherReadingSection.querySelectorAll('.loading').forEach((element) => {
                element.classList.add('d-none');
            });
            const furtherReadingContent = document.querySelector('#further-reading-content');
            htmlOutput = '';
            furtherReadingData["further_readings"].forEach((furtherReading) => {
                htmlOutput += paragraphToHTML(createLinkFromAnchor(furtherReading, sourceSlug), 'further-reading');
            });
            furtherReadingContent.innerHTML = htmlOutput;
        }
    });
}