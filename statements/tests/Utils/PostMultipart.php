<?php

namespace Trax\Statements\Tests\Utils;

trait PostMultipart
{
    protected function postMultipart($uri, string $content, string $boundary)
    {
        $headers = [
            'Content-Type' => 'multipart/mixed; boundary="'.$boundary.'"',
            'Content-Length' => mb_strlen($content, '8bit'),
        ];
        $server = $this->transformHeadersToServerVars($headers);
        $cookies = $this->prepareCookiesForRequest();
        return $this->call('POST', $uri, [], $cookies, [], $server, $content);
    }
}
