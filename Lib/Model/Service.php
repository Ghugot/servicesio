<?php
/**
 * Model : service accessor
 *
 * PHP Version 5
 *
 * @category Model
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Model;

use Redgem\ServicesIOBundle\Lib\Factory\Input\Factory;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Exception;

/**
 * the Service class drive the transformation of dataflow into
 * Nodes model to be used by controllers
 *
 * @category Model
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Service
{
    /**
     * @var array
     */
    private $config;
    
    /**
     * @var Container
     */
    private static $container;
    
    /**
     * the constructor
     *
     * @param Container $container the Symfony Container
     */
    public function __construct(Container $container)
    {
        $this->config = $container
            ->getParameter('servicesio_models');

        self::$container = $container;
    }

    /**
     * static access to Symfony container
     * to push it into Nodes. Nodes are supposed to be 
     * extendables and able to use Symfony services.
     *
     * @return Container
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Build a Model response from a raw Json
     *
     * @param string $content The raw Json datas
     * @param string $model The model configuration entry you want to use
     *
     * @return Base
     */
    public function build($content, $model = null)
    {
        $aContent = @json_decode($content, true);

        if ($aContent === null) {
            throw new Exception(
                'The json entry is not valid'
            );
        } 

        $config = null;
        if ($model) {
            if (!array_key_exists($model, $this->config)) {
                throw new Exception(
                    sprintf(
                        'You have requested a non existant Model config : %s',
                        $model
                    )
                );
            }
    
            $config = $this->config[$model];
        }
    
        $factory = new Factory(
            $aContent,
            $config
        );
    
        return $factory->get();
    }
}
