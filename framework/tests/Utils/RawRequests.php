<?php

namespace Trax\Framework\Tests\Utils;

trait RawRequests
{
    protected function postRaw(string $url, string $content, array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);
        return $this->call('POST', $url, [], [], [], $server, $content);
    }

    protected function putRaw(string $url, string $content, array $headers)
    {
        $server = $this->transformHeadersToServerVars($headers);
        return $this->call('PUT', $url, [], [], [], $server, $content);
    }
}
