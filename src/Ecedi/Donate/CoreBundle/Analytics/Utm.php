<?php
namespace Ecedi\Donate\CoreBundle\Analytics;

class Utm
{
    private $campaignSource;            // Campaign Source
    private $campaignName;            // Campaign Name
    private $campaignMedium;            // Campaign Medium
    private $campaignContent;        // Campaign Content
    private $campaignTerm;              // Campaign Term

    private $firstVisit;              // Date of first visit
      private $previousVisit;            // Date of previous visit
      private $currentVisitStarted;    // Current visit started at
      private $timesVisited;            // Times visited
      private $pagesViewed;            // Pages viewed in current session

      /**
       * Pages viewed in current session
       *
       * @return Integer pages viewed
       */
      public function getPagesViewed()
      {
          return $this->pagesViewed;
      }

      /**
       * Pages viewed
       *
       * @param Integer $newpagesViewed Pages viewed
       */
      public function setPagesViewed($pagesViewed)
      {
          $this->pagesViewed = $pagesViewed;

          return $this;
      }
      /**
       * times visited
       *
       * @return Integer times visited
       */
      public function getTimesVisited()
      {
          return $this->timesVisited;
      }

      /**
       * times visited
       *
       * @param Integer $newtimesVisited Integer times visited
       */
      public function setTimesVisited($timesVisited)
      {
          $this->timesVisited = $timesVisited;

          return $this;
      }

      /**
       * current visit started at
       *
       * @return \DateTime current visit started at
       */
      public function getCurrentVisitStarted()
      {
          return $this->currentVisitStarted;
      }

      /**
       *
       *
       * @param \DateTime $newcurrentVisitStarted Current visit started at
       */
      public function setCurrentVisitStarted(\DateTime $currentVisitStarted)
      {
          $this->currentVisitStarted = $currentVisitStarted;

          return $this;
      }

      /**
       * previous visit
       *
       * @return \DateTime previous visit
       */
      public function getPreviousVisit()
      {
          return $this->previousVisit;
      }

      /**
       * previous visit
       *
       * @param \DateTime $newpreviousVisit Previous visit
       */
      public function setPreviousVisit(\DateTime $previousVisit)
      {
          $this->previousVisit = $previousVisit;

          return $this;
      }

      /**
       * first visite
       *
       * @return \DateTime first visit
       */
      public function getFirstVisit()
      {
          return $this->firstVisit;
      }

      /**
       * first visit
       *
       * @param \DateTime $newfirstVisit First visit
       */
      public function setFirstVisit(\DateTime $firstVisit)
      {
          $this->firstVisit = $firstVisit;

          return $this;
      }

      /**
       * source
       *
       * @return string campaign source
       */
      public function getCampaignSource()
      {
          return $this->campaignSource;
      }

      /**
       * source
       *
       * @param String $newcampaignSource Campaign source
       */
      public function setCampaignSource($campaignSource)
      {
          $this->campaignSource = $campaignSource;

          return $this;
      }

      /**
       * name
       *
       * @return string name
       */
      public function getCampaignName()
      {
          return $this->campaignName;
      }

      /**
       * name
       *
       * @param String $newcampaignName Name
       */
      public function setCampaignName($campaignName)
      {
          $this->campaignName = $campaignName;

          return $this;
      }

      /**
       * medium
       *
       * @return string medium
       */
      public function getCampaignMedium()
      {
          return $this->campaignMedium;
      }

      /**
       * medium
       *
       * @param String $newcampaignMedium Medium
       */
      public function setCampaignMedium($campaignMedium)
      {
          $this->campaignMedium = $campaignMedium;

          return $this;
      }

      /**
       * content
       *
       * @return string content
       */
      public function getCampaignContent()
      {
          return $this->campaignContent;
      }

      /**
       * content
       *
       * @param String $newcampaignContent Content
       */
      public function setCampaignContent($campaignContent)
      {
          $this->campaignContent = $campaignContent;

          return $this;
      }

      /**
       * Term
       *
       * @return string term
       */
      public function getCampaignTerm()
      {
          return $this->campaignTerm;
      }

      /**
       * Term
       *
       * @param String $newcampaignTerm Term
       */
      public function setCampaignTerm($campaignTerm)
      {
          $this->campaignTerm = $campaignTerm;

          return $this;
      }
}
