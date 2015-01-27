<?php
namespace Ecedi\Donate\CmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ecedi\Donate\CoreBundle\Entity\Customer;

class PushCampaignMonitorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:cm:push')
            ->setDescription('Command for enroll new customers with optin to campaign monitor')
            ->setHelp(<<<EOF
The <info>donate:cm:push</info> read the database for new customers with optin. For each one, call webservice to enroll.

<info>php app/console donate:cm:push</info>
EOF
            );
    }
    /**
    * Exécution de la commande
    *
    * @param InputInterface $input
    * @param OutputInterface $output
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $apiKey = $container->getParameter('donate_cm.api_key');
        $listId = $container->getParameter('donate_cm.list_id');

        if ((false != $apiKey) && (false != $listId)) {
            $output->writeln("Will push to Campaign Monitor API key: <info>{$apiKey}</info> and List Id: <info>{$listId}</info>");

            $lotImportNbMax = $container->getParameter('donate_cm.lot_import_nb_max');
            $customFieldsDefinitions = $container->getParameter('donate_cm.custom_fields');
            $qCustomersWithOptinIterableResult = $this->nextCustomers($lotImportNbMax);

            $subscribers = array();
            foreach ($qCustomersWithOptinIterableResult as $row) {
                $c = $row[0];
                $customFields = array();
                if (!empty($customFieldsDefinitions)) {
                    foreach ($customFieldsDefinitions as $customer_getter => $customFieldDefinition) {
                        $customField = array('Key' => $customFieldDefinition['cm_custom_field_name']);
                        if (empty($customFieldDefinition['options'])) {
                            $customField['Value'] = $c->$customer_getter();
                            $customFields[] = $customField;
                        } else {
                            $value = $c->$customer_getter();
                            if (array_key_exists($value, $customFieldDefinition['options'])) {
                                $customField['Value'] = $customFieldDefinition['options'][$value];
                                $customFields[] = $customField;
                            }
                        }
                    }
                }
                $subscribers[] = array('EmailAddress' => $c->getEmail(),
                                    'Name' => $c->getLastName(),
                                    'CustomFields' => $customFields,
                                    );
                $c->setOptinSynchronized(1);
                $em->persist($c);
            }
            $msgResult = '';
            $em = $container->get('doctrine')->getManager();
            try {
                $result = $this->subscribe($apiKey, $listId, $subscribers);
                $response = $result->response;
                if ($result->was_successful()) {
                    $output->writeln("Import OK");
                    $output->writeln("TotalUniqueEmailsSubmitted: <info>{$response->TotalUniqueEmailsSubmitted}</info>");
                    $output->writeln("TotalExistingSubscribers: <info>{$response->TotalExistingSubscribers}</info>");
                    $output->writeln("TotalNewSubscribers: <info>{$response->TotalNewSubscribers}</info>");
                    $em->flush();
                } else {
                    $output->writeln("<error>Inscription error</error>");
                    if (is_object($response)) {
                        $output->writeln("Code: <info>{$response->Code}</info>");
                        $output->writeln("Message: <info>{$response->Message}</info>");
                    } else {
                        $output->writeln('Result: <info>'.print_r($result, 1).'</info>');
                    }
                }
            } catch (Exception $e) {
                $msgException = "\nException : ".$e->getMessage()."\ngetTraceAsString():\n".$e->getTraceAsString();
                $output->writeln("<error>Exception</error>");
                $output->writeln("\t".$e->getMessage());
                $output->writeln("\t".$e->getTraceAsString());
            }
        } else {
            $output->writeln('<error>Campaign Monitor URL is not configured. Nothing done</error>');
        }

        return;
    }

    /**
     * find customers created after a specific id
     * @param  Integer   $lastId a customerId
     * @param  integer   $limit  Nombre de résultats max
     * @return \Iterable
     */
    protected function nextCustomers($limit)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $customerRepository = $em->getRepository('DonateCoreBundle:Customer');
        $qCustomersWithOptin = $customerRepository->getCustomersWithOptinQuery($limit);

        return $qCustomersWithOptin->iterate();
    }

    /**
     * Subscribe
     *
     * @see  https://github.com/campaignmonitor/createsend-php
     * @see  http://www.campaignmonitor.com/api/
     * @param  string                 $apiKey
     * @param  string                 $listId
     * @param  array                  $subscribers
     * @return CS_REST_Wrapper_Result
     */
    protected function subscribe($apiKey, $listId, $subscribers)
    {
        $auth = array('api_key' => $apiKey);
        $wrap = new \CS_REST_Subscribers($listId, $auth);
        $result = $wrap->import($subscribers, true);

        return $result;
    }
}
