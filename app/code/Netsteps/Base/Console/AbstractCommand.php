<?php
/**
 * AbstractCommand
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


abstract class AbstractCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * Command name
     */
    protected $_commandName;

    /**
     * Command description
     */
    protected $_commandDescription;

    /**
     * Command options params
     * key => param name
     * value => array(
     *      'label' => 'Param Label',
     *      'required' => one of: InputOption::VALUE_NONE , InputOption::VALUE_REQUIRED, InputOption::VALUE_OPTIONAL, InputOption::VALUE_IS_ARRAY
     * );
     */
    protected $_options = [];

    /**
     * @var ProgressBar
     */
    protected $_progressBar;

    /**
     * @var OutputInterface
     */
    protected $_output;

    /**
     * @var InputInterface
     */
    protected $_input;

    /**
     * Configure command name, description and options that are available
     */
    protected function configure()
    {
        $options = [];

        foreach ($this->_options as $optionKey => $optionConfig) {
            $required = isset($optionConfig['required']) ? $optionConfig['required'] : InputOption::VALUE_OPTIONAL;
            $label = isset($optionConfig['label']) ? $optionConfig['label'] : ucwords($optionKey);

            $options[] = new InputOption($optionKey, null, $required, $label);
        }

        $this->setName($this->_commandName)
            ->setDescription($this->_commandDescription);

        if (!empty($options)) {
            $this->setDefinition($options);
        }
        parent::configure();
    }

    /**
     * Command staring point
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_output = $output;
        $this->_input = $input;
        $this->_execute();
    }

    /**
     * Initialize progress bar
     *
     * @param int $max
     * @param null $format
     */
    protected function initProgressBar($max, $format = null){
        if ($this->_progressBar){
            $this->_progressBar->finish();
            $this->_progressBar = null;
        }

        if ($this->_output){
            $this->_progressBar = new ProgressBar($this->_output, $max);
            if ($format){
                $this->_progressBar->setFormat($format);
            }
            $this->_progressBar->start();
        }
    }

    /**
     * Advance progress bar
     */
    protected function advanceProgressBar(){
        if ($this->_progressBar){
            $this->_progressBar->advance();
        }
    }

    /**
     * Finish progress bar
     */
    protected function finishProgressBar(){
        if ($this->_progressBar){
            $this->_progressBar->finish();
            $this->_output->writeln('');
            $this->_progressBar = null;
        }
    }

    /**
     * Render table
     *
     * @param array $headers
     * @param array $data
     */
    protected function renderTable(array $headers, array $data){
        if (empty($headers)){
            $this->_output->writeln('<error>Table headers are empty</error>');
            return;
        }

        $table = new Table($this->_output);
        $table->setHeaders($headers);

        foreach ($data as $datum){
            $table->addRow($datum);
        }

        $table->render();
    }

    /**
     * Command starting point
     *
     * @return void
     */
    abstract protected function _execute();
}
