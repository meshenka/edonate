<?php

namespace Ecedi\Donate\CoreBundle\Analytics;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @see https://raw.github.com/joaolcorreia/Google-Analytics-PHP-cookie-parser/master/class.gaparse.php
 * Google est en pleine mutation coté lib analytics
 * il y a l'ancienne api ga.js en cours de migration
 * Universal qui est la nouvelle lib (analytics.js)
 * mais cette dernière n'utilise plus les utm et ne difuse plus les info de campagne dans un cookie >_<
 *
 * @see https://developers.google.com/analytics/devguides/collection/analyticsjs/cookie-usage
 * Bref pas sur que ce code serve réellement
 * @TODO creuser comment faire avec Universal
 */
class GoogleCookieParser
{
    private $utm;

  /**
   * Utm
   *
   * @return Utm Utm
   */
  public function getUtm()
  {
      return $this->utm;
  }

  /**
   * Utm
   *
   * @param Utm $newutm Utm
   */
  public function setUtm(Utm $utm)
  {
      $this->utm = $utm;

      return $this;
  }

    public function utma($value)
    {
        list($domain_hash, $random_id, $time_initial_visit, $time_beginning_previous_visit, $time_beginning_current_visit, $session_counter) = preg_split('[\.]', $value);

        $first_visit = new \DateTime();
        $first_visit->setTimestamp($time_initial_visit);
        $this->getUtm()->setFirstVisit($first_visit);

        $previous_visit = new \DateTime();
        $previous_visit->setTimestamp($time_beginning_previous_visit);
        $this->getUtm()->setPreviousVisit($previous_visit);

        $current_visit_started = new \DateTime();
        $current_visit_started->setTimestamp($time_beginning_current_visit);
        $this->getUtm()->setCurrentVisitStarted($current_visit_started);

        $this->getUtm()->setTimesVisited($session_counter);
    }

    public function utmz($value)
    {
        list($domain_hash, $timestamp, $session_number, $campaign_numer, $campaign_data) = preg_split('[\.]', $value, 5);

    // Parse the campaign data
    $campaign_data = parse_str(strtr($campaign_data, "|", "&"));

        $this->getUtm()->setCampaignSource($utmcsr);
        $this->getUtm()->setCampaignName($utmccn);
        $this->getUtm()->setCampaignMedium($utmcmd);
        if (isset($utmctr)) {
            $this->getUtm()->setCampaignTerm($utmctr);
        }
        if (isset($utmcct)) {
            $this->getUtm()->setCampaignContent($utmcct);
        }
    }

    public function utmb($value)
    {
        list($domain_hash, $pages_viewed, $garbage, $time_beginning_current_session) = preg_split('[\.]', $value);
        $this->getUtm()->setPagesViewed($pages_viewed);
    }

    public function parseCookies(ParameterBag $cookies)
    {
        $this->setUtm(new Utm());
        if ($cookies->has('__utmz')) {
            $this->utmz($cookies->get('__utmz'));
        }
        if ($cookies->has('__utmb')) {
            $this->utmb($cookies->get('__utmb'));
        }
        if ($cookies->has('__utma')) {
            $this->utma($cookies->get('__utma'));
        }

        return $this->getUtm();
    }
}
