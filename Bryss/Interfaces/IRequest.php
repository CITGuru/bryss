<?php

namespace Bryss\Interfaces;

interface IRequest
{
    public function getBody();
    public function getHeader();
    public function json($body, $status);
    public function html($body, $data);
    public function input($key);
    public function header($key);
}