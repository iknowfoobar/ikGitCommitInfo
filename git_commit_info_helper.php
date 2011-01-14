<?php

/**
 * Retrieves HEAD information about a local a git repository
 *
 * @param string $repository The path the the repository
 * @return array
 * @todo Remove loop and use regular expression
 * @todo Identify entire date including offset
 * @author Giles Smith
 */
function git_commit_info($repository)
{
  $info = array('last' => '',
                'current' => '',
                'name' => '',
                'email' => '',
                'datetime' => '',
                'message' => '');
  $file = $repository . "/logs/HEAD";
  $file = escapeshellarg($file); // for the security concious (should be everyone!)
  $line = `tail -n 1 $file`;
  
  if(!empty($line))
  {
    $parts = explode(' ', trim($line));
    $i = 0;
    $j = FALSE;
    
    foreach($parts as $part)
    {
      if(!empty($part))
      {
        switch ($i)
        {
          case 0:
            $info['last'] = $part;
            break;
          
          case 1:
            $info['current'] = $part;
            break;
          
          default:
            if(filter_var(str_ireplace('<', '', str_ireplace('>', '', $part)), FILTER_VALIDATE_EMAIL) != FALSE)
            {
              $info['email'] = str_ireplace('<', '', str_ireplace('>', '', $part));
              $j = TRUE;
            }
            else
            {
              if($j == TRUE)
              {
                if(is_numeric($part))
                {
                  if(empty($info['datetime']))
                  {
                    $info['datetime'] = $part;
                  }
                  else
                  {
                    $info['commit'] .= $part . " ";
                  }
                }
                else
                {
                  $info['message'] .= $part . " ";
                }
              }
              else
              {
                $info['name'] .= $part . " ";
              }
            }
        }
      }
      
      $i++;
    }
  }
  
  $info['message'] = substr($info['message'], 5);
  return $info;
}