<?php

namespace Source\Models\Faq;

use Source\Core\Model;


class Channel extends Model
{    
    /**
     * __construct
     *
     * Channel Constructor
     */
    public function __construct()
    {
        parent::__construct("faq_channels", ["id"], ["channel", "description"]);
    }
}