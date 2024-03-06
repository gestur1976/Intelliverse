function getParagraphClasses( paragraph ) {
    let paragraphClasses = ["paragraph"];

    // Check if the paragraph contains a year in the format 'YYYY' or the word 'century'
    if (paragraph.match(/[0-9]{4}/) !== null || paragraph.includes("century")) {
        paragraphClasses.push("historical-fact");
    } else {
        // Check if the paragraph contains the word 'said' or quotes to determine if it is a quote
        if (paragraph.includes("said") || paragraph.includes(' "') || paragraph.includes('". ') || paragraph.includes(" '") || paragraph.includes("'. ")  || paragraph.includes('", ')) {
            paragraphClasses.push("quote");
        }
    }
    // Check for analogies in the paragraph
    if (paragraph.includes("magine") || paragraph.includes("onsider") || paragraph.includes('yourself')) {
        paragraphClasses.push("analogy");
    }
    // Check if at the end of the paragraph there is a ':'
    if (paragraph.endsWith(":")) {
        paragraphClasses.push("explanation");
    } else {
        // Check if the paragraph is the conclussion
        if (paragraph.includes("onclusion")) {
            paragraphClasses.push("conclusion");
        }
    }

    return paragraphClasses;
}

function createSlugFromText(text) {
    return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
}

// We create the article content HTML paragraphs
function paragraphToHTML( paragraph, classes ) {
    const classesString = classes ? ' class="' + classes + '"' : '';
    return '<p' + classesString +'>' + paragraph + '</p>';
}

function createLinkFromAnchor(anchor, sourceSlug) {
    return '<a href="/' + sourceSlug + '/' + createSlugFromText(anchor) +'">' + anchor + '</a>';
}

