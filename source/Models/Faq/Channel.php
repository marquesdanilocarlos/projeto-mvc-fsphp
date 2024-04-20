<?php

namespace Source\Models\Faq;

use Source\Core\Model;

class Channel extends Model
{
    public function __construct()
    {
        parent::__construct('faq_channels', ['id'], ['channel', 'description']);
    }

    public function save(): bool
    {
        
    }
}