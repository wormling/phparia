<?php
namespace phparia\Tests\Functional;

use phparia\Client\Phparia;
use \PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Yaml;
use Zend\Log\LoggerInterface;


abstract class PhpariaTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phparia
     */
    protected $client = null;

    /**
     * @var string
     */
    protected $ariAddress;

    /**
     * @var string
     */
    protected $amiAddress;

    /**
     * @var string
     */
    protected $dialString;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function setUp()
    {
        $configFile = __DIR__.'/../../config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $this->ariAddress = $value['tests']['ari_address'];
        $this->amiAddress = $value['tests']['ami_address'];
        $this->dialString = $value['tests']['dial_string'];

        $this->logger = new \Zend\Log\Logger();
        $logWriter = new \Zend\Log\Writer\Stream("php://output");
        $this->logger->addWriter($logWriter);
        $filter = new \Zend\Log\Filter\SuppressFilter(true);
        //$filter = new \Zend\Log\Filter\Priority(\Zend\Log\Logger::NOTICE);
        $logWriter->addFilter($filter);

        $this->client = new Phparia($this->logger);
        $this->client->connect($this->ariAddress, $this->amiAddress);
    }


}
