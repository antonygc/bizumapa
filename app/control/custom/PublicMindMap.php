<?php

class PublicMindMap extends TPage
{
    public function __construct()
    {
        parent::__construct();

        // 'scope' é usado no file manager
        TSession::setValue('scope', 'public');

        $vbox = MindMapUtils::getFrameVBox(__CLASS__);

        parent::add($vbox);
    }
}