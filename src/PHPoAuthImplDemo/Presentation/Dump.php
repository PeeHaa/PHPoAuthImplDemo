<?php

namespace PHPoAuthImplDemo\Presentation;

class Dump
{
    public function parse($data)
    {
        if (is_object($data)) {
            // @todo
        } elseif (is_resource($data)) {
            // @todo
        } elseif (is_array($data)) {
            return $this->parseArray($data);
        } else {
            return $this->parseScalar($data);
        }
    }

    private function parseScalar($data)
    {
        if (is_string($data)) {
            return 'string(' . strlen($data) . ') "' . $data . '"' . "\n";
        }

        return 'int(' . $data . ')' . "\n";
    }

    private function parseArray(array $data)
    {
        $output = 'array(' . count($data) . ') {' . "\n";

        foreach ($data as $key => $value) {
            $output .= '    [' . $key . '] => ' . $this->parse($value) . "\n";
        }

        $output .= '}' . "\n";

        return $output;
    }
}
