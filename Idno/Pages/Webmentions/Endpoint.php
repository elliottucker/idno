<?php

    /**
     * Webmentions endpoint
     */

    namespace Idno\Pages\Webmentions {

        /**
         * Class to serve the webmention endpoint
         */
        class Endpoint extends \Idno\Common\Page
        {

            function getContent() {
                $t = \Idno\Core\site()->template();
                $t->__(['title' => 'Webmention endpoint', 'body' => $t->draw('pages/webmention')])->drawPage();
            }

            function post() {

                parse_str(trim(file_get_contents("php://input")),$vars);

                // Check that both source and target are non-empty
                if (!empty($vars['source']) && !empty($vars['target'])) {
                    $source = urldecode($vars['source']); $target = urldecode($vars['target']);
                    // Get the page handler for target
                    if ($page = \Idno\Core\site()->getPageHandler($target)) {
                        // Check that source exists, parse it for mf2 content,
                        // and ensure that it genuinely mentions this page
                        if ($source_content = \Idno\Core\Webmention::getPageContent($source)) {
                            if (substr_count($source_content,$target)) {
                                $source_mf2 = \Idno\Core\Webmention::parseContent($source_content);
                                // Set source and target information as input variables
                                $page->setInput('source', $source);                 // Source URL
                                $page->setInput('target', $target);                 // Target URL
                                $page->setInput('sourceHTML', $source_content);     // Source HTML
                                $page->setInput('sourceParsed', $source_mf2);      // Source parsed microformats2 content
                                $page->setPermalink();
                                if ($page->webmentionContent()) {
                                    $this->setResponse(202);    // Webmention received a-ok.
                                    exit;
                                }
                            } else error_log('No link from ' . $source . ' to ' . $target);
                        } else error_log('No content from ' . $source);
                    }
                }
                $this->setResponse(400);    // Webmention failed.
            }

        }

    }