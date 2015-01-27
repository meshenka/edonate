<?php

namespace Ecedi\Donate\CoreBundle\PaymentMethod;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Discovery
{
    private $availableMethods = array();

    private $methods = array();

    private $translator;

    private $config;

    /**
     * config
     *
     * @return array list of enabled methods
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * config
     *
     * @param Array $newconfig List of enabled methods
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * translator
     *
     * @return TranslatorInterface translator service
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * translator
     *
     * @param TranslatorInterface $newtranslator Translator service
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * methods
     *
     * @return array PaymentMethodInterface
     */
    public function getAvailableMethods()
    {
        return $this->availableMethods;
    }

    /**
     * methods
     *
     * @param [type] $newmethods PaymentMethodInterface
     */
    protected function setAvailableMethods($methods)
    {
        $this->availableMethods = $methods;

        return $this;
    }

    public function __construct(TranslatorInterface $translator, $config)
    {
        $this->setTranslator($translator);
        $this->config = $config;
    }

    public function addMethod(PaymentMethodInterface $method)
    {
        $this->availableMethods[$method->getId()] = $this->getTranslator()->trans($method->getName());
        $this->methods[$method->getId()] = $method;

        return $this;
    }

    /**
     * compare available services and enabled one
     * @return array key is serviceId, value is Name
     */
    public function getEnabledMethods()
    {
        $enabledMethods = array();

        foreach ($this->config as $mid) {
            if (isset($this->methods[$mid])) {
                $enabledMethods[$mid] = $this->methods[$mid];
            }
        }

        return $enabledMethods;
    }

    /**
     * @param string a method identifier
     * @return PaymentMethodInterface instance
     * @throws \Exception
     */
    public function getMethod($methodId)
    {
        if (isset($this->methods[$methodId])) {
            return $this->methods[$methodId];
        } else {
            throw new \Exception('Payment Method not found');
        }
    }
}
