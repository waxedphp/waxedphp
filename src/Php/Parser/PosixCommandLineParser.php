<?php
namespace Waxedphp\Waxedphp\Php\Parser;


class PosixCommandLineParser
{
    /**
     * Parse the POSIX command line into commands and arguments.
     *
     * @param string $commandLine The command line string.
     * @return array Parsed commands and their arguments.
     */
    public function parse($commandLine)
    {
        $commands = [];
        // Split the command line by pipes first
        $pipeSegments = explode('|', $commandLine);
        
        foreach ($pipeSegments as $segment) {
            $segment = trim($segment);
            
            if (!empty($segment)) {
                $commands[] = $this->parseCommand($segment);
            }
        }
        return $commands;
    }

    function parseBashCommand(string $commandLine)
    {
        // Regular expression to match quoted strings or non-space sequences
        $pattern = '/("[^"]*"|\'[^\']*\'|\S+)/';
        
        preg_match_all($pattern, $commandLine, $matches);
        
        // Flatten the matches array and remove surrounding quotes from quoted strings
        $parsed = array_map(function ($match) {
            if ($match[0] === '"' && substr($match, -1) === '"') {
                return ['value' => substr($match, 1, -1), 'flags' => 2];
            }
            if ($match[0] === '\'' && substr($match, -1) === '\'') {
                return ['value' => substr($match, 1, -1), 'flags' => 1];
            }
            return ['value' => $match, 'flags' => 0];
        }, $matches[0]);
        $parsedPipes = [];$i = 0;
        $parsedPipes[$i] = ['values' => []];
        foreach ($parsed as $part) {
          if ($part['flags']===0) {
            $exp = $this->explode($part['value']);
            if (count($exp) > 1) {
              foreach ($exp as $n => $expAPart) {
                $expPart = $expAPart['value'];
                $expSign = $expAPart['delim'];
                if (trim($expPart)=='') {
                  if ((isset($parsedPipes[$i]))&&(count($parsedPipes[$i]['values'])>0)) {
                    $i++;
                    $parsedPipes[$i] = ['pipe'=>$expSign,'values' => []];
                  }
                } else {
                  if (($n>0)&&(isset($parsedPipes[$i]))&&(count($parsedPipes[$i]['values'])>0)) {
                    $i++;
                    $parsedPipes[$i] = ['pipe'=>$expSign,'values' => []];
                  }                  
                  $parsedPipes[$i]['values'][] = ['value' => trim($expPart), 'flags' => 0];
                }
              }
            } else {
              $parsedPipes[$i]['values'][] = $part;
            }
          } else {
            $parsedPipes[$i]['values'][] = $part;
          }
        }
        
        return $parsedPipes;
    }    
    
    function explode(string $commandLine) {
        $matches = preg_split('/[\|<>]+/', $commandLine,-1, PREG_SPLIT_OFFSET_CAPTURE);
        $results = [];
        foreach($matches as $k => $v){
          if ($v[1]!=0) {
            //$matches[$k][2] = $commandLine[$v[1]-1];
            $results[] = ['value'=>$v[0], 'delim'=>$commandLine[$v[1]-1]];
          } else {
            $results[] = ['value'=>$v[0], 'delim'=>$commandLine[0]];
          }
        }
        return $results;
    }
    
    /**
     * Parse an individual command segment.
     *
     * @param string $segment The command segment.
     * @return array Command and its arguments.
     */
    private function parseCommand($segment)
    {
        // Regex to split by space while respecting quoted strings
        $pattern = '/\s+(?=([^"]*"[^"]*")*[^"]*$)/';
        $parts = preg_split($pattern, $segment);
        
        $command = [];
        foreach ($parts as $part) {
            // Remove surrounding quotes
            $part = trim($part, '"');
            if (strlen($part) > 0) {
                $command[] = $part;
            }
        }
        return [
            'command' => $command[0],
            'arguments' => array_slice($command, 1)
        ];
    }
}

