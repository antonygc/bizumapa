<?php

class PrivateMindMap extends TPage
{
    public function __construct()
    {
        parent::__construct();

        // 'scope' é usado no file manager
        TSession::setValue('scope', 'private');

        $vbox = MindMapUtils::getFrameVBox(__CLASS__);

        parent::add($vbox);
    }
}