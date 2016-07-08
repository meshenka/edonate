<?php

namespace Ecedi\Donate\CoreBundle\Tests\Analytics;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Ecedi\Donate\CoreBundle\Analytics\GoogleCookieParser;
use Ecedi\Donate\CoreBundle\Analytics\Utm;
use Symfony\Component\HttpFoundation\ParameterBag;

class GoogleCookieParserTest extends WebTestCase
{
    public function testParseUtmzString()
    {
        $utmz = '1.1386025859.5.5.utmcsr=apis.google.com|utmccn=(referral)|utmcmd=referral|utmcct=/u/0/wm/4/_/widget/render/comments';

        $parser = new GoogleCookieParser();
        $parser->setUtm(new Utm());

        $parser->utmz($utmz);

        $utm = $parser->getUtm();

        $this->assertEquals('apis.google.com', $utm->getCampaignSource(), 'Campaign Source');
        $this->assertEquals('(referral)', $utm->getCampaignName(), 'Campaign Name');
        $this->assertEquals('referral', $utm->getCampaignMedium(), 'Campaign Medium');
        $this->assertEquals('/u/0/wm/4/_/widget/render/comments', $utm->getCampaignContent(), 'Campaign Content');
        $this->assertNull($utm->getCampaignTerm(), 'Campaign Term');
    }

    public function testParseCookies()
    {
        $cookies = new ParameterBag();

        $cookies->set('__utmz', '1.1386025859.5.5.utmcsr=apis.google.com|utmccn=(referral)|utmcmd=referral|utmcct=/u/0/wm/4/_/widget/render/comments');

        $cookies->set('__utma', '187139004.1145570274.1387294774.1387294774.1387294774.1');
        $cookies->set('__utmb', '187139004.1.10.1387294774');

        $parser = new GoogleCookieParser();

        $utm = $parser->parseCookies($cookies);

        $this->assertEquals('apis.google.com', $utm->getCampaignSource(), 'Campaign Source');
        $this->assertEquals('(referral)', $utm->getCampaignName(), 'Campaign Name');
        $this->assertEquals('referral', $utm->getCampaignMedium(), 'Campaign Medium');
        $this->assertEquals('/u/0/wm/4/_/widget/render/comments', $utm->getCampaignContent(), 'Campaign Content');
        $this->assertNull($utm->getCampaignTerm(), 'Campaign Term');

        $this->assertEquals('1', $utm->getTimesVisited(), 'Times visited');
        $this->assertEquals('1', $utm->getPagesViewed(), 'Page Viewed');

        $this->assertEquals('2013-12-17 16:39:34', $utm->getFirstVisit()->format('Y-m-d H:i:s'), 'First visit');
        $this->assertEquals('2013-12-17 16:39:34', $utm->getPreviousVisit()->format('Y-m-d H:i:s'), 'Previous visit');
        $this->assertEquals('2013-12-17 16:39:34', $utm->getCurrentVisitStarted()->format('Y-m-d H:i:s'), 'Previous visit');
    }
}
