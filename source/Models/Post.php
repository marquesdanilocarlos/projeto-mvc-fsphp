<?php

namespace Source\Models;

use Source\Core\Model;

class Post extends Model
{
    public function __construct()
    {
        parent::__construct('posts', ['id'], ['title', 'id', 'subtitle', 'content', 'post_status_id']);
    }

    public function find(?string $terms = null, ?string $params = null, string $columns = '*'): Model
    {
        $defatultTerms = 'post_status_id = :post_status_id AND post_at <= NOW()';
        $defaultParams = "post_status_id=" . PostStatus::POSTED;

        if ($terms) {
            $defatultTerms .= " AND {$terms}";
        }

        if ($params) {
            $defaultParams .= "&{$params}";
        }


        return parent::find($defatultTerms, $defaultParams, $columns);
    }

    public function getAuthor(): ?Model
    {
        if (!$this->author) {
            return null;
        }

        return (new User())->findById($this->author);
    }

    public function getCategory(): ?Model
    {
        if (!$this->category) {
            return null;
        }

        return (new Category())->findById($this->author);
    }

    public function findByUri(string $uri, string $columns = '*'): ?self
    {
        $find = $this->find('uri = :uri', "uri={$uri}", $columns);
        return $find->fetch();
    }

    public function save(): bool
    {
        /**
         * Post Update
         */
        if (!empty($this->id)) {
            $postId = $this->id;
            $this->update($this->safe(), 'id = :id', "id={$postId}");

            if ($this->getFail()) {
                $this->message->error("Erro ao atualizar o post");
                return false;
            }

            /**
             * Post Create
             */

            $this->data = $this->findById($postId)->getData();
            return true;
        }
    }
}