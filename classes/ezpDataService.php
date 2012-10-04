<?php
use ODataProducer\Configuration\EntitySetRights;
require_once 'ODataProducer'.DIRECTORY_SEPARATOR.'IDataService.php';
require_once 'ODataProducer'.DIRECTORY_SEPARATOR.'IRequestHandler.php';
require_once 'ODataProducer'.DIRECTORY_SEPARATOR.'DataService.php';
require_once 'ODataProducer'.DIRECTORY_SEPARATOR.'IServiceProvider.php';
use ODataProducer\Configuration\DataServiceProtocolVersion;
use ODataProducer\Configuration\DataServiceConfiguration;
use ODataProducer\IServiceProvider;
use ODataProducer\DataService;

class ezpDataService extends DataService implements IServiceProvider
{
    private $_Metadata = null;
    private $_QueryProvider = null;

    /**
     * This method is called only once to initialize service-wide policies
     * 
     * @param DataServiceConfiguration &$config Data service configuration object
     * 
     * @return void
     */
    public static function initializeService(DataServiceConfiguration &$config)
    {
    	$ini = eZINI::instance( "odata.ini" );
    	if( $ini->hasVariable( 'Settings', 'EntitySetPageSize' ) )
    	{
    		$config->setEntitySetPageSize('*', (int) $ini->variable( 'Settings', 'EntitySetPageSize' ) );
    	}
    	else
    	{
    		$config->setEntitySetPageSize('*', 10);
    	}
        $config->setEntitySetAccessRule('*', EntitySetRights::ALL);
        $config->setAcceptCountRequests(true);
        $config->setAcceptProjectionRequests(true);
        $config->setMaxDataServiceVersion(DataServiceProtocolVersion::V3);
    }

    /**
     * Get the service like IDataServiceMetadataProvider, IDataServiceQueryProvider,
     * IDataServiceStreamProvider
     * 
     * @param String $serviceType Type of service IDataServiceMetadataProvider, 
     *                            IDataServiceQueryProvider,
     *                            IDataServiceStreamProvider
     * 
     * @see library/ODataProducer/ODataProducer.IServiceProvider::getService()
     * @return object
     */
    public function getService($serviceType)
    {
        if ($serviceType === 'IDataServiceMetadataProvider')
        {
            if (is_null($this->_Metadata))
            {
                $this->_Metadata = ezpMetadata::create();
            }

            return $this->_Metadata;
        }
        else if ($serviceType === 'IDataServiceQueryProvider')
        {
            if (is_null($this->_QueryProvider))
            {
                $this->_QueryProvider = new ezpQueryProvider();
            }

            return $this->_QueryProvider;
        }
        else if ($serviceType === 'IDataServiceStreamProvider')
        {
            return new ezpStreamProvider();
        }

        return null;
    }
}