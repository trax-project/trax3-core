<?php

namespace Trax\Framework\Xapi\Helpers;

class Multipart
{
    /**
     * Return the parts of a multipart content.
     *
     * @param  string  $content
     * @param  string|false  $boundary
     * @return  array
     */
    public static function parts(string $content, $boundary = false)
    {
        if (!$boundary) {
            return [];
        }

        $res = [];
        $crlf = "\r\n";
        $parts = explode('--'.$boundary.$crlf, $content);
        array_shift($parts);
        foreach ($parts as $part) {
            //
            // Parameters.
            $params = [];
            $sub = explode($crlf.$crlf, $part);
            if (count($sub) < 2) {
                continue;
            }
            $paramLines = explode($crlf, array_shift($sub));
            foreach ($paramLines as $line) {
                $split = explode(':', $line);
                if (count($split) < 2) {
                    continue;
                }
                $params[trim($split[0])] = trim($split[1]);
            }
            
            // Content.
            $content = implode($crlf.$crlf, $sub);
            $content = trim(str_replace($crlf.'--'.$boundary.'--', '', $content));
            
            // Result.
            $partRes = (object)array();
            if (isset($params['Content-Transfer-Encoding'])) {
                $partRes->encoding = $params['Content-Transfer-Encoding'];
            }
            if (isset($params['Content-Length'])) {
                $partRes->length = $params['Content-Length'];
            }
            if (isset($params['Content-Type'])) {
                $partRes->contentType = $params['Content-Type'];
            }
            if (isset($params['X-Experience-API-Hash'])) {
                $partRes->sha2 = $params['X-Experience-API-Hash'];
            }
            $partRes->content = $content;

            if (isset($partRes->sha2)) {
                $res[$partRes->sha2] = $partRes;
            } else {
                $res[] = $partRes;
            }
        }
        return $res;
    }

    /**
     * Return the multipart boundary given the multipart content-type.
     *
     * @param  string  $contentType;
     * @return  string|false
     */
    public static function boundary(string $contentType): string
    {
        $parts = explode("boundary=", $contentType);
        if (count($parts) == 2) {
            $boundary = trim($parts[1], ' "');
            if (!empty($boundary)) {
                return $boundary;
            }
        }
        return false;
    }

    /**
     * Return a multipart content and boundary.
     *
     * @param  array  $parts
     * @param  bool  $autoComplete
     * @return object
     */
    public static function contentAndBoundary(array $parts, bool $autoComplete = true): object
    {
        // Generate a boundary.
        $boundary = md5(rand());
    
        // Content.
        $crlf = "\r\n";
        $content = '';
        foreach ($parts as $part) {
            $content .= self::contentPart($part, $boundary, $autoComplete);
        }
        $content .= $crlf.'--'.$boundary.'--'.$crlf;

        return (object)['content' => $content, 'boundary' => $boundary];
    }
    
    /**
     * Return a part of a multipart.
     *
     * @param  object  $part
     * @param  string  $boundary
     * @param  bool  $autoComplete
     * @return  string
     */
    protected static function contentPart(object $part, string $boundary, bool $autoComplete = true): string
    {
        $crlf = "\r\n";
        $content = $crlf.'--'.$boundary.$crlf;
  
        // Content type.
        if (!isset($part->contentType) && $autoComplete) {
            $content .= 'Content-Type:application/json' . $crlf;
        } elseif (isset($part->contentType)) {
            $content .= 'Content-Type:' . $part->contentType . $crlf;
        }

        // Content length.
        if (!isset($part->length) && $autoComplete) {
            $content .= 'Content-Length:' . mb_strlen($part->content, '8bit') . $crlf;
        } elseif (isset($part->length)) {
            $content .= 'Content-Length:' . $part->length . $crlf;
        }
        
        // Encoding.
        $encoding =  isset($part->encoding) ? $part->encoding : 'binary';
        $content .= 'Content-Transfer-Encoding:'.$encoding.$crlf;
        
        // Hash.
        $hash =  isset($part->sha2) ? $part->sha2 : hash('sha256', $part->content);
        $content .= 'X-Experience-API-Hash:'.$hash.$crlf;
        
        // Content.
        $content .= $crlf.$part->content;
        return $content;
    }
}
