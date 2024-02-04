<?php declare(strict_types=1);

/**
 * Trait PhpDocs
 *
 * This trait provides methods for updating test results and index files.
 */
trait PhpDocs
{
    public bool $enableUpdate = !false;

    /**
     * Updates the test result.
     *
     * @param string $path The path of the test.
     * @param string $method The method of the test.
     * @param mixed $result The result of the test.
     * @param bool $toJson Whether to convert the result to JSON.
     */
    public function updateTestResult(string $path, string $method, mixed $result, bool $toJson = true)
    {
        if (!$this->enableUpdate) {
            return;
        }

        $this->updateIndexRst($path);

        list($testClass, $methodName) = explode('::', $method);

        $parts = pathinfo($path);
        $directories = explode(DIRECTORY_SEPARATOR, $parts['dirname']);
        $folder = $directories[array_search('tests', $directories) + 1];

        $file_path = dirname(__FILE__) . "/../docs/tests/{$folder}/{$testClass}/{$methodName}.rst";

        // Check if directory exists, if not create it
        if (!is_dir(dirname($file_path))) {
            mkdir(dirname($file_path), 0755, true);
        }

        $file = fopen($file_path, "w");

        fwrite($file, ".. _{$testClass}_{$methodName}:\n");

        $title = "{$testClass}::{$methodName}";
        fwrite($file, "\n{$title}\n");
        fwrite($file, str_repeat('=', strlen($title)) . "\n");
        fwrite($file, "\nHere are the results for the test method.\n");
        fwrite($file, "\n.. code-block:: " . ($toJson ? 'json' : 'text') . "\n\n");

        $result = $toJson ? json_encode($result, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_IGNORE) : $result;

        foreach (explode("\n", $result) as $line) {
            fwrite($file, "    " . $line . "\n");
        }

        fclose($file);

        $this->updateProtocolsIndexRst($path, $method);
    }

    /**
     * Updates the index.rst file.
     *
     * @param string $path The path of the test.
     */
    public function updateIndexRst(string $path)
    {
        $parts = pathinfo($path);
        $directories = explode(DIRECTORY_SEPARATOR, $parts['dirname']);
        $folder = $directories[array_search('tests', $directories) + 1];

        $dir = dirname(__FILE__) . "/{$folder}/*Test.php";
        $test_files = glob($dir);

        $file_path = dirname(__FILE__) . "/../docs/tests/{$folder}/index.rst";

        // Check if directory exists, if not create it
        if (!is_dir(dirname($file_path))) {
            mkdir(dirname($file_path), 0755, true);
        }

        $file = fopen($file_path, "w");

        $title = str_replace("_", " ", ucwords($folder)) . ' Tests';
        fwrite($file, ".. _{$folder}_tests:\n");
        fwrite($file, "\n{$title}\n");
        fwrite($file, str_repeat("=", strlen($title)) . "\n");
        fwrite($file, "\nTable of Contents\n");
        fwrite($file, "-----------------\n");

        foreach ($test_files as $test_file) {
            $file_name = pathinfo($test_file, PATHINFO_FILENAME);
            fwrite($file, "\n* :ref:`{$file_name}`\n");
        }

        fclose($file);
    }

    /**
     * Updates the protocols index.rst file.
     *
     * @param string $path The path of the test.
     * @param string $method The method of the test.
     */
    public function updateProtocolsIndexRst(string $path, string $method)
    {
        list($testClass, $methodName) = explode('::', $method);

        $parts = pathinfo($path);
        $directories = explode(DIRECTORY_SEPARATOR, $parts['dirname']);
        $folder = $directories[array_search('tests', $directories) + 1];

        $dir = dirname(__FILE__) . "/../docs/tests/{$folder}/{$testClass}/test*.rst";
        $test_results = glob($dir);

        $file_path = dirname(__FILE__) . "/../docs/tests/{$folder}/{$testClass}/index.rst";

        // Check if directory exists, if not create it
        if (!is_dir(dirname($file_path))) {
            mkdir(dirname($file_path), 0755, true);
        }

        $file = fopen($file_path, "w");
        fwrite($file, ".. _{$testClass}:\n");
        fwrite($file, "\n{$testClass}\n");
        fwrite($file, str_repeat("=", strlen($testClass)) . "\n");
        fwrite($file, "\nTable of Contents\n");
        fwrite($file, "-----------------\n");

        foreach ($test_results as $test_file) {
            $file_name = pathinfo($test_file, PATHINFO_FILENAME);
            fwrite($file, "\n* :ref:`{$testClass}_{$file_name}`\n");
        }

        fclose($file);
    }
}
