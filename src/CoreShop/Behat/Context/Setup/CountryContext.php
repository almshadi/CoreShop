<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Context\FixedCountryContext;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class CountryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var FixedCountryContext
     */
    private $fixedCountryContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $countryFactory
     * @param CountryRepositoryInterface $countryRepository
     * @param FixedCountryContext $fixedCountryContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $countryFactory,
        CountryRepositoryInterface $countryRepository,
        FixedCountryContext $fixedCountryContext
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
        $this->fixedCountryContext = $fixedCountryContext;
    }

    /**
     * @Given /^the site has a country "([^"]+)" with (currency "[^"]+")$/
     */
    public function theSiteHasACountry($name, CurrencyInterface $currency)
    {
        $this->createCountry($name, $currency);
    }

    /**
     * @Then /^the (country "[^"]+") is in (zone "[^"]+")$/
     */
    public function theCountryIsInZone(CountryInterface $country, ZoneInterface $zone)
    {
        $country->setZone($zone);

        $this->saveCountry($country);
    }

    /**
     * @Given /^I am in (country "[^"]+")$/
     */
    public function iAmInCountry(CountryInterface $country)
    {
        $this->fixedCountryContext->setCountry($country);
    }

    /**
     * @param $name
     */
    private function createCountry($name, CurrencyInterface $currency)
    {
        $country = $this->countryRepository->findByName($name, 'en');

        if (!$country) {
            /**
             * @var $country CountryInterface
             */
            $country = $this->countryFactory->createNew();
            $country->setName($name, 'en');
            $country->setIsoCode('AT');
            $country->setActive(true);
            $country->setCurrency($currency);

            $this->saveCountry($country);
        }
    }

    /**
     * @param CountryInterface $country
     */
    private function saveCountry(CountryInterface $country)
    {
        $this->objectManager->persist($country);
        $this->objectManager->flush();
    }
}
