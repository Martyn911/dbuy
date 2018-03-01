<?php
namespace App\Services;

use App\Entity\Domain;
use App\Entity\Whois;
use Doctrine\ORM\EntityManagerInterface;
use Novutec\WhoisParser;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;


Class WhoisChecker
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var array необходимые функции
     */
    private $neededFunctions = ['exec', 'shell_exec', ];
    /**
     * @var array признаки зарезервированного домена
     */
    private $reservedPatterns = ['Reserved by Registry', 'The registration of this domain is restricted'];
    /**
     * @var array whois просит повторить немного позже
     */
    private $limitPatterns = ['Please maintain at least', 'WHOIS LIMIT EXCEEDED'];
    /**
     * @var array статусы доменов по группам
     */
    private $domainStatuses = [
        Domain::DSTATUS_NOTAVAILABLE => [
            'connect', //.de
            'ok', //Стандартный статус. Устанавливается в случае отсутствия других статусов.
            'clientTransferProhibited', //Статус устанавливаемый Регистратором. Обозначает запрет трансфера.
            'serverDeleteProhibited', //Статус устанавливаемый Реестром. Обозначает запрет удаления доменного объекта.
            'serverTransferProhibited', //Статус устанавливаемый Реестром. Обозначает запрет трансфера.
            'serverUpdateProhibited', //Статус устанавливаемый Реестром. Обозначает запрет на внесение изменений в доменный объект.
            'pendingTransfer', //Статус устанавливается автоматически на период выполнения трансфера домена.
            'inactive', //Устанавливается при отсутствии хостов (NS-в) в домене.
            'clientDeleteProhibited', //Статус устанавливаемый Регистратором. Обозначает запрет трансфера.
            'clientUpdateProhibited', //Статус устанавливаемый Регистратором. Обозначает запрет на внесение изменений в доменный объект.
            'clientRenewProhibited', //Статус устанавливаемый Регистратором. Обозначает запрет на продление доменного имени. Не действует на автоматическое продление домена.
            'clientDeleteProhibited', //Статус устанавливаемый Регистратором. Обозначает запрет удаления доменного объекта.
            'registered',
            'Active', //jp
        ],
        Domain::DSTATUS_HOLD => [
            'clientHold',
            'Frozen',
            'HOLD-SІNCE'
        ],
        Domain::DSTATUS_REDEMPTION => [
            'redemptionPeriod'
        ],
        Domain::DSTATUS_PENDING_DELETE => [
            'pendingDelete'
        ]
    ];

    private $container;

    /**
     * WhoisChecker constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->checkAlailableFunctions();
        $this->container = $container;
    }

    /**
     * @param string $domainName
     * @return whois response
     */
    public function check($domainName)
    {
        $cache = new FilesystemCache();
        $whoisInfo = $cache->get('whois_checker.' . md5($domainName) );
        //$whoisInfo = false;
        if (!$whoisInfo) {
            $whoisParser = new WhoisParser\Parser();
            $whoisParser->setCachePath($this->container->getParameter('kernel.root_dir') . '/../var/data');
            $whoisParser->setCustomConfigFile($this->container->getParameter('kernel.root_dir') . '/../config/whois-checker/whois.ini');
            $whoisParser->setProxyConfigFile($this->container->getParameter('kernel.root_dir') . '/../config/whois-checker/proxy.ini');
            $whoisParser->setCustomTemplateNamespace('App\Utils\WhoisParser\Templates');
            $whoisInfo = $whoisParser->lookup($domainName);

            $domainInfo = $whoisParser->getQuery();

            $whoisInfo->rawdata = $this->cutServerIp($whoisInfo->rawdata[0]);

            /**
             * Сохраняем только зарегистрированные домены
             * Информацию про IP пока не храним
             */
            if(!$domainInfo->ip && $domainName == $domainInfo->fqdn && $domainInfo->validHostname){
                $this->save($domainInfo, $whoisInfo);
            }
            $cache->set('whois_checker.' . md5($domainName), $whoisInfo, 3600);
        }

        return $whoisInfo;
    }

    private function save($domainInfo, $whoisInfo){
        $zone = $this->isAvailableZone($domainInfo->tld);
        if(!$zone){
            return false;
            //throw new Exception('Zone not available');
        }

        $domain = $this->em->getRepository('App:Domain')->findOneByName($domainInfo->fqdn);
        if(!$domain){
            $domain = new Domain();

            if(!empty($domainInfo->idnFqdn) && $domainInfo->fqdn != $domainInfo->idnFqdn){
                $domain->setName($domainInfo->idnFqdn);
                $domain->setIdnName($domainInfo->fqdn);
            } else {
                $domain->setName($domainInfo->fqdn);
            }

            $domain->setZone($zone);
            $domain->setStatus(Domain::STATUS_ACTIVE);
        }

        if(!empty($whoisInfo->created) && empty($whoisInfo->changed)){
            $whoisInfo->changed = $whoisInfo->created;
        }

        if($whoisInfo->registered){
            if ( empty($whoisInfo->status) || empty($whoisInfo->created) || empty($whoisInfo->changed) || empty($whoisInfo->expires) ) {
                //throw new Exception('Not found all required params');
                return false;
            }
            if($this->findInStringByPattern($whoisInfo->rawdata, $this->reservedPatterns)) {
                $status = $this->em->getRepository('App:DomainStatus')->findOneByName(Domain::DSTATUS_RESERVED);
                $domain->setDstatus($status);
            } else {
                $status = $this->em->getRepository('App:DomainStatus')->findOneByName($this->getLocalDomainStatus($whoisInfo->status));
                $domain->setDstatus($status);
                $domain->setCreatedAt($this->parseDate($whoisInfo->created));
                $domain->setModifedAt($this->parseDate($whoisInfo->changed));
                $domain->setExpiresAt($this->parseDate($whoisInfo->expires));
            }
        } else {
            $status = $this->em->getRepository('App:DomainStatus')->findOneByName(Domain::DSTATUS_AVAILABLE);
            $domain->setDstatus($status);
        }
        $this->em->persist($domain);

        $whois = $this->em->getRepository('App:Whois')->createQueryBuilder('w')
            ->where('w.domain = :domain and w.type = :type')
            ->setParameters([
                'domain' => $domain->getId(),
                'type' => Whois::TYPE_ACTUAL
            ])
            ->orderBy('w.updatedAt', 'DESC')
            ->setMaxResults( 1 )
            ->getQuery()
            ->getOneOrNullResult();

        //меняем тип старой записи
        if($whois){
            $whois->setType(Whois::TYPE_HISTORY);
            $this->em->persist($whois);;
        }

        //создаем новую запись
        $whois = new Whois();
        $whois->setType(Whois::TYPE_ACTUAL);
        $whois->setDomain($domain);
        $whois->setData($whoisInfo->rawdata);

        $this->em->persist($whois);
        $this->em->flush();
    }

    /**
     * Вырезает ip сервер с которого был whois запрос
     * @param string $data whois info
     * @return string $data whois info
     */
    private function cutServerIp($data)
    {
        return trim(preg_replace('/(.*)(request\ from)(.*)/ui', '', $data));
    }

    /**
     * Разбор и преобразование даты в DateTime
     * @param string $date
     * @return \DateTime
     * @throws \Exception
     */
    //TODO проверить другие даты
    private function parseDate($date)
    {
        if (date_parse($date)['error_count'] == 0) {
            return new \DateTime($date);
        } else {
            throw new \Exception('Bad date: ' . $date);
        }
    }

    /**
     * Проверяет доступность доменной зоны в базе
     * @param  string $zone_name
     * check zone available
     */
    private function isAvailableZone($zone_name)
    {
        return $this->em->getRepository('App:Zone')->findOneBy([
            'name' => $zone_name,
            'status' => Domain::STATUS_ACTIVE
        ]);
    }

    /**
     * Getting whois information
     * @param $domainName
     * @param bool|string $whois_service если не задан берется из локальной базы whois сервиса
     * @return array массив строк полученных от whois сервиса
     * @throws \Exception
     */
    private function getWhoisInfo($domainName, $whois_service = false)
    {
        $whoisPath = exec('command -v whois');
        if(empty($whoisPath)){
            throw new \Exception('Whois not found');
        }
        $cmd = $whoisPath . ' -p 43';
        if ($whois_service) {
            $cmd .= ' -h ' . $whois_service;
        }
        $cmd .= ' ' . $domainName;
        exec($cmd, $output, $return_var);

        return $output;
    }

    /**
     * Проверяем доступность нужных функций
     * @throws \Exception
     */
    private function checkAlailableFunctions()
    {
        foreach ($this->neededFunctions as $function){
            if(!function_exists($function)){
                throw new \Exception('Dissabled function: ' . $function);
            }
        }
    }

    /**
     * Поиск наличия подстроки в строке
     * @param string $string строка для поиска
     * @param array|string $pattern regexp шаблон либо массив признаков
     * @return bool|int
     */
    private function findInStringByPattern($string, $pattern){
        if(is_array($pattern)){
            $pattern = '/(' . str_replace(' ', '\s', implode('|', $pattern)) . ')/ui';
        }

        return preg_match($pattern, trim($string));
    }

    /**
     * Назначает локальный статус исходя из заданных шаблонов $this->domainStatuses
     * @param string|array $status
     * @return int|string
     * @throws \Exception
     */
    private function getLocalDomainStatus($status){
        foreach ($this->domainStatuses as $localStatus => $rules){
            if(is_array($status)){
                foreach ($status as $item){
                    if($this->findInStringByPattern($item, $rules)){
                        return $localStatus;
                    }
                }
            } else {
                if($this->findInStringByPattern($status, $rules)){
                    return $localStatus;
                }
            }
        }
        throw new \Exception('Local status not defined');
    }
}