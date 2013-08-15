#!/usr/bin/env php
<?php
// We are a valid Joomla entry point.
// This is required to load the Joomla Platform import.php file.
define('_JEXEC', 1);

?>
<?php
##*HEADER*##

// Increase error reporting to that any errors are displayed.
// Note, you would not use these settings in production.
error_reporting(- 1);
ini_set('display_errors', true);

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

/**
 * An example command line application class.
 *
 * This application shows how to access command line arguments.
 *
 * @package  ECR_COM_NAME
 */
class ECR_COM_NAME extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * @return  void
     */
    public function doExecute()
    {
        // Print a blank line.
        $this->out();
        $this->out('JOOMLA PLATFORM ARGV EXAMPLE');
        $this->out('============================');
        $this->out();

        // You can look for named command line arguments in the form of:
        // (a) -n value
        // (b) --name=value
        //
        // Try running file like this:
        // $ ./run.php -fa
        // $ ./run.php -f foo
        // $ ./run.php --set=match
        //
        // The values are accessed using the $this->input->get() method.
        // $this->input is an instance of a JInputCli object.

        // This is an example of an option using short args (-).
        $value = $this->input->get('a');
        $this->out(
            sprintf(
                '%25s = %s', 'a',
                var_export($value, true)
            )
        );

        $value = $this->input->get('f');
        $this->out(
            sprintf(
                '%25s = %s', 'f',
                var_export($value, true)
            )
        );

        // This is an example of an option using long args (--).
        $value = $this->input->get('set');
        $this->out(
            sprintf(
                '%25s = %s', 'set',
                var_export($value, true)
            )
        );

        // You can also apply defaults to the command line options.
        $value = $this->input->get('f', 'default');
        $this->out(
            sprintf(
                '%25s = %s', 'f (with default)',
                var_export($value, true)
            )
        );

        // You can also apply input filters found in the JFilterInput class.
        // Try running this file like this:
        // $ ./run.php -f one2

        $value = $this->input->get('f', 0, 'INT');
        $this->out(
            sprintf(
                '%25s = %s', 'f (cast to int)',
                var_export($value, true)
            )
        );

        // Print out all the remaining command line arguments used to run this file.
        if(false == empty($this->input->args))
        {
            $this->out();
            $this->out('These are the remaining arguments passed:');
            $this->out();

            // Unallocated arguments are found in $this->input->args.
            // Try running the file like this:
            // $ ./run.php -f foo bar

            foreach($this->input->args as $arg)
            {
                $this->out($arg);
            }
        }

        // Print a blank line at the end.
        $this->out();
    }
}

// Wrap the execution in a try statement to catch any exceptions thrown anywhere in the script.
try
{
    // Instantiate the application object, passing the class name to JApplicationCli::getInstance
    // and use chaining to execute the application.
    JApplicationCli::getInstance('ECR_COM_NAME')->execute();
}
catch(Exception $e)
{
    // An exception has been caught, just echo the message.
    fwrite(STDOUT, $e->getMessage()."\n");
    exit($e->getCode());
}
