<?php

namespace AlibabaCloud\Drds\V20171016;

use AlibabaCloud\Client\Resolver\ApiResolver;

/**
 * @method CreateDrdsAccount createDrdsAccount(array $options = [])
 * @method CreateDrdsDB createDrdsDB(array $options = [])
 * @method CreateDrdsInstance createDrdsInstance(array $options = [])
 * @method CreateReadOnlyAccount createReadOnlyAccount(array $options = [])
 * @method DeleteDrdsDB deleteDrdsDB(array $options = [])
 * @method DeleteFailedDrdsDB deleteFailedDrdsDB(array $options = [])
 * @method DescribeCreateDrdsInstanceStatus describeCreateDrdsInstanceStatus(array $options = [])
 * @method DescribeDrdsDB describeDrdsDB(array $options = [])
 * @method DescribeDrdsDBIpWhiteList describeDrdsDBIpWhiteList(array $options = [])
 * @method DescribeDrdsDBs describeDrdsDBs(array $options = [])
 * @method DescribeDrdsInstance describeDrdsInstance(array $options = [])
 * @method DescribeDrdsInstanceDbMonitor describeDrdsInstanceDbMonitor(array $options = [])
 * @method DescribeDrdsInstanceMonitor describeDrdsInstanceMonitor(array $options = [])
 * @method DescribeDrdsInstanceNetInfoForInner describeDrdsInstanceNetInfoForInner(array $options = [])
 * @method DescribeDrdsInstances describeDrdsInstances(array $options = [])
 * @method DescribeRdsList describeRdsList(array $options = [])
 * @method DescribeReadOnlyAccount describeReadOnlyAccount(array $options = [])
 * @method DescribeRegions describeRegions(array $options = [])
 * @method DescribeShardDbConnectionInfo describeShardDbConnectionInfo(array $options = [])
 * @method DescribeShardDBs describeShardDBs(array $options = [])
 * @method EnableInstance enableInstance(array $options = [])
 * @method ModifyDrdsDBPasswd modifyDrdsDBPasswd(array $options = [])
 * @method ModifyDrdsInstanceDescription modifyDrdsInstanceDescription(array $options = [])
 * @method ModifyDrdsIpWhiteList modifyDrdsIpWhiteList(array $options = [])
 * @method ModifyFullTableScan modifyFullTableScan(array $options = [])
 * @method ModifyRdsReadWeight modifyRdsReadWeight(array $options = [])
 * @method ModifyReadOnlyAccountPassword modifyReadOnlyAccountPassword(array $options = [])
 * @method QueryInstanceInfoByConn queryInstanceInfoByConn(array $options = [])
 * @method RemoveDrdsInstance removeDrdsInstance(array $options = [])
 * @method RemoveReadOnlyAccount removeReadOnlyAccount(array $options = [])
 */
class DrdsApiResolver extends ApiResolver
{
}

class Rpc extends \AlibabaCloud\Client\Resolver\Rpc
{
    /** @var string */
    public $product = 'Drds';

    /** @var string */
    public $version = '2017-10-16';

    /** @var string */
    public $method = 'POST';

    /** @var string */
    public $serviceCode = 'Drds';
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getPassword()
 * @method $this withPassword($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getUserName()
 * @method $this withUserName($value)
 */
class CreateDrdsAccount extends Rpc
{
}

/**
 * @method string getEncode()
 * @method $this withEncode($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getPassword()
 * @method $this withPassword($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getRdsInstances()
 * @method $this withRdsInstances($value)
 */
class CreateDrdsDB extends Rpc
{
}

/**
 * @method string getIsAutoRenew()
 * @method $this withIsAutoRenew($value)
 * @method string getClientToken()
 * @method $this withClientToken($value)
 * @method string getDescription()
 * @method $this withDescription($value)
 * @method string getType()
 * @method $this withType($value)
 * @method string getDuration()
 * @method $this withDuration($value)
 * @method string getIsHa()
 * @method $this withIsHa($value)
 * @method string getInstanceSeries()
 * @method $this withInstanceSeries($value)
 * @method string getQuantity()
 * @method $this withQuantity($value)
 * @method string getSpecification()
 * @method $this withSpecification($value)
 * @method string getVswitchId()
 * @method $this withVswitchId($value)
 * @method string getVpcId()
 * @method $this withVpcId($value)
 * @method string getZoneId()
 * @method $this withZoneId($value)
 * @method string getPayType()
 * @method $this withPayType($value)
 * @method string getPricingCycle()
 * @method $this withPricingCycle($value)
 */
class CreateDrdsInstance extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getPassword()
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class CreateReadOnlyAccount extends Rpc
{

    /**
     * @param string $value
     *
     * @return $this
     */
    public function withPassword($value)
    {
        $this->data['Password'] = $value;
        $this->options['query']['password'] = $value;

        return $this;
    }
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DeleteDrdsDB extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DeleteFailedDrdsDB extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class DescribeCreateDrdsInstanceStatus extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeDrdsDB extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getGroupName()
 * @method $this withGroupName($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeDrdsDBIpWhiteList extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class DescribeDrdsDBs extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class DescribeDrdsInstance extends Rpc
{
}

/**
 * @method string getEndTime()
 * @method $this withEndTime($value)
 * @method string getStartTime()
 * @method $this withStartTime($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getKey()
 * @method $this withKey($value)
 */
class DescribeDrdsInstanceDbMonitor extends Rpc
{
}

/**
 * @method string getEndTime()
 * @method $this withEndTime($value)
 * @method string getStartTime()
 * @method $this withStartTime($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getKey()
 * @method $this withKey($value)
 * @method string getPeriodMultiple()
 * @method $this withPeriodMultiple($value)
 */
class DescribeDrdsInstanceMonitor extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class DescribeDrdsInstanceNetInfoForInner extends Rpc
{
}

/**
 * @method string getType()
 * @method $this withType($value)
 * @method string getTags()
 * @method $this withTags($value)
 */
class DescribeDrdsInstances extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeRdsList extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeReadOnlyAccount extends Rpc
{
}

class DescribeRegions extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getSubDbName()
 * @method $this withSubDbName($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeShardDbConnectionInfo extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class DescribeShardDBs extends Rpc
{
}

/**
 * @method string getClientToken()
 * @method $this withClientToken($value)
 * @method string getEngineVersion()
 * @method $this withEngineVersion($value)
 * @method string getSourceDbInstId()
 * @method $this withSourceDbInstId($value)
 * @method string getRestoreTime()
 * @method $this withRestoreTime($value)
 * @method string getSwitchId()
 * @method $this withSwitchId($value)
 * @method string getBackupId()
 * @method $this withBackupId($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbInstanceClass()
 * @method $this withDbInstanceClass($value)
 * @method string getVpcId()
 * @method $this withVpcId($value)
 * @method string getZoneId()
 * @method $this withZoneId($value)
 */
class EnableInstance extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getNewPasswd()
 * @method $this withNewPasswd($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class ModifyDrdsDBPasswd extends Rpc
{
}

/**
 * @method string getDescription()
 * @method $this withDescription($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class ModifyDrdsInstanceDescription extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getGroupName()
 * @method $this withGroupName($value)
 * @method string getMode()
 * @method $this withMode($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getGroupAttribute()
 * @method $this withGroupAttribute($value)
 * @method string getIpWhiteList()
 * @method $this withIpWhiteList($value)
 */
class ModifyDrdsIpWhiteList extends Rpc
{
}

/**
 * @method string getTableNames()
 * @method $this withTableNames($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getFullTableScan()
 * @method $this withFullTableScan($value)
 */
class ModifyFullTableScan extends Rpc
{
}

/**
 * @method string getWeights()
 * @method $this withWeights($value)
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getInstanceNames()
 * @method $this withInstanceNames($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 */
class ModifyRdsReadWeight extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getNewPasswd()
 * @method $this withNewPasswd($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getAccountName()
 * @method $this withAccountName($value)
 * @method string getOriginPassword()
 * @method $this withOriginPassword($value)
 */
class ModifyReadOnlyAccountPassword extends Rpc
{
}

/**
 * @method string getPort()
 * @method $this withPort($value)
 * @method string getHost()
 * @method $this withHost($value)
 * @method string getUserName()
 * @method $this withUserName($value)
 */
class QueryInstanceInfoByConn extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 */
class RemoveDrdsInstance extends Rpc
{
}

/**
 * @method string getDrdsInstanceId()
 * @method $this withDrdsInstanceId($value)
 * @method string getDbName()
 * @method $this withDbName($value)
 * @method string getAccountName()
 * @method $this withAccountName($value)
 */
class RemoveReadOnlyAccount extends Rpc
{
}
