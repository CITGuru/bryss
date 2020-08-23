<?php

namespace Bryss\Interfaces;

interface IResponse
{
    public function send($body, $status);
    public function json($data, $status);
    public function html($body, $status);
}