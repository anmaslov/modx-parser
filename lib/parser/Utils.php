<?php
/**
 * User: MaslovAN
 * Date: 26.12.2016
 * Time: 11:25
 */

namespace anmaslov\parser;


class Utils
{
    static public function extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}