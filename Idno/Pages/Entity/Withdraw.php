<?php

    /**
     * Withdraw syndication
     */

    namespace Idno\Pages\Entity {

        class Withdraw extends \Idno\Common\Page
        {

            // Handle GET requests to the homepage

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $this->setResponse(404);
                    echo \Idno\Core\site()->template()->__(['body' => \Idno\Core\site()->template()->draw('404'), 'title' => 'Not found'])->drawPage();
                    exit;
                }

                if (!$object->canEdit()) {
                    $this->setResponse(403);
                    echo \Idno\Core\site()->template()->__(['body' => \Idno\Core\site()->template()->draw('403'), 'title' => 'Permission denied'])->drawPage();
                    exit;
                }

                $object->unsyndicate();

                \Idno\Core\site()->session()->addMessage("We removed copies on all the syndicated sites.");

                $this->forward($object->getURL());

            }

        }

    }