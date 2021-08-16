<?php

namespace App\Console\Commands\Billing;

use App\Aggregates\Billing\AWSBillingReportAggregate;
use App\Models\AWSBilling\AwsBilling;
use App\Models\Data\Reports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TriggerBillingImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:import {--c= : cycle (YYYYMM) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the job chain that imports AWS Billing and Normalized the data';

    protected AwsBilling $billing;
    protected Reports $reports;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AwsBilling $billing, Reports $reports)
    {
        parent::__construct();
        $this->billing = $billing;
        $this->reports = $reports;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $config = $this->getConfig();
        $this->reports = $this->createReport($config);
        $aggy = AWSBillingReportAggregate::retrieve($this->reports->id);

        $result = $this->createOrTruncate($config['table_name']);

        $aggy = ($result == 'created')
            ? $aggy->logTableCreated($this->reports->misc['created'])
            : $aggy->logTableTruncated($this->reports->misc['truncated']);


        $this->executeImportQuery($config['folder_name'], $config['table_name'], $config['table_date']);
        $aggy->logTableImported($this->reports->misc['imported'])
            ->persist();

        $this->warn('Completed the table import! Queueing up processing jobs! Peace out!');

    }

    private function createReport(array $config): Reports
    {
        $payload = [
            'report' => 'AWS Billing Report',
            'time_started' => date('Y-m-d H:i:s'),
        ];

        $report = $this->reports->firstOrCreate($payload);
        $report->misc = $config;
        $report->save();

        return $report;
    }

    private function getConfig() : array
    {
        $table_date = $this->option('c') ?? date('Ym');
        $table_date_next_month = date('Ym', strtotime( "+1MONTH", strtotime($table_date)));
        return [
            'table_date' => $table_date,
            'table_name' => 'awsbilling'.$table_date,
            'folder_name' => "reportd/redshift/BillingReportRedshift/{$table_date}01-{$table_date_next_month}01"
        ];
    }

    private function createOrTruncate(string $table_name) : string
    {
        if(!Schema::connection('aws-billing')->hasTable($table_name))
        {
            $action = 'created';
            $this->warn("We need to create {$table_name}!");
            $query = 'create table AWSBilling202108(identity_LineItemId VARCHAR(512), identity_TimeInterval VARCHAR(512), bill_InvoiceId VARCHAR(512), bill_BillingEntity VARCHAR(512), bill_BillType VARCHAR(512), bill_PayerAccountId VARCHAR(512), bill_BillingPeriodStartDate VARCHAR(512), bill_BillingPeriodEndDate VARCHAR(512), lineItem_UsageAccountId VARCHAR(512), lineItem_LineItemType VARCHAR(512), lineItem_UsageStartDate VARCHAR(512), lineItem_UsageEndDate VARCHAR(512), lineItem_ProductCode VARCHAR(512), lineItem_UsageType VARCHAR(512), lineItem_Operation VARCHAR(512), lineItem_AvailabilityZone VARCHAR(512), lineItem_ResourceId VARCHAR(512), lineItem_UsageAmount VARCHAR(512), lineItem_NormalizationFactor VARCHAR(512), lineItem_NormalizedUsageAmount VARCHAR(512), lineItem_CurrencyCode VARCHAR(512), lineItem_UnblendedRate VARCHAR(512), lineItem_UnblendedCost VARCHAR(512), lineItem_BlendedRate VARCHAR(512), lineItem_BlendedCost VARCHAR(512), lineItem_LineItemDescription VARCHAR(512), lineItem_TaxType VARCHAR(512), lineItem_LegalEntity VARCHAR(512), product_ProductName VARCHAR(512), product_availability VARCHAR(512), product_availabilityZone VARCHAR(512), product_cacheEngine VARCHAR(512), product_capacitystatus VARCHAR(512), product_classicnetworkingsupport VARCHAR(512), product_clockSpeed VARCHAR(512), product_currentGeneration VARCHAR(512), product_databaseEngine VARCHAR(512), product_dedicatedEbsThroughput VARCHAR(512), product_deploymentOption VARCHAR(512), product_description VARCHAR(512), product_durability VARCHAR(512), product_ecu VARCHAR(512), product_engineCode VARCHAR(512), product_enhancedNetworkingSupported VARCHAR(512), product_fromLocation VARCHAR(512), product_fromLocationType VARCHAR(512), product_group VARCHAR(512), product_groupDescription VARCHAR(512), product_instanceFamily VARCHAR(512), product_instanceType VARCHAR(512), product_instanceTypeFamily VARCHAR(512), product_intelAvx2Available VARCHAR(512), product_intelAvxAvailable VARCHAR(512), product_intelTurboAvailable VARCHAR(512), product_io VARCHAR(512), product_licenseModel VARCHAR(512), product_location VARCHAR(512), product_locationType VARCHAR(512), product_logsDestination VARCHAR(512), product_marketoption VARCHAR(512), product_maxIopsBurstPerformance VARCHAR(512), product_maxIopsvolume VARCHAR(512), product_maxThroughputvolume VARCHAR(512), product_maxVolumeSize VARCHAR(512), product_memory VARCHAR(512), product_messageDeliveryFrequency VARCHAR(512), product_messageDeliveryOrder VARCHAR(512), product_minVolumeSize VARCHAR(512), product_networkPerformance VARCHAR(512), product_normalizationSizeFactor VARCHAR(512), product_operatingSystem VARCHAR(512), product_operation VARCHAR(512), product_physicalProcessor VARCHAR(512), product_platostoragetype VARCHAR(512), product_platousagetype VARCHAR(512), product_platovolumetype VARCHAR(512), product_preInstalledSw VARCHAR(512), product_processorArchitecture VARCHAR(512), product_processorFeatures VARCHAR(512), product_productFamily VARCHAR(512), product_queueType VARCHAR(512), product_region VARCHAR(512), product_requestDescription VARCHAR(512), product_requestType VARCHAR(512), product_routingTarget VARCHAR(512), product_routingType VARCHAR(512), product_servicecode VARCHAR(512), product_servicename VARCHAR(512), product_sku VARCHAR(512), product_storage VARCHAR(512), product_storageClass VARCHAR(512), product_storageMedia VARCHAR(512), product_storageType VARCHAR(512), product_tenancy VARCHAR(512), product_toLocation VARCHAR(512), product_toLocationType VARCHAR(512), product_transferType VARCHAR(512), product_usageFamily VARCHAR(512), product_usagetype VARCHAR(512), product_vcpu VARCHAR(512), product_version VARCHAR(512), product_volumeApiName VARCHAR(512), product_volumeType VARCHAR(512), product_vpcnetworkingsupport VARCHAR(512), pricing_RateCode VARCHAR(512), pricing_RateId VARCHAR(512), pricing_currency VARCHAR(512), pricing_publicOnDemandCost VARCHAR(512), pricing_publicOnDemandRate VARCHAR(512), pricing_term VARCHAR(512), pricing_unit VARCHAR(512), reservation_AmortizedUpfrontCostForUsage VARCHAR(512), reservation_AmortizedUpfrontFeeForBillingPeriod VARCHAR(512), reservation_EffectiveCost VARCHAR(512), reservation_EndTime VARCHAR(512), reservation_ModificationStatus VARCHAR(512), reservation_NormalizedUnitsPerReservation VARCHAR(512), reservation_NumberOfReservations VARCHAR(512), reservation_RecurringFeeForUsage VARCHAR(512), reservation_StartTime VARCHAR(512), reservation_SubscriptionId VARCHAR(512), reservation_TotalReservedNormalizedUnits VARCHAR(512), reservation_TotalReservedUnits VARCHAR(512), reservation_UnitsPerReservation VARCHAR(512), reservation_UnusedAmortizedUpfrontFeeForBillingPeriod VARCHAR(512), reservation_UnusedNormalizedUnitQuantity VARCHAR(512), reservation_UnusedQuantity VARCHAR(512), reservation_UnusedRecurringFee VARCHAR(512), reservation_UpfrontValue VARCHAR(512), savingsPlan_TotalCommitmentToDate VARCHAR(512), savingsPlan_SavingsPlanARN VARCHAR(512), savingsPlan_SavingsPlanRate VARCHAR(512), savingsPlan_UsedCommitment VARCHAR(512), savingsPlan_SavingsPlanEffectiveCost VARCHAR(512), savingsPlan_AmortizedUpfrontCommitmentForBillingPeriod VARCHAR(512), savingsPlan_RecurringCommitmentForBillingPeriod VARCHAR(512));';
            DB::connection('aws-billing')->select($query);
            $this->warn("{$table_name} created!");

            $misc = $this->reports->misc;
            $misc['created'] =  date('Y-m-d H:i:s');
            $misc['truncated'] = false;
            $this->reports->misc = $misc;
            $this->reports->save();
        }
        else
        {
            $action = 'truncated';
            $this->warn('Woot! Skipping table creation! Truncating instead ;p');
            $query = 'TRUNCATE '.$table_name;
            DB::connection('aws-billing')->select($query);

            $misc = $this->reports->misc;
            $misc['created'] = false;
            $misc['truncated'] = date('Y-m-d H:i:s');
            $this->reports->misc = $misc;
            $this->reports->save();
        }

        return $action;
    }

    private function executeImportQuery(string $folder_name, string $table_name, string $table_date) : bool
    {
        $this->info('Remote populating...hang tight...');
        $query = "copy {$table_name} from 's3://{$folder_name}/BillingReportRedshift-RedshiftManifest.json' credentials
            'aws_iam_role=arn:aws:iam::809874100651:role/CustomAWSServiceRoleForS3AndRedshift' region 'us-east-1'
                GZIP CSV IGNOREHEADER 1 TIMEFORMAT 'auto' manifest;
        ";

        DB::connection('aws-billing')->select($query);

        $this->billing->changeTable($table_date);
        $count = $this->billing->count();
        $valid = ($count > 0);

        if($valid)
        {
            $this->warn('Table seeded!');
        }
        else
        {
            $this->error('Table NOT Seeded ):');
        }

        $misc = $this->reports->misc;
        $misc['imported'] = date('Y-m-d H:i:s');
        $this->reports->misc = $misc;
        $this->reports->save();

        return $valid;
    }
}
