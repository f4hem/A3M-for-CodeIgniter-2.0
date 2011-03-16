<?php

class Home extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(array('account/authentication'));
    }

    function index()
    {
        if ($this->authentication->is_signed_in())
        {
            echo "Signed in";
        }
        else
        {
            echo "Not signed in!";
        }
    }
}

