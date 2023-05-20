<?php

namespace insectdie\PHP\MVC\Middleware;

interface Middleware
{
    function before(): void;
}