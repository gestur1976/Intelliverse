function formatParagraphs( contentParagraphs ) {
    let htmlOutput='';
    lastParagraph = contentParagraphs.length - 1;
    contentParagraphs.forEach((paragraph, index) => {
        if (index === 0) {
            htmlOutput += paragraphToHTML(paragraph, "paragraph lead");
        } else {
            if (index === lastParagraph) {
                htmlOutput += paragraphToHTML('<em><strong>' + paragraph + '</strong></em>', "paragraph lead");
            } else {
                // Check if the paragraph is the conclussion
                if (paragraph.includes("onclusion")) {
                    htmlOutput += paragraphToHTML('<strong>' + paragraph + '</strong>', "paragraph lead");
                } else {
                    // Check if the paragraph contains a year in the format 'YYYY' or the word 'century'
                    if (paragraph.includes("said") || paragraph.includes(' "') || paragraph.includes('". ') || paragraph.includes(" '") || paragraph.includes("'. ") || paragraph.includes('", ')) {
                        htmlOutput += paragraphToHTML(paragraph, "paragraph blockquote");
                    } else {
                        if (paragraph.match(/[1-2][0-9]{3}/) !== null || paragraph.includes("century")) {
                            htmlOutput += paragraphToHTML('<em><strong>' + paragraph + '</strong></em>', "paragraph historical-fact");
                        } else {
                            // Check if the paragraph contains the word 'said' or quotes to determine if it is a quote
                            if (paragraph.includes("said") || paragraph.includes(' "') || paragraph.includes('". ') || paragraph.includes(" '") || paragraph.includes("'. ") || paragraph.includes('", ')) {
                                htmlOutput += paragraphToHTML(paragraph, "paragraph blockquote");
                            } else {
                                // Check for analogies in the paragraph
                                if (paragraph.includes("magine") || paragraph.includes("onsider") || paragraph.includes('yourself')) {
                                    htmlOutput += paragraphToHTML('<em><strong>' + paragraph + '</strong></em>', "paragraph");
                                } else {
                                    // Check if at the end of the paragraph there is a ':'
                                    if (paragraph.endsWith(":")) {
                                        htmlOutput += paragraphToHTML('<strong>' + paragraph + '</strong', "paragraph") + '</strong>';
                                    } else {
                                        htmlOutput += paragraphToHTML(paragraph, "paragraph");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    });
    return htmlOutput;
}

function createSlugFromText(text) {
    return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
}

// We create the article content HTML paragraphs
function paragraphToHTML( paragraph, classes ) {
    const classesString = classes ? ' class="' + classes + '"' : '';
    return '<p' + classesString +'>' + paragraph + '</p>';
}

function createLinkFromAnchor(anchor, targetSlug) {
    return '<a href="/' + targetSlug + '/' + createSlugFromText(anchor) +'">' + anchor + '</a>';
}
