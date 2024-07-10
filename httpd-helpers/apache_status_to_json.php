<?php
// URL to the Apache server status page
$url = "http://127.0.0.1/server-status?auto";
$output_file = '/var/log/httpd/apache-status.log';
$metric_prefix = "apache-";

// Stats to collect
$stats_to_collect = array("CurrentTime", "ReqPerSec", "BytesPerSec", "BusyWorkers", "IdleWorkers");

// Fetch the status page content
$status_content = file_get_contents($url);

if ($status_content === FALSE) {
    die("Error fetching the Apache server status.");
}

// Parse the status content
$status_lines = explode("\n", $status_content);
$status_data = array();

foreach ($status_lines as $line) {
    // Split each line by colon and space
    $parts = explode(": ", $line, 2);
    if (count($parts) == 2) {
        $key = trim($parts[0]);

        // Only continue if the $key is a stat we want to collect
        if (array_search($key, $stats_to_collect) === false){
           continue;
        }

        // Add a prefix to the key name
        $key = $metric_prefix . $key;

        $value = trim($parts[1]);

        // Convert numeric values to their respective types and format floats to 1 decimal place
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                $value = number_format((float)$value, 1);
            } else {
                $value = (int)$value;
            }
        }

        // Add to the associative array
        $status_data[$key] = $value;
    }
}

// Convert the associative array to JSON
$json_output = json_encode($status_data);

if ($json_output === FALSE) {
    die("Error encoding the status data to JSON.");
}

// Open the file in append mode and write the JSON output
$file_handle = fopen($output_file, 'a');
if ($file_handle === FALSE) {
    die("Error opening file for appending.");
}

// Append JSON output to the file on a single line
if (fwrite($file_handle, $json_output . "\n") === FALSE) {
    die("Error writing JSON data to the file.");
}

// Close the file handle
fclose($file_handle);
?>
